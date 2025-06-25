<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use App\Traits\HasUserStamps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasUserstamps;
    use SoftDeletes;

    const TABLE_NAME = 'users';

    const ID_COLUMN = 'id';
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const EMAIL = 'email';
    const AVATAR = 'avatar';
    const PHONE = 'phone';
    const PASSWORD = 'password';
    const THERAPIST_RELATION = 'therapist';
    const VISITS_RELATION = 'visits';
    const REFRESH_TOKEN = 'refresh_token';
    const REFRESH_TOKEN_EXPIRE_DATE = 'refresh_token_expires_at';
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
        self::FIRST_NAME,
        self::LAST_NAME,
        self::PHONE,
        self::EMAIL,
        self::AVATAR,
        self::PASSWORD,
        self::REFRESH_TOKEN,
        self::REFRESH_TOKEN_EXPIRE_DATE,
        'email_verified_at',
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
        self::PASSWORD,
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        self::PASSWORD => 'hashed',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->email_verified_at = null;
            $user->save();
        });
    }

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
        return $this->getAttribute(self::FIRST_NAME);
    }

    /**
     *  Getter for lastName
     *
     * @return string|null
     */
    public function getUserLastName(): ?string
    {
        return $this->getAttribute(self::LAST_NAME);
    }

    /**
     *  Getter for lastName
     *
     * @return string|null
     */
    public function getUserFullName(): ?string
    {
        return $this->getAttribute(self::FIRST_NAME) . " " . $this->getAttribute(self::LAST_NAME);
    }

    /**
     *  Getter for phone
     *
     * @return string|null
     */
    public function getUserPhone(): ?string
    {
        return $this->getAttribute(self::PHONE);
    }

    /**
     *  Getter for email
     *
     * @return string|null
     */
    public function getUserEmail(): ?string
    {
        return $this->getAttribute(self::EMAIL);
    }

    /**
     *  Getter for avatar
     *
     * @return string|null
     */
    public function getUserAvatar(): ?string
    {
        return $this->getAttribute(self::AVATAR);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmail());
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
        return $this->hasMany(Visit::class, Visit::USER_ID, self::ID_COLUMN);
    }
}
