<?php

namespace App\Services;

use App\Models\PrivyDigitalIdSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PrivyDigitalIdService
{
    public function newRequestDigitalId(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $softDeleted = DB::table('tbl_privy_digitalID')
                ->where('id_borrower', $data['id_borrower'])
                ->whereNull('deleted')
                ->update(['deleted' => now()]);

            $insertId = DB::table('tbl_privy_digitalID')->insertGetId([
                'id_borrower' => $data['id_borrower'],
                'expired_at' => $data['expired_at'] ?? null,
                'requestId' => $data['request_id'] ?? null,
                'identifier' => $data['identifier'] ?? null,
                'waiting_url' => $data['waiting_url'] ?? null,
                'deeplink' => $data['deeplink'] ?? null,
            ]);

            return [
                'softDeleted' => $softDeleted,
                'insertId' => $insertId,
            ];
        });
    }

    public function insertConnectDigitalId(array $data): int
    {
        return DB::table('tbl_privy_digitalID')->insertGetId([
            'id_borrower' => $data['id_borrower'],
            'expired_at' => $data['expired_at'] ?? null,
            'requestId' => $data['request_id'] ?? null,
            'identifier' => $data['identifier'] ?? null,
            'waiting_url' => $data['waiting_url'] ?? null,
            'deeplink' => $data['deeplink'] ?? null,
        ]);
    }

    public function insertSession(array $data): PrivyDigitalIdSession
    {
        return PrivyDigitalIdSession::create([
            'user_id' => $data['user_id'] ?? $data['id_borrower'],
            'reference_number' => $data['reference_number'] ?? $data['session_id'],
            'privy_session_id' => $data['session_id'],
            'session_url' => $data['session_url'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'status' => 'created',
        ]);
    }

    public function getSession(string $sessionId): ?PrivyDigitalIdSession
    {
        return PrivyDigitalIdSession::where('privy_session_id', $sessionId)->first();
    }

    public function acceptCallback(PrivyDigitalIdSession $session): ?PrivyDigitalIdSession
    {
        return DB::transaction(function () use ($session) {
            $owner = User::whereKey($session->user_id)->lockForUpdate()->first();

            if (! $owner) {
                return null;
            }

            $activeSession = PrivyDigitalIdSession::whereKey($session->getKey())
                ->where('user_id', $owner->getKey())
                ->lockForUpdate()
                ->first();

            if (! $activeSession) {
                return null;
            }

            if ($activeSession->callback_received_at === null) {
                PrivyDigitalIdSession::where('user_id', $owner->getKey())
                    ->where('id', '!=', $activeSession->getKey())
                    ->delete();

                $activeSession->callback_received_at = now();
                $activeSession->save();
            }

            return $activeSession;
        });
    }

    public function markReceived(string $sessionId, string $source): int
    {
        $column = $source === 'callback'
            ? 'callback_received_at'
            : 'exchange_received_at';

        return PrivyDigitalIdSession::where('privy_session_id', $sessionId)
            ->whereNull($column)
            ->update([$column => now()]);
    }

    public function claimSession(string $sessionId, string $source): bool
    {
        $affected = PrivyDigitalIdSession::where('privy_session_id', $sessionId)
            ->where(function ($query) {
                $query->whereIn('status', ['created', 'pending', 'failed'])
                    ->orWhere(function ($query) {
                        $query->where('status', 'processing')
                            ->where('processing_started_at', '<', now()->subMinutes(10));
                    });
            })
            ->update([
                'status' => 'processing',
                'processed_by' => $source,
                'processing_started_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
                'error_code' => null,
                'error_message' => null,
            ]);

        return $affected === 1;
    }

    public function finishSession(string $sessionId, array $data): int
    {
        $values = [
            'status' => strtolower($data['status']),
            'event' => $data['event'] ?? null,
            'privy_id' => $data['privy_id'] ?? null,
        ];

        if (strtoupper((string) $data['status']) === 'VERIFIED') {
            $values['verified_at'] = now();
        }

        if (array_key_exists('error_code', $data)) {
            $values['error_code'] = $data['error_code'];
        }

        if (array_key_exists('error_message', $data)) {
            $values['error_message'] = substr((string) $data['error_message'], 0, 255);
        }

        return PrivyDigitalIdSession::where('privy_session_id', $sessionId)
            ->where('status', 'processing')
            ->update($values);
    }

    public function failSession(string $sessionId, ?string $errorCode, ?string $errorMessage): int
    {
        return PrivyDigitalIdSession::where('privy_session_id', $sessionId)
            ->where('status', 'processing')
            ->update([
                'status' => 'failed',
                'error_code' => substr((string) $errorCode, 0, 50),
                'error_message' => substr((string) $errorMessage, 0, 255),
            ]);
    }

    public function updateDecryptedIdentity(string $sessionId, array $identity): int
    {
        return PrivyDigitalIdSession::where('privy_session_id', $sessionId)->update([
            'decrypted_privy_id' => $identity['privy_id'] ?? null,
            'decrypted_name' => $identity['name'] ?? null,
            'decrypted_identity_number' => $identity['identity_number'] ?? null,
            'decrypted_dob' => isset($identity['dob']) ? \Carbon\Carbon::parse($identity['dob'])->format('Y-m-d') : null,
            'decrypted_email' => $identity['email'] ?? null,
            'decrypted_phone' => $identity['phone'] ?? null,
            'decrypted_raw' => $identity,
            'decrypted_at' => now(),
        ]);
    }
}
