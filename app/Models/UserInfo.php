<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInfo extends Model
{
    protected $table = 'user_info';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'gender',
        'birthdate',
        'birth_place',
        'national',
        'religion',
        'hometown',
        'identity',
        'identity_date',
        'identity_place',
        'tax_code',
        'phone',
        'address',
        'household',
        'bank_account',
        'bank',
        'start_working_date',
        'working_place',
        'note',
        'company_name',
        'department_name',
        'unit_name',
        'headquarter_name',
        'position_name',
        'concurrent_position_name',
        'department_id',
        'company_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'gender' => 'integer',
        'birthdate' => 'datetime',
        'identity_date' => 'datetime',
        'start_working_date' => 'datetime',
    ];

    public const GENDER_MALE = 1;

    public const GENDER_FEMALE = 0;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isMale(): bool
    {
        return (int) $this->gender === self::GENDER_MALE;
    }

    public function isFemale(): bool
    {
        return (int) $this->gender === self::GENDER_FEMALE;
    }
}
