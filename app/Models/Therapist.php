<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsTomany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Therapist extends Model
{
    use HasFactory;

    const TABLE_NAME = 'therapists';

    const ID_COLUMN = 'id';
    const USER_ID = 'user_id';
    const USER_RELATION = 'user';
    const AVAILABLE_TERMS_RELATION = 'availableTerms';
    const LOCATIONS_RELATION = 'locations';

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
        self::USER_ID,
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
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->getAttribute(self::USER_ID);
    }

    /**
     * Therapist user relation
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, self::USER_ID, User::ID_COLUMN);
    }

    /**
     * Therapist availableTerms relation
     *
     * @return HasMany
     */
    public function availableTerms(): HasMany
    {
        return $this->hasMany(AvailableTerm::class, AvailableTerm::AVAILABLE_TERM_THERAPIST_ID);
    }

    /**
     * Therapist location relation
     *
     * @return BelongsToMany
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class,
            Location2Therapist::TABLE_NAME,
            Location2Therapist::THERAPIST_ID,
            Location2Therapist::LOCATION_ID);
    }
}
