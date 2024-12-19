<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasUserstamps;

    const TABLE_NAME = 'users';

    const ID_COLUMN = 'id';
    const USER_FIRST_NAME = 'firstName';
    const USER_LAST_NAME = 'lastName';
    const USER_EMAIL = 'email';
    const USER_PHONE = 'phone';
    const USER_PASSWORD = 'password';
    const THERAPIST_RELATION = 'therapist';
    const VISITS_RELATION = 'visits';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const DELETED_BY = 'deleted_by';

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
        self::USER_FIRST_NAME,
        self::USER_LAST_NAME,
        self::USER_PHONE,
        self::USER_EMAIL,
        self::USER_PASSWORD,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        self::USER_PASSWORD,
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        self::USER_PASSWORD => 'hashed',
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
     *  Getter for fistName
     *
     * @return string|null
     */
    public function getUserFirstName(): ?string
    {
        return $this->getAttribute(self::USER_FIRST_NAME);
    }

    /**
     *  Getter for lastName
     *
     * @return string|null
     */
    public function getUserLastName(): ?string
    {
        return $this->getAttribute(self::USER_LAST_NAME);
    }

    /**
     *  Getter for phone
     *
     * @return string|null
     */
    public function getUserPhone(): ?string
    {
        return $this->getAttribute(self::USER_PHONE);
    }

    /**
     *  Getter for email
     *
     * @return string|null
     */
    public function getUserEmail(): ?string
    {
        return $this->getAttribute(self::USER_EMAIL);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, self::CREATED_BY, User::ID_COLUMN);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, self::UPDATED_BY, User::ID_COLUMN);
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, self::DELETED_BY, User::ID_COLUMN);
    }

    /**
     * User therapist relation
     *
     * @return HasOne
     */
    public function therapist(): HasOne
    {
        return $this->hasOne(Therapist::class, Therapist::USER_ID, self::ID_COLUMN);
    }

    /**
     * User visits relation
     *
     * @return HasMany
     */

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, Visit::VISIT_USER_ID, self::ID_COLUMN);
    }
}
