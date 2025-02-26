<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Concerns\HourlyRate;
use App\Models\Concerns\PaymentType;
use App\Models\Concerns\Salary;
use App\Models\Employee;

enum PaymentTypes: string
{
    case Salary = 'salary';
    case HourlyRate = 'hourly_rate';

    public function makePaymentType(Employee $employee): PaymentType
    {
        return match ($this) {
            self::Salary => new Salary($employee),
            self::HourlyRate => new HourlyRate($employee)
        };
    }
}
