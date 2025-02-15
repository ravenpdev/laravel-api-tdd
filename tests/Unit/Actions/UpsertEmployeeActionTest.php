<?php

declare(strict_types=1);

use App\Actions\UpsertEmployeeAction;
use App\Models\Department;
use App\Models\Employee;

use function Pest\Laravel\assertDatabaseCount;

it('should create an employee', function () {
    $department = Department::factory()->create();
    $action = app(UpsertEmployeeAction::class);

    $employee = $action->execute(
        new Employee(),
        departmentId: $department->id,
        firstName: 'raven',
        lastName: 'paragas',
        jobTitle: 'developer',
        paymentType: 'salary',
        salary: 40_000,
    );

    assertDatabaseCount('employees', 1);
    expect($employee->first_name)->toBe('raven')
        ->and($employee->last_name)->toBe('paragas')
        ->and($employee->job_title)->toBe('developer')
        ->and($employee->payment_type)->toBe('salary')
        ->and($employee->salary)->toBe(40_000)
        ->and($employee->hourly_rate)->toBeNull();
});

it('should update an employee', function () {
    $department = Department::factory()->create([
        'name' => 'Developer',
    ]);
    $developer = Employee::factory()->create()->refresh();
    $action = app(UpsertEmployeeAction::class);

    $employee = $action->execute(
        $developer,
        departmentId: $department->id,
        firstName: 'raven',
        lastName: 'paragas',
        jobTitle: 'developer',
        paymentType: 'salary',
        salary: 40_000
    );

    assertDatabaseCount('employees', 1);
    expect($employee->id)->toBe($developer->id)
        ->and($employee->department->id)->toBe($department->id)
        ->and($employee->first_name)->toBe('raven')
        ->and($employee->last_name)->toBe('paragas')
        ->and($employee->job_title)->toBe('developer')
        ->and($employee->payment_type)->toBe('salary')
        ->and($employee->salary)->toBe(40_000)
        ->and($employee->hourly_rate)->toBeNull();
});

it('should update employee payment_type from salary to hourly_rate', function () {
    $department = Department::factory()->create([
        'name' => 'Developer',
    ]);
    $developer = Employee::factory()->salary()->create([
        'first_name' => 'raven',
        'last_name' => 'paragas',
        'job_title' => 'developer',
    ]);
    $action = app(UpsertEmployeeAction::class);

    $employee = $action->execute(
        employee: $developer,
        departmentId: $department->id,
        firstName: $developer->first_name,
        lastName: $developer->last_name,
        jobTitle: $developer->job_title,
        paymentType: 'hourly_rate',
        hourlyRate: 20,
    );

    $employee->refresh();
    assertDatabaseCount('employees', 1);
    expect($employee->id)->toBe($developer->id)
        ->and($employee->first_name)->toBe('raven')
        ->and($employee->last_name)->toBe('paragas')
        ->and($employee->payment_type)->toBe('hourly_rate')
        ->and($employee->hourly_rate)->toBe(20)
        ->and($employee->salary)->toBeNull();
});

it('should update employee payment_type from hourly_rate to salary', function () {
    $department = Department::factory()->create([
        'name' => 'Developer',
    ]);
    $developer = Employee::factory()->hourly()->create([
        'first_name' => 'raven',
        'last_name' => 'paragas',
        'job_title' => 'developer',
    ]);
    $action = app(UpsertEmployeeAction::class);

    $employee = $action->execute(
        employee: $developer,
        departmentId: $department->id,
        firstName: $developer->first_name,
        lastName: $developer->last_name,
        jobTitle: $developer->job_title,
        paymentType: 'salary',
        salary: 40_000,
    );

    $employee->refresh();
    assertDatabaseCount('employees', 1);
    expect($employee->id)->toBe($developer->id)
        ->and($employee->first_name)->toBe('raven')
        ->and($employee->last_name)->toBe('paragas')
        ->and($employee->payment_type)->toBe('salary')
        ->and($employee->salary)->toBe(40_000)
        ->and($employee->hourly_rate)->toBeNull();
});
