<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Booking extends Model
{
    /** -------- Status constants — must match DB ENUM exactly -------- */
    public const STATUS_PENDING   = 'pending';        // created, awaiting admin action
    public const STATUS_AWAITING  = 'awaiting_admin'; // card paid, waiting for admin approval
    public const STATUS_CONFIRMED = 'confirmed';      // approved by admin
    public const STATUS_REJECTED  = 'rejected';       // rejected by admin
    public const STATUS_CANCELLED = 'cancelled';      // cancelled by admin or customer
    public const STATUS_COMPLETED = 'completed';      // service delivered

    protected $fillable = [
        'user_id',
        'forklift_id',
        'start_time',
        'end_time',
        'notes',
        'status',
        'service_address',
        'postal_code',
        'city',
        'province',
        'country',
        'payment_method',
        'payment_intent_id',
        'payment_status',
        'amount_subtotal',
        'amount_gst',
        'amount_pst',
        'amount_total',
        'currency',
        'invoice_number',
        'refund_status',
        'refund_amount',
        'refunded_at',
        'completed_at',
        'paid_at',
    ];

    protected $casts = [
        'start_time'      => 'datetime',
        'end_time'        => 'datetime',
        'completed_at'    => 'datetime',
        'refunded_at'     => 'datetime',
        'amount_subtotal' => 'integer',
        'amount_gst'      => 'integer',
        'amount_pst'      => 'integer',
        'amount_total'    => 'integer',
        'refund_amount'   => 'integer',
        'paid_at'         => 'datetime',
    ];

    protected $attributes = [
        'status'   => self::STATUS_PENDING,
        'currency' => 'CAD',
    ];

    /** ---------------- Relationships ---------------- */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forklift(): BelongsTo
    {
        return $this->belongsTo(Forklift::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Location::class);
    }

    /** ---------------- Model hooks ------------------ */
    protected static function booted(): void
    {
        static::creating(function (self $b) {
            if (empty($b->invoice_number)) {
                $b->invoice_number = 'INV-' . now('UTC')->format('Ymd') . '-' . Str::upper(Str::random(6));
            }
            $b->currency ??= 'CAD';
        });
    }

    /** ---------------- Scopes ----------------------- */
    public function scopeConfirmed(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_CONFIRMED);
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_PENDING);
    }

    public function scopeForForklift(Builder $q, int $forkliftId): Builder
    {
        return $q->where('forklift_id', $forkliftId);
    }

    public function scopeForDateRange(Builder $q, $from, $to): Builder
    {
        return $q->where(function ($qq) use ($from, $to) {
            $qq->whereBetween('start_time', [$from, $to])
               ->orWhereBetween('end_time', [$from, $to])
               ->orWhere(function ($q3) use ($from, $to) {
                   $q3->where('start_time', '<=', $from)
                      ->where('end_time',   '>=', $to);
               });
        });
    }

    public function scopeOverlapping(Builder $q, $start, $end, ?int $exceptId = null): Builder
    {
        return $q->where('start_time', '<', $end)
                 ->where('end_time',   '>', $start)
                 ->when($exceptId, fn ($qq) => $qq->where('id', '!=', $exceptId));
    }

    /** ---------------- Status helpers --------------- */
    public function isAwaitingAdmin(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_AWAITING]);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isRejected(): bool
    {
        return in_array($this->status, [self::STATUS_REJECTED, self::STATUS_CANCELLED]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /** ---------------- Accessors (MONEY) ------------ */
    public function getSubtotalDollarsAttribute(): float   { return ($this->amount_subtotal ?? 0) / 100; }
    public function getGstDollarsAttribute(): float        { return ($this->amount_gst ?? 0) / 100; }
    public function getPstDollarsAttribute(): float        { return ($this->amount_pst ?? 0) / 100; }
    public function getTotalDollarsAttribute(): float      { return ($this->amount_total ?? 0) / 100; }

    public function getSubtotalFormattedAttribute(): string { return $this->formatMoney($this->amount_subtotal ?? 0); }
    public function getGstFormattedAttribute(): string      { return $this->formatMoney($this->amount_gst ?? 0); }
    public function getPstFormattedAttribute(): string      { return $this->formatMoney($this->amount_pst ?? 0); }
    public function getTotalFormattedAttribute(): string    { return $this->formatMoney($this->amount_total ?? 0); }

    /** ---------------- Accessors (TIME) ------------- */
    protected function displayTz(): string
    {
        return config('booking.display_tz', 'America/Regina');
    }

    public function getStartLocalAttribute(): ?Carbon
    {
        return $this->start_time?->clone()->setTimezone($this->displayTz());
    }

    public function getEndLocalAttribute(): ?Carbon
    {
        return $this->end_time?->clone()->setTimezone($this->displayTz());
    }

    public function getStartLocalFmtAttribute(): string
    {
        return $this->start_local?->format('Y-m-d H:i') ?? '';
    }

    public function getEndLocalFmtAttribute(): string
    {
        return $this->end_local?->format('Y-m-d H:i') ?? '';
    }

    public function getHoursAttribute(): float
    {
        if (!$this->start_time || !$this->end_time) return 0.0;
        return $this->end_time->floatDiffInRealHours($this->start_time);
    }

    public function getHoursRoundedAttribute(): int
    {
        return max(1, (int) ceil($this->hours));
    }

    /** ---------------- Helpers ---------------------- */
    protected function formatMoney(int $cents): string
    {
        return ($this->currency ?? 'CAD') === 'CAD'
            ? '$' . number_format($cents / 100, 2) . ' CAD'
            : number_format($cents / 100, 2) . ' ' . $this->currency;
    }
}
