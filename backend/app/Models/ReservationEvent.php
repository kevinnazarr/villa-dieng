<?php

namespace App\Models;

use App\Enums\ReservationEventType;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationEvent extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'reservation_id',
        'actor_id',
        'event_type',
        'from_status',
        'to_status',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => ReservationEventType::class,
            'from_status' => ReservationStatus::class,
            'to_status' => ReservationStatus::class,
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
