<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * Double-booking conflict. Thrown when a reservation or availability block
 * collides with existing inventory, including PostgreSQL EXCLUDE
 * violations (SQLSTATE 23P01). Rendered as HTTP 409 BOOKING_CONFLICT.
 */
class BookingConflictException extends HttpException
{
    public function __construct(string $message = 'BOOKING_CONFLICT', ?Throwable $previous = null)
    {
        parent::__construct(409, $message, $previous);
    }

    public static function fromExclusionViolation(Throwable $e): self
    {
        return new self('BOOKING_CONFLICT', $e);
    }

    public static function isExclusionViolation(Throwable $e): bool
    {
        $current = $e;

        while ($current instanceof Throwable) {
            $code = $current->getCode();

            if ($code === '23P01' || $code === 23001) {
                return true;
            }

            $message = $current->getMessage();

            if (str_contains($message, '23P01') || str_contains($message, 'exclusion')) {
                return true;
            }

            $current = $current->getPrevious();
        }

        return false;
    }
}