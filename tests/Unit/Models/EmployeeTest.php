<?php

declare(strict_types=1);

use App\Models\Concerns\HourlyRate;
use App\Models\Concerns\Salary;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Paycheck;
use App\Models\Timelog;

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

    expect($employee->payment_type)->toBeInstanceOf(Salary::class)
        ->and($employee->payment_type->type())->toBe('salary')
        ->and($employee->salary)->not()->toBeNull();
});

it('should create an employee with a payment_type hourly_rate', function () {
    $employee = Employee::factory()->hourly()->create()->refresh();

    expect($employee->payment_type)->toBeInstanceOf(HourlyRate::class)
        ->and($employee->payment_type->type())->toBe('hourly_rate')
        ->and($employee->hourly_rate)->not()->toBeNull();
});

test('employee belongs to a department', function () {
    $employee = Employee::factory()->create();

    expect($employee->department)->toBeInstanceOf(Department::class);
});

test('employee has many pachecks', function () {
    $employee = Employee::factory()->create();
    Paycheck::factory(count: 10)->create([
        'employee_id' => $employee->id,
    ]);

    expect($employee->paychecks)->toHaveCount(10);
});

test('employee has many timelogs', function () {
    $employee = Employee::factory()->create()->fresh();
    Timelog::factory(count: 10, state: [
        'employee_id' => $employee->id,
    ])->create()->fresh();

    expect($employee->timelogs)->toHaveCount(10)
        ->and($employee->timelogs()->first()->employee_id)->toBe($employee->id);
});
