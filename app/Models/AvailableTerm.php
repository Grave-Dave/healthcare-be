<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvailableTerm extends Model
{
    use HasFactory;
    use HasUserstamps;
    use SoftDeletes;

    const TABLE_NAME = 'available_terms';

    const ID_COLUMN = 'id';
    const THERAPIST_ID = 'therapist_id';
    const LOCATION_ID = 'location_id';
    const DATE = 'date';
    const TIME = 'time';
    const STATUS = 'status';
    const THERAPIST_RELATION = 'therapist';
    const LOCATION_RELATION = 'location';
    const VISITS_RELATION = 'visits';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const DELETED_BY = 'updated_by';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    const STATUS_AVAILABLE = '0';
    const STATUS_BOOKED = '1';
    const STATUS_CANCELED = '2';

    const STATUS_ENUM = [
        self::STATUS_AVAILABLE,
        self::STATUS_BOOKED,
        self::STATUS_CANCELED,
    ];

    /** @var int */
    protected $primaryKey = self::ID_COLUMN;

    /** @var string */
    protected $table = self::TABLE_NAME;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::THERAPIST_ID,
        self::LOCATION_ID,
        self::DATE,
        self::TIME,
        self::STATUS,
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
        return $this->getAttribute(self::THERAPIST_ID);
    }

    /**
     *  Getter for locationId
     *
     * @return int|null
     */
    public function getLocationId(): ?int
    {
        return $this->getAttribute(self::LOCATION_ID);
    }

    /**
     *  Getter for date
     *
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->getAttribute(self::DATE);
    }

    /**
     *  Getter for time
     *
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->getAttribute(self::TIME);
    }

    /**
     *  Getter for status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getAttribute(self::STATUS);
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
        return $this->belongsTo(Therapist::class, self::THERAPIST_ID, Therapist::ID_COLUMN);
    }

    /**
     * AvailableTerm location relation
     *
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, self::LOCATION_ID, Location::ID_COLUMN);
    }

    /**
     * AvailableTerm visits relation
     *
     * @return HasMany
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, Visit::AVAILABLE_TERM_ID, self::ID_COLUMN);
    }
}
