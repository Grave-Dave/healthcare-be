<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    const TABLE_NAME = 'locations';

    const ID_COLUMN = 'id';
    const LOCATION_NAME = 'name';
    const ENTRY_DATA = 'entry_data';
    const THERAPIST_RELATION = 'therapists';
    const AVAILABLE_TERMS_RELATION = 'availableTerms';

    /** @var int */
    protected $primaryKey = self::ID_COLUMN;

    /** @var string */
    protected $table = self::TABLE_NAME;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::LOCATION_NAME,
        self::ENTRY_DATA,
    ];

    /**
     * Getter for id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->getKey();
    }

    /**
     *  Getter for locationName
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getAttribute(self::LOCATION_NAME);
    }

    /**
     *  Getter for location entryData
     *
     * @return string|null
     */
    public function getEntryData(): ?string
    {
        return $this->getAttribute(self::ENTRY_DATA);
    }

    /**
     * Location therapist relation
     *
     * @return BelongsToMany
     */
    public function therapists(): BelongsToMany
    {
        return $this->belongsToMany(Therapist::class,
            Location2Therapist::TABLE_NAME,
            Location2Therapist::LOCATION_ID,
            Location2Therapist::THERAPIST_ID);
    }

    /**
     * Location availableTerm relation
     *
     * @return HasMany
     */
    public function availableTerms(): HasMany
    {
        return $this->hasMany(AvailableTerm::class, AvailableTerm::LOCATION_ID, self::ID_COLUMN);
    }
}
