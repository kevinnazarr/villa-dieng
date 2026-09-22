<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_code',
        'user_id',
        'cabin_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'nightly_rate',
        'subtotal',
        'total',
        'currency',
        'status',
        'guest_name',
        'guest_email',
        'guest_phone',
        'special_request',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'adults' => 'integer',
            'children' => 'integer',
            'nightly_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => ReservationStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cabin(): BelongsTo
    {
        return $this->belongsTo(Cabin::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ReservationEvent::class)->orderBy('created_at');
    }
}
