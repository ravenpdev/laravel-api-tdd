<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\Employee;

test('to array', function () {
    $deparment = Department::factory()->create();

    expect(array_keys($deparment->toArray()))->toEqualCanonicalizing([
        'id',
        'name',
        'description',
        'created_at',
        'updated_at',
    ]);
});

test('department has many employee', function () {
    $deparment = Department::factory()->create();
    Employee::factory(count: 10)->create([
        'department_id' => $deparment->id,
    ]);

    expect($deparment->employees)->toHaveCount(10);
});
