<?php

namespace App\Enums;

enum ReservationEventType: string
{
    case Created = 'created';
    case PaymentInitiated = 'payment_initiated';
    case PaymentSucceeded = 'payment_succeeded';
    case PaymentFailed = 'payment_failed';
    case Confirmed = 'confirmed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case AvailabilityBlocked = 'availability_blocked';
    case AvailabilityUnblocked = 'availability_unblocked';
}
