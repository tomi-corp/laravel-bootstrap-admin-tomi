<?php

namespace Tests\Unit;

use App\Http\Middleware\PayrollMaintenanceMiddleware;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PayrollMaintenanceMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_payment_date_above_25_uses_h_minus_7_until_end_of_month(): void
    {
        $this->assertMaintenanceOn('2026-09-19', 26);
        $this->assertMaintenanceOn('2026-09-30', 26);
        $this->assertMaintenanceOff('2026-10-01', 26);
    }

    public function test_payment_date_25_or_below_uses_h_minus_7_until_h_plus_7(): void
    {
        $this->assertMaintenanceOn('2026-09-03', 10);
        $this->assertMaintenanceOn('2026-09-17', 10);
        $this->assertMaintenanceOff('2026-09-18', 10);
    }

    private function assertMaintenanceOn(string $today, int $paymentDate): void
    {
        $this->assertTrue($this->isInPayrollMaintenanceWindow($today, $paymentDate));
    }

    private function assertMaintenanceOff(string $today, int $paymentDate): void
    {
        $this->assertFalse($this->isInPayrollMaintenanceWindow($today, $paymentDate));
    }

    private function isInPayrollMaintenanceWindow(string $today, int $paymentDate): bool
    {
        Carbon::setTestNow(Carbon::parse($today));

        $reflection = new ReflectionClass(PayrollMaintenanceMiddleware::class);
        $method = $reflection->getMethod('isInPayrollMaintenanceWindow');
        $method->setAccessible(true);

        return $method->invoke(new PayrollMaintenanceMiddleware(), $paymentDate);
    }
}
