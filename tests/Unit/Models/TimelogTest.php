<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Timelog;

test('to array', function () {
    $timelog = Timelog::factory()->create();

    expect(array_keys($timelog->toArray()))->toEqualCanonicalizing([
        'id',
        'employee_id',
        'minutes',
        'started_at',
        'stopped_at',
        'created_at',
        'updated_at',
    ]);
});

test('timelog belongs to a employee', function () {
    $employee = Employee::factory()->create();
    $timelog = Timelog::factory([
        'employee_id' => $employee->id,
    ])->create();

    expect($timelog->employee)->toBeInstanceOf(Employee::class);
});
