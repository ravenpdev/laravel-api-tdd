<?php

declare(strict_types=1);

namespace App\enums;

enum PaymentTypes: string
{
    case Salary = 'salary';
    case HourlyRate = 'hourly_rate';
}
