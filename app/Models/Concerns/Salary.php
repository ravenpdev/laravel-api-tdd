<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\PaymentTypes;
use App\Models\Employee;
use RuntimeException;

final class Salary extends PaymentType
{
    public function __construct(Employee $employee)
    {
        throw_if(
            $employee->salary === null,
            new RuntimeException('Hourly rate cannot be null')
        );

        parent::__construct($employee);
    }

    public function monthlyAmount(): int
    {
        return (int) ($this->employee->salary / 12);
    }

    public function type(): string
    {
        return PaymentTypes::Salary->value;
    }

    public function amount(): int
    {
        return $this->employee->salary;
    }
}
