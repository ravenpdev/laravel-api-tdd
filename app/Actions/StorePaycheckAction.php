<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Employee;

final class StorePaycheckAction
{
    public function execute(): void
    {
        Employee::all()->each(function (Employee $employee) {
            $employee->paychecks()->create([
                'net_amount' => $employee->payment_type->monthlyAmount(),
                'payed_at' => now(),
            ]);
        });
    }
}
