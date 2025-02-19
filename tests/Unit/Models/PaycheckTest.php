<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Paycheck;

test('to array', function () {
    $paycheck = Paycheck::factory()->create();

    expect(array_keys($paycheck->toArray()))->toEqualCanonicalizing([
        'id',
        'employee_id',
        'net_amount',
        'payed_at',
        'created_at',
        'updated_at',
    ]);
});

test('paychecks belongs to employee', function () {
    $paycheck = Paycheck::factory()->create();

    expect($paycheck->employee)->toBeInstanceOf(Employee::class);
});
