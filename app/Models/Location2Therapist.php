<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location2Therapist extends Model
{
    use HasFactory;

    const TABLE_NAME = 'locations2therapists';

    const LOCATION_ID = 'location_id';
    const THERAPIST_ID = 'therapist_id';
    const LOCATION_RELATION = 'location';
    const THERAPIST_RELATION = 'therapist';


    /** @var string */
    protected $table = self::TABLE_NAME;
    /** @var array */
    protected $guarded = [];
    /** @var string[] */
    protected $casts = [
        self::LOCATION_ID => 'int',
        self::THERAPIST_ID => 'int',
    ];

    /** @return int */
    public function getLocationId()
    {
        return $this->getAttribute(self::LOCATION_ID);
    }

    /** @return int */
    public function getTherapistId()
    {
        return $this->getAttribute(self::THERAPIST_ID);
    }

    /** @return BelongsTo */
    public function location(): BelongsTo
    {
        return $this->belongsTo(
            Location::class,
            self::LOCATION_ID,
            Location::ID_COLUMN
        );
    }

    /** @return BelongsTo */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(
            Therapist::class,
            self::THERAPIST_ID,
            Therapist::ID_COLUMN
        );
    }
}
