<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\PaymentTypes;

final class HourlyRate extends PaymentType
{
    public function monthlyAmount(): int
    {
        return $this->employee->hourly_rate * 8 * 5;
    }

    public function type(): string
    {
        return PaymentTypes::HourlyRate->value;
    }

    public function amount(): int
    {
        return $this->employee->hourly_rate;
    }
}
