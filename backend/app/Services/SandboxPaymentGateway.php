<?php

namespace App\Services;

/**
 * Sandbox payment simulation (no real money). Deterministic outcomes by
 * scenario so tests and local dev can drive success/fail/timeout paths:
 * 'success' → paid, 'fail' → failed, 'timeout' → unknown (reconcilable).
 */
final class SandboxPaymentGateway
{
    public const SCENARIO_SUCCESS = 'success';

    public const SCENARIO_FAIL = 'fail';

    public const SCENARIO_TIMEOUT = 'timeout';

    /** @return 'paid'|'failed'|'unknown' */
    public function charge(string $providerReference, string $amount, string $scenario = self::SCENARIO_SUCCESS): string
    {
        return match ($scenario) {
            self::SCENARIO_SUCCESS => 'paid',
            self::SCENARIO_FAIL => 'failed',
            self::SCENARIO_TIMEOUT => 'unknown',
            default => 'paid',
        };
    }
}
