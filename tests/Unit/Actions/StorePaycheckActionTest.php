<?php

declare(strict_types=1);

use App\Actions\StorePaycheckAction;
use App\Enums\PaymentTypes;
use App\Models\Employee;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

test('create paycheck for all employee with payment type salary', function () {
    $employees = Employee::factory()
        ->count(2)
        ->sequence(
            [
                'salary' => 50000 * 100,
                'payment_type' => PaymentTypes::Salary->value,
            ],
            [
                'salary' => 70000 * 100,
                'payment_type' => PaymentTypes::Salary->value,
            ]
        )
        ->create();

    $action = app(StorePaycheckAction::class);
    $action->execute();

    assertDatabaseCount('paychecks', 2);
    assertDatabaseHas('paychecks', [
        'employee_id' => $employees[0]->id,
        'net_amount' => 416666,
    ]);
    assertDatabaseHas('paychecks', [
        'employee_id' => $employees[1]->id,
        'net_amount' => 583333,
    ]);
});
