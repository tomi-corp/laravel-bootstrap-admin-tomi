<?php

namespace Tests\Feature;

use App\Exceptions\PrivyDigitalIdException;
use App\Http\Controllers\User\PrivyController;
use App\Models\PrivyDigitalIdSession;
use App\Models\User;
use App\Services\PrivyDigitalIdClient;
use App\Services\PrivyDigitalIdService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class PrivyDigitalIdCallbackTest extends TestCase
{
    private array $originalEnvironment = [];

    private int $sessionSequence = 0;

    public function createApplication()
    {
        // Set these before Laravel bootstraps, even when phpunit.xml leaves DB settings unset.
        foreach (['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:'] as $key => $value) {
            $this->originalEnvironment[$key] = [getenv($key), $_ENV[$key] ?? null, $_SERVER[$key] ?? null];
            putenv($key.'='.$value);
            $_ENV[$key] = $_SERVER[$key] = $value;
        }

        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        $app->afterBootstrapping(LoadConfiguration::class, static function ($app) {
            // Override cached application configuration before any provider can resolve DB.
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite', [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
                'foreign_key_constraints' => true,
            ]);
            $app['config']->set('cache.default', 'array');
            $app['config']->set('session.driver', 'array');
            $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('t', 32)));
        });
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        self::createIsolatedSchema();
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            foreach ($this->originalEnvironment as $key => [$process, $environment, $server]) {
                putenv($process === false ? $key : $key.'='.$process);
                if ($environment === null) {
                    unset($_ENV[$key]);
                } else {
                    $_ENV[$key] = $environment;
                }
                if ($server === null) {
                    unset($_SERVER[$key]);
                } else {
                    $_SERVER[$key] = $server;
                }
            }
        }
    }

    public static function createIsolatedSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'dob', 'verified_privy', 'id_privy', 'enterprise_token_privy', 'reason_privy', 'resend_privy'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('privy_digital_id_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            foreach (['nik', 'full_name', 'dob', 'email', 'phone', 'ktp', 'selfie'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['reference_number', 'privy_session_id', 'exchange_code', 'session_url', 'status', 'event', 'privy_id', 'processed_by', 'error_code', 'error_message', 'callback_state', 'decrypted_privy_id', 'decrypted_name', 'decrypted_identity_number', 'decrypted_dob', 'decrypted_email', 'decrypted_phone', 'local_ktp_path', 'local_selfie_path'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['callback_token', 'callback_shared_data', 'decrypted_raw'] as $column) {
                $table->text($column)->nullable();
            }
            foreach (['expires_at', 'exchanged_at', 'exchange_received_at', 'callback_received_at', 'processing_started_at', 'verified_at', 'decrypted_at'] as $column) {
                $table->timestamp($column)->nullable();
            }
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        // The real successful-result path checks for a configured certificate cost.
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->decimal('biaya')->nullable();
        });
    }

    public static function unsuccessfulStatuses(): array
    {
        return [['FAILED'], ['REJECTED'], ['ERROR']];
    }

    /** @dataProvider unsuccessfulStatuses */
    public function test_authenticated_unsuccessful_callback_replaces_every_other_session_only_for_its_owner(string $status): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('privy/synthetic-selfie.jpg', 'synthetic photo');
        $owner = $this->user();
        $otherOwner = $this->user();
        $siblings = [];
        foreach (['created', 'pending', 'failed', 'processing', 'verified', 'identity_mismatch'] as $index => $oldStatus) {
            $siblings[] = $this->createSession($owner, [
                'status' => $oldStatus,
                'expires_at' => $index % 2 === 0 ? now()->subDay() : now()->addDay(),
                'local_selfie_path' => 'privy/synthetic-selfie.jpg',
                'local_ktp_path' => 'privy/synthetic-ktp.jpg',
            ]);
        }
        $current = $this->createSession($owner);
        $unrelated = $this->createSession($otherOwner);
        $this->provider(['session_id' => $current->privy_session_id, 'status' => $status]);

        // A borrower ID supplied outside the signed token cannot change the owner.
        $response = $this->invokeCallback(['id_borrower' => $otherOwner->id, 'state' => 'synthetic-state', 'shared_data' => ['source' => 'synthetic']]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['received']);
        $this->assertFalse($response->getData(true)['privy_verified']);
        $this->assertSame([$current->id, $unrelated->id], PrivyDigitalIdSession::orderBy('id')->pluck('id')->all());
        $this->assertSame(count($siblings) + 2, PrivyDigitalIdSession::withTrashed()->count());
        foreach ($siblings as $sibling) {
            $historical = PrivyDigitalIdSession::withTrashed()->findOrFail($sibling->id);
            $this->assertNotNull($historical->deleted_at);
            $this->assertSame('privy/synthetic-selfie.jpg', $historical->local_selfie_path);
            $this->assertSame('privy/synthetic-ktp.jpg', $historical->local_ktp_path);
        }
        Storage::disk('local')->assertExists('privy/synthetic-selfie.jpg');
        $current->refresh();
        $this->assertSame('failed', $current->status);
        $this->assertNotNull($current->callback_received_at);
        $this->assertSame('synthetic-state', $current->callback_state);
        $this->assertSame(['source' => 'synthetic'], $current->callback_shared_data);
        $this->assertNotSame('synthetic-token', $current->callback_token);
        $this->assertSame('synthetic-token', Crypt::decryptString($current->callback_token));
    }

    public static function identityCases(): array
    {
        return [
            'different NIK' => ['9999999999999999', '1990-01-02', 'identity_mismatch'],
            'different DOB with matching NIK' => ['1111111111111111', '1991-02-03', 'verified'],
            'matching identity without photos' => ['1111111111111111', '1990-01-02', 'verified'],
        ];
    }

    /** @dataProvider identityCases */
    public function test_real_identity_matching_runs_after_sibling_retirement(string $nik, string $dob, string $expected): void
    {
        $owner = $this->user();
        $old = $this->createSession($owner);
        $current = $this->createSession($owner);
        $this->provider([
            'session_id' => $current->privy_session_id,
            'status' => 'VERIFIED',
            'privy_id' => 'SYNTHETIC-PRIVY',
            'my_identity' => 'synthetic-ciphertext',
        ], ['identity_number' => $nik, 'dob' => $dob, 'privy_id' => 'SYNTHETIC-PRIVY']);

        $this->assertSame(200, $this->invokeCallback()->getStatusCode());

        $this->assertNotNull(PrivyDigitalIdSession::withTrashed()->findOrFail($old->id)->deleted_at);
        $current->refresh();
        $this->assertSame($expected, $current->status);
        $this->assertNotNull($current->callback_received_at);
        $this->assertSame($nik, $current->decrypted_identity_number);
        $this->assertNull($current->local_selfie_path);
        $this->assertNull($current->local_ktp_path);
        $this->assertSame($expected === 'verified' ? 'y' : 'n', $owner->fresh()->verified_privy);
    }

    public function test_verified_identity_snapshot_stores_image_file_names_without_paths(): void
    {
        Storage::fake('local');
        $owner = $this->user();
        $session = $this->createSession($owner);
        $this->provider([
            'session_id' => $session->privy_session_id,
            'status' => 'VERIFIED',
            'privy_id' => 'SYNTHETIC-PRIVY',
            'my_identity' => 'synthetic-ciphertext',
            'data' => [
                'ktp' => base64_encode('synthetic ktp'),
                'selfie' => base64_encode('synthetic selfie'),
            ],
        ], [
            'identity_number' => '1111111111111111',
            'name' => 'Synthetic User',
            'dob' => '1990-01-02',
            'email' => 'synthetic@example.com',
            'phone' => '081234567890',
            'privy_id' => 'SYNTHETIC-PRIVY',
        ]);

        $this->assertSame(200, $this->invokeCallback()->getStatusCode());

        $session->refresh();
        $this->assertSame('1111111111111111', $session->nik);
        $this->assertSame('Synthetic User', $session->full_name);
        $this->assertSame('1990-01-02', $session->dob->format('Y-m-d'));
        $this->assertSame('synthetic@example.com', $session->email);
        $this->assertSame('081234567890', $session->phone);
        $this->assertSame(basename($session->local_ktp_path), $session->ktp);
        $this->assertSame(basename($session->local_selfie_path), $session->selfie);
        $this->assertStringNotContainsString('/', $session->ktp);
        $this->assertStringNotContainsString('/', $session->selfie);
    }

    public static function invalidCallbacks(): array
    {
        return [
            'bad signature with request ID' => [null, ['session_id' => 'current'], 401, 'token callback tidak valid'],
            'bad signature without request ID' => [null, [], 401, 'token callback tidak valid'],
            'missing signed ID' => [['status' => 'FAILED'], ['session_id' => 'current'], 400, 'session_id tidak ditemukan pada callback'],
            'non-string signed ID' => [['session_id' => 123], [], 400, 'session_id tidak ditemukan pada callback'],
            'empty signed ID' => [['session_id' => '   '], [], 400, 'session_id tidak ditemukan pada callback'],
            'empty supplied ID' => [['session_id' => 'current'], ['session_id' => ''], 401, 'token callback tidak valid'],
            'null supplied ID' => [['session_id' => 'current'], ['session_id' => null], 401, 'token callback tidak valid'],
            'conflicting request ID' => [['session_id' => 'current', 'status' => 'FAILED'], ['session_id' => 'sibling'], 401, 'token callback tidak valid'],
        ];
    }

    /** @dataProvider invalidCallbacks */
    public function test_invalid_callbacks_leave_all_rows_unchanged(?array $payload, array $input, int $status, string $message): void
    {
        $owner = $this->user();
        $this->createSession($owner, ['privy_session_id' => 'current', 'status' => 'verified', 'callback_state' => 'original']);
        $this->createSession($owner, ['privy_session_id' => 'sibling']);
        $before = DB::table('privy_digital_id_sessions')->orderBy('id')->get()->toJson();
        $this->provider($payload);

        $response = $this->invokeCallback($input + ['state' => 'untrusted', 'exchange_code' => 'untrusted', 'shared_data' => ['untrusted' => true]]);

        $this->assertSame($status, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['status']);
        $this->assertFalse($response->getData(true)['received']);
        $this->assertSame($message, $response->getData(true)['message']);
        $this->assertSame($before, DB::table('privy_digital_id_sessions')->orderBy('id')->get()->toJson());
    }

    public function test_missing_token_leaves_rows_unchanged(): void
    {
        $this->createSession($this->user());
        $before = DB::table('privy_digital_id_sessions')->get()->toJson();
        $client = Mockery::mock(PrivyDigitalIdClient::class);
        $client->shouldNotReceive('verifyToken');
        $this->app->instance(PrivyDigitalIdClient::class, $client);

        $response = $this->invokeCallback(['token' => '']);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame($before, DB::table('privy_digital_id_sessions')->get()->toJson());
    }

    /** @dataProvider tokenlessFailureReasons */
    public function test_tokenless_identity_failure_callback_explains_each_failure(string $reason, string $message): void
    {
        $owner = $this->user();
        $session = $this->createSession($owner);
        $client = Mockery::mock(PrivyDigitalIdClient::class);
        $client->shouldNotReceive('verifyToken');
        $this->app->instance(PrivyDigitalIdClient::class, $client);

        $response = $this->invokeCallback([
            'token' => null,
            'session_id' => $session->privy_session_id,
            'event' => 'identity.'.$reason,
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['status' => true, 'received' => true, 'message' => $message, 'event' => 'identity.'.$reason], $response->getData(true));
        $session->refresh();
        $this->assertSame('failed', $session->status);
        $this->assertSame('identity.'.$reason, $session->event);
        $this->assertSame($reason, $session->error_code);
        $this->assertSame($message, $session->error_message);
        $this->assertNull($session->callback_token);
        $this->assertNotNull($session->callback_received_at);
    }

    public static function tokenlessFailureReasons(): array
    {
        return [
            ['cancelled', 'Verifikasi identitas dibatalkan oleh pengguna.'],
            ['user_back', 'Pengguna kembali sebelum verifikasi identitas selesai.'],
            ['consent_denied', 'Persetujuan untuk verifikasi identitas ditolak.'],
            ['too_many_attempts', 'Batas percobaan verifikasi identitas telah tercapai.'],
            ['session_expired', 'Sesi verifikasi identitas telah kedaluwarsa.'],
            ['liveness_blocked', 'Verifikasi liveness diblokir karena pemeriksaan tidak memenuhi syarat.'],
            ['fallback_manual', 'Verifikasi identitas dialihkan ke proses manual.'],
            ['error', 'Terjadi kesalahan saat verifikasi identitas.'],
        ];
    }

    public function test_nested_signed_session_id_is_accepted(): void
    {
        $owner = $this->user();
        $old = $this->createSession($owner);
        $current = $this->createSession($owner);
        $this->provider(['data' => ['session_id' => $current->privy_session_id, 'status' => 'FAILED']]);

        $this->assertSame(200, $this->invokeCallback()->getStatusCode());
        $this->assertNotNull($current->fresh()->callback_received_at);
        $this->assertTrue(PrivyDigitalIdSession::withTrashed()->findOrFail($old->id)->trashed());
    }

    public function test_unknown_and_deleted_sessions_are_acknowledged_without_any_writes(): void
    {
        $owner = $this->user();
        $deleted = $this->createSession($owner);
        $deleted->delete();
        $this->createSession($owner);
        foreach (['unknown-session', $deleted->privy_session_id] as $signedId) {
            $before = DB::table('privy_digital_id_sessions')->orderBy('id')->get()->toJson();
            $this->provider(['session_id' => $signedId, 'status' => 'FAILED']);
            $response = $this->invokeCallback();
            $this->assertSame(200, $response->getStatusCode());
            $this->assertTrue($response->getData(true)['received']);
            $this->assertFalse($response->getData(true)['processed']);
            $this->assertSame($before, DB::table('privy_digital_id_sessions')->orderBy('id')->get()->toJson());
        }
    }

    public function test_repeated_callback_cannot_delete_a_new_sibling_or_restore_a_retired_session(): void
    {
        $owner = $this->user();
        $first = $this->createSession($owner);
        $this->provider(['session_id' => $first->privy_session_id, 'status' => 'FAILED']);
        $this->invokeCallback();
        $acceptedAt = $first->fresh()->callback_received_at->toDateTimeString();
        $newer = $this->createSession($owner);
        $this->provider(['session_id' => $first->privy_session_id, 'status' => 'FAILED']);
        $this->invokeCallback();
        $this->assertNotNull($newer->fresh());
        $this->assertNull($newer->fresh()->deleted_at);
        $this->assertSame($acceptedAt, $first->fresh()->callback_received_at->toDateTimeString());

        $this->provider(['session_id' => $newer->privy_session_id, 'status' => 'FAILED']);
        $this->invokeCallback();
        $this->assertNull(PrivyDigitalIdSession::find($first->id));
        $before = DB::table('privy_digital_id_sessions')->orderBy('id')->get()->toJson();
        $this->provider(['session_id' => $first->privy_session_id, 'status' => 'FAILED']);
        $this->assertFalse($this->invokeCallback()->getData(true)['processed']);
        $this->assertSame($before, DB::table('privy_digital_id_sessions')->orderBy('id')->get()->toJson());
        $this->assertSame([$newer->id], PrivyDigitalIdSession::pluck('id')->all());
    }

    public function test_first_callback_for_exchange_verified_session_retires_siblings_even_when_claim_is_refused(): void
    {
        $owner = $this->user();
        $verified = $this->createSession($owner, [
            'status' => 'verified', 'processed_by' => 'exchange', 'privy_id' => 'SYNTHETIC-PRIVY',
            'exchange_received_at' => now(), 'exchanged_at' => now(), 'verified_at' => now(),
        ]);
        $sibling = $this->createSession($owner);
        $this->provider(['session_id' => $verified->privy_session_id, 'status' => 'FAILED']);

        $this->assertSame(200, $this->invokeCallback()->getStatusCode());

        $this->assertNotNull($verified->fresh()->callback_received_at);
        $this->assertSame('verified', $verified->fresh()->status);
        $this->assertSame('exchange', $verified->fresh()->processed_by);
        $this->assertSame(0, $verified->fresh()->attempts);
        $this->assertTrue(PrivyDigitalIdSession::withTrashed()->findOrFail($sibling->id)->trashed());
    }

    public function test_retirement_is_committed_before_provider_identity_processing_fails(): void
    {
        $owner = $this->user();
        $old = $this->createSession($owner);
        $current = $this->createSession($owner);
        $client = Mockery::mock(PrivyDigitalIdClient::class);
        $client->shouldReceive('verifyToken')->once()->with('synthetic-token')->andReturn([
            'session_id' => $current->privy_session_id, 'status' => 'VERIFIED', 'my_identity' => 'broken-ciphertext',
        ]);
        $client->shouldReceive('decryptIdentity')->once()->with('broken-ciphertext')
            ->andThrow(new PrivyDigitalIdException('Synthetic decryption failure', 'decrypt_failed'));
        $this->app->instance(PrivyDigitalIdClient::class, $client);

        $response = $this->invokeCallback();
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('decrypt_failed', $response->getData(true)['error_code']);
        $this->assertTrue(PrivyDigitalIdSession::withTrashed()->findOrFail($old->id)->trashed());
        $this->assertNotNull($current->fresh()->callback_received_at);
        $this->assertSame('decrypt_failed', $current->fresh()->error_code);
    }

    public function test_soft_deleted_sessions_are_excluded_from_reads_claims_and_updates(): void
    {
        $session = $this->createSession($this->user());
        $session->delete();
        $service = app(PrivyDigitalIdService::class);
        $before = DB::table('privy_digital_id_sessions')->where('id', $session->id)->first();

        $this->assertNull(PrivyDigitalIdSession::find($session->id));
        $this->assertNull($service->getSession($session->privy_session_id));
        $this->assertFalse($service->claimSession($session->privy_session_id, 'callback'));
        $this->assertSame(0, $service->markReceived($session->privy_session_id, 'callback'));
        $this->assertNull($service->acceptCallback($session));
        $this->assertTrue(PrivyDigitalIdSession::withTrashed()->findOrFail($session->id)->trashed());
        $this->assertEquals($before, DB::table('privy_digital_id_sessions')->where('id', $session->id)->first());
    }

    public function test_exchange_rejects_code_historically_owned_by_another_user(): void
    {
        $historical = $this->createSession($this->user(), ['exchange_code' => 'historical-code', 'status' => 'verified']);
        $historical->delete();
        $requester = $this->user();
        $this->createSession($requester);
        // No provider methods are mocked here: reaching the provider is a test failure.
        $this->app->instance(PrivyDigitalIdClient::class, Mockery::mock(PrivyDigitalIdClient::class));
        $this->actingAs($requester, 'api');

        $response = app(PrivyController::class)->digitalIdExchange(Request::create('/privy/digital-id/exchange', 'POST', [
            'exchange_code' => 'historical-code',
        ]));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Kode verifikasi tidak sesuai dengan akun Anda.', $response->getData(true)['message']);
        $this->assertTrue(PrivyDigitalIdSession::withTrashed()->findOrFail($historical->id)->trashed());
    }

    /** Payload JWT asli Privy tidak membawa "status"; status berasal dari event identity.verified. */
    public function test_identity_verified_event_marks_user_verified_and_is_idempotent(): void
    {
        $owner = $this->user();
        $session = $this->createSession($owner);
        $payload = ['session_id' => $session->privy_session_id, 'data' => ['my_identity' => 'synthetic-ciphertext']];
        $this->provider($payload, ['identity_number' => '1111111111111111', 'dob' => '1990-01-02', 'privy_id' => 'SYNTHETIC-PRIVY']);

        $response = $this->invokeCallback(['session_id' => $session->privy_session_id, 'event' => 'identity.verified']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['status']);
        $this->assertTrue($response->getData(true)['privy_verified']);
        $this->assertSame('SYNTHETIC-PRIVY', $response->getData(true)['privy_id']);
        $this->assertSame('y', $owner->fresh()->verified_privy);
        $this->assertSame('SYNTHETIC-PRIVY', $owner->fresh()->id_privy);
        $this->assertSame('verified', $session->fresh()->status);

        // Callback kedua (mis. dari server Privy) tidak memproses ulang dan memberi hasil yang sama
        $client = Mockery::mock(PrivyDigitalIdClient::class);
        $client->shouldReceive('verifyToken')->once()->andReturn($payload);
        $client->shouldNotReceive('decryptIdentity');
        $this->app->instance(PrivyDigitalIdClient::class, $client);

        $again = $this->invokeCallback(['session_id' => $session->privy_session_id, 'event' => 'identity.verified']);

        $this->assertSame(200, $again->getStatusCode());
        $this->assertTrue($again->getData(true)['privy_verified']);
        $this->assertSame('SYNTHETIC-PRIVY', $again->getData(true)['privy_id']);
    }

    public function test_user_without_nik_and_dob_is_verified_and_filled_from_privy(): void
    {
        $owner = new User;
        $owner->forceFill(['verified_privy' => 'n'])->save();
        $session = $this->createSession($owner);
        $this->provider(
            ['session_id' => $session->privy_session_id, 'data' => ['my_identity' => 'synthetic-ciphertext']],
            ['identity_number' => '3175030605980006', 'dob' => '1998-05-06', 'privy_id' => 'SYNTHETIC-PRIVY'],
        );

        $response = $this->invokeCallback(['event' => 'identity.verified']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['privy_verified']);
        $owner->refresh();
        $this->assertSame('y', $owner->verified_privy);
        $this->assertSame('3175030605980006', $owner->nik);
        $this->assertSame('1998-05-06', $owner->dob);
    }

    public function test_privy_nik_already_owned_by_another_account_is_rejected(): void
    {
        $other = $this->user();
        $owner = new User;
        $owner->forceFill(['verified_privy' => 'n'])->save();
        $session = $this->createSession($owner);
        $this->provider(
            ['session_id' => $session->privy_session_id, 'data' => ['my_identity' => 'synthetic-ciphertext']],
            ['identity_number' => $other->nik, 'dob' => '1990-01-02', 'privy_id' => 'SYNTHETIC-PRIVY'],
        );

        $response = $this->invokeCallback(['event' => 'identity.verified']);

        $this->assertSame('identity_conflict', $response->getData(true)['reason']);
        $this->assertSame('identity_conflict', $session->fresh()->status);
        $this->assertSame('n', $owner->fresh()->verified_privy);
        $this->assertNull($owner->fresh()->nik);
    }

    public function test_verified_event_without_token_requires_token(): void
    {
        $session = $this->createSession($this->user());
        $client = Mockery::mock(PrivyDigitalIdClient::class);
        $client->shouldNotReceive('verifyToken');
        $this->app->instance(PrivyDigitalIdClient::class, $client);

        $response = $this->invokeCallback(['token' => null, 'session_id' => $session->privy_session_id, 'event' => 'identity.verified']);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('token wajib diisi', $response->getData(true)['message']);
        $this->assertSame('created', $session->fresh()->status);
    }

    public function test_tokenless_failure_does_not_downgrade_verified_session(): void
    {
        $session = $this->createSession($this->user(), ['status' => 'verified', 'privy_id' => 'SYNTHETIC-PRIVY']);
        $this->app->instance(PrivyDigitalIdClient::class, Mockery::mock(PrivyDigitalIdClient::class));

        $response = $this->invokeCallback(['token' => null, 'session_id' => $session->privy_session_id, 'event' => 'identity.cancelled']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('verified', $session->fresh()->status);
    }

    public function test_unknown_tokenless_reason_is_recorded_as_error(): void
    {
        $session = $this->createSession($this->user());
        $this->app->instance(PrivyDigitalIdClient::class, Mockery::mock(PrivyDigitalIdClient::class));

        $response = $this->invokeCallback(['token' => null, 'session_id' => $session->privy_session_id, 'event' => 'identity.something_new']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('failed', $session->fresh()->status);
        $this->assertSame('error', $session->fresh()->error_code);
    }

    private function user(): User
    {
        $user = new User;
        $user->forceFill(['nik' => '1111111111111111', 'dob' => '1990-01-02', 'verified_privy' => 'n'])->save();

        return $user;
    }

    private function createSession(User $user, array $attributes = []): PrivyDigitalIdSession
    {
        $id = 'synthetic-'.++$this->sessionSequence;

        return PrivyDigitalIdSession::create(array_replace([
            'user_id' => $user->id, 'reference_number' => $id, 'privy_session_id' => $id, 'status' => 'created',
        ], $attributes));
    }

    private function provider(?array $payload, ?array $identity = null): void
    {
        $client = Mockery::mock(PrivyDigitalIdClient::class);
        $verification = $client->shouldReceive('verifyToken')->once()->with('synthetic-token');
        if ($payload === null) {
            $verification->andThrow(new PrivyDigitalIdException('Synthetic invalid signature', 'invalid_token', 401));
        } else {
            $verification->andReturn($payload);
        }
        if ($identity !== null) {
            $client->shouldReceive('decryptIdentity')->once()->with('synthetic-ciphertext')->andReturn($identity);
        } else {
            $client->shouldNotReceive('decryptIdentity');
        }
        $this->app->instance(PrivyDigitalIdClient::class, $client);
    }

    private function invokeCallback(array $input = []): JsonResponse
    {
        return app(PrivyController::class)->digitalIdCallback(Request::create('/privy/digital-id/callback', 'POST',
            array_replace(['token' => 'synthetic-token'], $input)));
    }
}
