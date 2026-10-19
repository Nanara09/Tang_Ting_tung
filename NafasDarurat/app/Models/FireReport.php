<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class FireReport extends Model
{
    protected $fillable = ['place', 'description', 'status', 'lat', 'lng', 'photo_path'];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'yes_count' => 'integer',
        'no_count' => 'integer',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(FireVote::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('state', 'approved');
    }

    public function photoUrl(): string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : '';
    }

    /** Bentuk JSON untuk JavaScript (kunci sama dengan versi demo: place, desc, ts, st, yes, no, voted). */
    public function toPublic(array $votedChoices = []): array
    {
        return [
            'id' => (string) $this->id,
            'place' => $this->place,
            'desc' => $this->description ?: '-',
            'ts' => (int) ($this->created_at->getTimestamp() * 1000),
            'st' => $this->status,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'yes' => $this->yes_count,
            'no' => $this->no_count,
            'photo' => $this->photoUrl(),
            'voted' => ['y' => in_array('y', $votedChoices, true), 'n' => in_array('n', $votedChoices, true)],
        ];
    }

    protected static function booted(): void
    {
        // Hapus file foto ketika laporan dihapus.
        static::deleted(function (self $report) {
            if ($report->photo_path) {
                Storage::disk('public')->delete($report->photo_path);
            }
        });
    }
}
