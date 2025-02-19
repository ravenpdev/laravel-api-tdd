<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\PaymentTypes;

final class Salary extends PaymentType
{
    public function monthlyAmount(): int
    {
        return $this->employee->salary / 12;
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
