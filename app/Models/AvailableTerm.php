<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvailableTerm extends Model
{
    use HasFactory;
    use HasUserstamps;

    const TABLE_NAME = 'available_terms';

    const ID_COLUMN = 'id';
    const AVAILABLE_TERM_THERAPIST_ID = 'therapist_id';
    const AVAILABLE_TERM_LOCATION_ID = 'location_id';
    const AVAILABLE_TERM_DATE = 'date';
    const AVAILABLE_TERM_TIME = 'time';
    const AVAILABLE_TERM_STATUS = 'status';
    const THERAPIST_RELATION = 'therapist';
    const LOCATION_RELATION = 'location';
    const VISITS_RELATION = 'visits';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    const STATUS_AVAILABLE = 0;
    const STATUS_BOOKED = 1;
    const STATUS_CANCELED = 2;

    const STATUS_ENUM = [
        self::STATUS_AVAILABLE,
        self::STATUS_BOOKED,
        self::STATUS_CANCELED,
    ];

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
        self::AVAILABLE_TERM_THERAPIST_ID,
        self::AVAILABLE_TERM_LOCATION_ID,
        self::AVAILABLE_TERM_DATE,
        self::AVAILABLE_TERM_TIME,
        self::AVAILABLE_TERM_STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
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
     *  Getter for therapistId
     *
     * @return int|null
     */
    public function getTherapistId(): ?int
    {
        return $this->getAttribute(self::AVAILABLE_TERM_THERAPIST_ID);
    }

    /**
     *  Getter for locationId
     *
     * @return int|null
     */
    public function getLocationId(): ?int
    {
        return $this->getAttribute(self::AVAILABLE_TERM_LOCATION_ID);
    }

    /**
     *  Getter for date
     *
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->getAttribute(self::AVAILABLE_TERM_DATE);
    }

    /**
     *  Getter for time
     *
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->getAttribute(self::AVAILABLE_TERM_TIME);
    }

    /**
     *  Getter for status
     *
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->getAttribute(self::AVAILABLE_TERM_STATUS);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, self::CREATED_BY, User::ID_COLUMN);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, self::UPDATED_BY, User::ID_COLUMN);
    }

    /**
     * AvailableTerm therapist relation
     *
     * @return BelongsTo
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class, self::AVAILABLE_TERM_THERAPIST_ID, Therapist::ID_COLUMN);
    }

    /**
     * AvailableTerm location relation
     *
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, self::AVAILABLE_TERM_LOCATION_ID, Location::ID_COLUMN);
    }

    /**
     * AvailableTerm visits relation
     *
     * @return HasMany
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, Visit::VISIT_AVAILABLE_TERM_ID, self::ID_COLUMN);
    }
}
