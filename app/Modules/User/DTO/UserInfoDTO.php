<?php

declare(strict_types=1);

namespace App\Modules\User\DTO;

use App\Models\UserInfo;

final readonly class UserInfoDTO
{
    /**
     * Dữ liệu công khai (có thể hiển thị cho người khác).
     *
     * @return array<string, mixed>
     */
    public static function safe(UserInfo $info): array
    {
        return [
            'code' => $info->code,
            'gender' => $info->gender,
            'company_name' => $info->company_name,
            'department_name' => $info->department_name,
            'unit_name' => $info->unit_name,
            'headquarter_name' => $info->headquarter_name,
            'position_name' => $info->position_name,
        ];
    }

    /**
     * Dữ liệu nhạy cảm (chỉ user tự xem hoặc admin).
     *
     * @return array<string, mixed>
     */
    public static function sensitive(UserInfo $info): array
    {
        return [
            'birthdate' => $info->birthdate?->toIso8601String(),
            'birth_place' => $info->birth_place,
            'national' => $info->national,
            'religion' => $info->religion,
            'hometown' => $info->hometown,
            'identity' => $info->identity,
            'identity_date' => $info->identity_date?->toIso8601String(),
            'identity_place' => $info->identity_place,
            'tax_code' => $info->tax_code,
            'phone' => $info->phone,
            'address' => $info->address,
            'household' => $info->household,
            'bank_account' => $info->bank_account,
            'bank' => $info->bank,
            'start_working_date' => $info->start_working_date?->toIso8601String(),
            'working_place' => $info->working_place,
            'note' => $info->note,
            'concurrent_position_name' => $info->concurrent_position_name,
            'department_id' => $info->department_id,
            'company_id' => $info->company_id,
        ];
    }
}
