<?php

namespace Tests\Unit;

use App\Http\Middleware\WeekdaySubmissionMiddleware;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class WeekdaySubmissionMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_weekend_is_marked_as_holiday_and_submission_is_rejected(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-21')); // Saturday
        $nextCalled = false;

        $response = (new WeekdaySubmissionMiddleware())->handle(
            Request::create('/v1/api/action/pinjaman', 'POST'),
            function () use (&$nextCalled) {
                $nextCalled = true;
            }
        );

        $this->assertFalse($nextCalled);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertTrue(json_decode($response->getContent(), true)['holiday']);
    }

    public function test_weekday_allows_submission(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-20')); // Friday
        $nextCalled = false;

        $response = (new WeekdaySubmissionMiddleware())->handle(
            Request::create('/v1/api/action/pinjaman', 'POST'),
            function () use (&$nextCalled) {
                $nextCalled = true;

                return new JsonResponse(['status' => 200]);
            }
        );

        $this->assertTrue($nextCalled);
        $this->assertSame(200, $response->getStatusCode());
    }
}
