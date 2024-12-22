<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;
    use HasUserstamps;

    const TABLE_NAME = 'visits';

    const ID_COLUMN = 'id';
    const USER_ID = 'user_id';
    const AVAILABLE_TERM_ID = 'availableTerm_id';
    const STATUS = 'status';
    const USER_RELATION = 'user';
    const AVAILABLE_TERM_RELATION = 'availableTerm';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    const STATUS_PENDING = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_CANCELED = 2;

    const STATUS_ENUM = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
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
        self::USER_ID,
        self::AVAILABLE_TERM_ID,
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
     *  Getter for userId
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->getAttribute(self::USER_ID);
    }

    /**
     *  Getter for availableTermId
     *
     * @return int|null
     */
    public function getAvailableTermId(): ?int
    {
        return $this->getAttribute(self::AVAILABLE_TERM_ID);
    }

    /**
     *  Getter for status
     *
     * @return int|null
     */
    public function getStatus(): ?int
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
     * Visit user relation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID, User::ID_COLUMN);
    }

    /**
     * Visit availableTerm relation
     *
     * @return BelongsTo
     */
    public function availableTerm(): BelongsTo
    {
        return $this->belongsTo(AvailableTerm::class, self::AVAILABLE_TERM_ID, AvailableTerm::ID_COLUMN);
    }
}
