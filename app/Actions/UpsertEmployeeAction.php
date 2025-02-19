<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

final class UpsertEmployeeAction
{
    public function execute(
        Employee $employee,
        string $departmentId,
        string $firstName,
        string $lastName,
        string $jobTitle,
        string $paymentType,
        ?int $salary,
        ?int $hourlyRate
    ): Employee {
        return DB::transaction(fn () => Employee::query()->updateOrCreate(
            [
                'id' => $employee->id,
            ],
            [
                'department_id' => $departmentId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'job_title' => $jobTitle,
                'payment_type' => $paymentType,
                'salary' => $salary,
                'hourly_rate' => $hourlyRate,
            ]
        ));
    }
}
