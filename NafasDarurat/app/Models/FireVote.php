<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FireVote extends Model
{
    protected $fillable = ['fire_report_id', 'voter_key', 'choice'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(FireReport::class, 'fire_report_id');
    }
}
