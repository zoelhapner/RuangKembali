<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Event extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'license_id',
        'event_code',
        'name',
        'event_category_id',
        'event_type',
        'audience_type',
        'registration_open',
        'registration_close',
        'start_at',
        'end_at',
        'location',
        'price',
        'quota',
        'poster',
        'thumbnail',
        'description',
        'status',
        'is_published',
    ];

    protected $casts = [
        'registration_open'  => 'date',
        'registration_close' => 'date',
        'start_at'           => 'datetime',
        'end_at'             => 'datetime',
        'price'              => 'decimal:2',
        'is_published'       => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function speakers()
    {
        return $this->hasMany(EventSpeaker::class);
    }

    public function faqs()
    {
        return $this->hasMany(EventFaq::class);
    }

    public function galleries()
    {
        return $this->hasMany(EventGallery::class);
    }

    public function sponsors()
    {
        return $this->hasMany(EventSponsor::class);
    }

    public function vouchers()
    {
        return $this->hasMany(EventVoucher::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function certificate()
    {
        return $this->hasOne(EventCertificate::class);
    }

    public function getRemainingQuotaAttribute()
    {
        if (is_null($this->quota)) {
            return null;
        }

        return max(
            0,
            $this->quota - $this->registrations()->count()
        );
    }

    public function getIsRegistrationOpenAttribute()
    {
        if (!$this->registration_open || !$this->registration_close) {
            return false;
        }

        $now = now();

        return $now->between(
            $this->registration_open->startOfDay(),
            $this->registration_close->endOfDay()
        );
    }

    public function getIsFullAttribute()
    {
        if (is_null($this->quota)) {
            return false;
        }

        return $this->remaining_quota <= 0;
    }

    public function getStatusLabelAttribute()
    {
        $now = now();

        if ($this->start_at && $now->lt($this->start_at)) {

            if (
                $this->registration_open &&
                $now->gte($this->registration_open->startOfDay()) &&
                $this->registration_close &&
                $now->lte($this->registration_close->endOfDay())
            ) {
                return $this->is_full
                    ? 'Sold Out'
                    : 'Pendaftaran';
            }

            return 'Coming Soon';
        }

        if (
            $this->start_at &&
            $this->end_at &&
            $now->between($this->start_at, $this->end_at)
        ) {
            return 'Sedang Berlangsung';
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return 'Selesai';
        }

        return 'Coming Soon';
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    public function scopeFinished($query)
    {
        return $query->where('end_at', '<', now());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}