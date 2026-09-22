<?php

namespace App\Domain;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use InvalidArgumentException;

/**
 * Payment lifecycle (backend AGENTS.md). Status-only: callers create the
 * reservation audit events. Adds pending/processing/unknown → expired so
 * ExpirePendingReservationAction can close open payments; this extension
 * is documented in LOG-004.
 */
final class PaymentStateMachine
{
    /** @return array<string, list<string>> */
    public static function allowed(): array
    {
        return [
            PaymentStatus::Pending->value => [
                PaymentStatus::Processing->value,
                PaymentStatus::Expired->value,
            ],
            PaymentStatus::Processing->value => [
                PaymentStatus::Paid->value,
                PaymentStatus::Failed->value,
                PaymentStatus::Unknown->value,
                PaymentStatus::Expired->value,
            ],
            PaymentStatus::Unknown->value => [
                PaymentStatus::Paid->value,
                PaymentStatus::Failed->value,
                PaymentStatus::Expired->value,
            ],
            PaymentStatus::Failed->value => [
                PaymentStatus::Processing->value,
            ],
            PaymentStatus::Paid->value => [],
            PaymentStatus::Expired->value => [],
        ];
    }

    public static function can(PaymentStatus $from, PaymentStatus $to): bool
    {
        return in_array($to->value, self::allowed()[$from->value] ?? [], true);
    }

    public static function transition(Payment $payment, PaymentStatus $to): Payment
    {
        $from = $payment->status;

        if (! self::can($from, $to)) {
            throw new InvalidArgumentException(
                "Illegal payment transition from {$from->value} to {$to->value}."
            );
        }

        $payment->status = $to;

        if ($to === PaymentStatus::Paid && $payment->paid_at === null) {
            $payment->paid_at = now();
        }

        $payment->save();

        return $payment;
    }
}
