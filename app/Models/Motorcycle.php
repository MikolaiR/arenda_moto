<?php

namespace App\Models;

use App\Enums\RentalStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'year', 'comment'])]
class Motorcycle extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Motorcycle $motorcycle) {
            if (empty($motorcycle->slug)) {
                $motorcycle->slug = (string) Str::ulid();
            }
        });

        static::created(function (Motorcycle $motorcycle) {
            $motorcycle->slug = $motorcycle->id . '-' . Str::slug($motorcycle->name) . '-' . $motorcycle->year;
            $motorcycle->saveQuietly();
        });

        static::updating(function (Motorcycle $motorcycle) {
            if ($motorcycle->isDirty('name') || $motorcycle->isDirty('year')) {
                $motorcycle->slug = $motorcycle->id . '-' . Str::slug($motorcycle->name) . '-' . $motorcycle->year;
            }
        });
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function activeRental(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Rental::class)
            ->where('status', RentalStatus::Rented->value)
            ->where('started_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ended_at')->orWhere('ended_at', '>', now());
            })
            ->latest('started_at');
    }

    public function upcomingRental(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Rental::class)
            ->where('status', RentalStatus::Rented->value)
            ->where('started_at', '>', now())
            ->oldest('started_at');
    }

    public function overlappingRentals(\DateTimeInterface $from, \DateTimeInterface $to, ?int $excludeRentalId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->rentals()
            ->where('status', RentalStatus::Rented->value)
            ->where('started_at', '<', $to)
            ->where(function ($q) use ($from) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>', $from);
            });

        if ($excludeRentalId) {
            $query->where('id', '!=', $excludeRentalId);
        }

        return $query->orderBy('started_at')->get();
    }

    public function isAvailableBetween(\DateTimeInterface $from, \DateTimeInterface $to, ?int $excludeRentalId = null): bool
    {
        return $this->overlappingRentals($from, $to, $excludeRentalId)->isEmpty();
    }

    public function currentStatus(): RentalStatus
    {
        $active = $this->activeRental;

        return $active ? RentalStatus::Rented : RentalStatus::Free;
    }
}
