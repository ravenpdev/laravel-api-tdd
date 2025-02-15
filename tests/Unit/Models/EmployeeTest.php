<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\Employee;

test('to array', function () {
    $employee = Employee::factory()->create()->refresh();

    expect(array_keys($employee->toArray()))->toEqualCanonicalizing([
        'id',
        'department_id',
        'first_name',
        'last_name',
        'job_title',
        'payment_type',
        'salary',
        'hourly_rate',
        'created_at',
        'updated_at',
    ]);
});

it('should create an employee with a payment_type salary', function () {
    $employee = Employee::factory()->salary()->create()->refresh();

    expect($employee->payment_type)->toBe('salary')
        ->and($employee->salary)->not()->toBeNull();
});

it('should create an employee with a payment_type hourly_rate', function () {
    $employee = Employee::factory()->hourly()->create()->refresh();

    expect($employee->payment_type)->toBe('hourly_rate')
        ->and($employee->hourly_rate)->not()->toBeNull();
});

test('employee belongs to a department', function () {
    $employee = Employee::factory()->create();

    expect($employee->department)->toBeInstanceOf(Department::class);
});
