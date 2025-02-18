<?php

declare(strict_types=1);

use App\enums\PaymentTypes;
use App\Models\Department;
use App\Models\Employee;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

describe('Employee index', function () {
    it('should return all employees', function () {
        Employee::factory(count: 25)->create();
        $response = getJson(route('api.v1.employees.index'));
        $response->assertStatus(Response::HTTP_OK);

        $employees = $response->json('employees');
        $meta = $response->json('meta');

        expect($employees)->toHaveCount(10);
        expect($meta['perPage'])->toBe(10)
            ->and($meta['currentPage'])->toBe(1);
    });

    it('should return number of employees base on perPage', function () {
        Employee::factory(count: 25)->create();
        $response = getJson(route('api.v1.employees.index', ['page[number]' => 2, 'page[size]' => 15]));
        $response->assertStatus(Response::HTTP_OK);

        $employees = $response->json('employees');
        $meta = $response->json('meta');

        expect($employees)->toHaveCount(10);
        expect($meta['perPage'])->toBe(15)
            ->and($meta['currentPage'])->toBe(2);
    });

    it('should filter employees', function () {
        Employee::factory(count: 30)->create();
        Employee::factory()->create([
            'first_name' => 'rave',
            'last_name' => 'paragas',
        ]);
        Employee::factory()->create([
            'first_name' => 'raven',
            'last_name' => 'paragas',
        ]);
        Employee::factory()->create([
            'first_name' => 'kristine',
            'last_name' => 'paragas',
        ]);

        $response = getJson(route('api.v1.employees.index', ['filter[last_name]' => 'paragas', 'filter[first_name]' => 'rav']));
        $response->assertStatus(Response::HTTP_OK);
        $employees = $response->json('employees');
        $meta = $response->json('meta');

        expect($employees)->toHaveCount(3);
        expect($meta['perPage'])->toBe(10)
            ->and($meta['currentPage'])->toBe(1);
    });

    it('should sort employees', function () {
        Employee::factory()->create([
            'first_name' => 'zianna',
        ]);
        Employee::factory()->create([
            'first_name' => 'elia',
        ]);
        Employee::factory()->create([
            'first_name' => 'kristine',
        ]);

        $response = getJson(route('api.v1.employees.index', ['sort' => '+first_name']));
        $response->assertStatus(Response::HTTP_OK);
        $employees = $response->json('employees');
        $meta = $response->json('meta');
        $names = collect(array_values($employees))->pluck('firstName');

        expect($names->toArray())->toBe([
            'elia',
            'kristine',
            'zianna',
        ]);
        expect($meta['perPage'])->toBe(10)
            ->and($meta['currentPage'])->toBe(1);
    });
});

describe('Employee show', function () {
    it('should get an employee', function () {
        $developer = Employee::factory()->create([
            'first_name' => 'raven',
            'last_name' => 'paragas',
            'job_title' => 'php/laravel developer',
        ]);

        $response = getJson(route('api.v1.employees.show', ['employee' => $developer]));
        $response->assertStatus(Response::HTTP_OK);
        $employee = $response->json('employee');

        expect($employee['id'])->toBe($developer->id)
            ->and($employee['firstName'])->toBe('raven')
            ->and($employee['lastName'])->toBe('paragas')
            ->and($employee['jobTitle'])->toBe('php/laravel developer');
    });

    it('should return 404 not found', function () {
        $response = getJson(route('api.v1.employees.show', ['employee' => '1234']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
});

describe('Employee store', function () {
    it('should create an employee', function () {
        $department = Department::factory()->create();

        $response = postJson(route('api.v1.employees.store'), [
            'departmentId' => $department->id,
            'firstName' => 'raven',
            'lastName' => 'paragas',
            'jobTitle' => 'developer',
            'paymentType' => 'salary',
            'salary' => 40_000,
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $employee = $response->json('employee');

        expect($employee['departmentId'])->toBe($department->id)
            ->and($employee['firstName'])->toBe('raven')
            ->and($employee['lastName'])->toBe('paragas')
            ->and($employee['jobTitle'])->toBe('developer')
            ->and($employee['paymentType'])->toBe('salary')
            ->and($employee['salary'])->toBe(40_000);
    });

    it('should require departmentId', function () {
        $response = postJson(route('api.v1.employees.store'), [
            'firstName' => 'raven',
            'lastName' => 'paragas',
            'jobTitle' => 'developer',
            'paymentType' => 'salary',
            'salary' => 40_000,
        ]);

        $response->assertInvalid(['departmentId'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    it('should require firstName', function () {
        $department = Department::factory()->create();
        $response = postJson(route('api.v1.employees.store'), [
            'departmentId' => $department->id,
            'lastName' => 'paragas',
            'jobTitle' => 'developer',
            'paymentType' => 'salary',
            'salary' => 40_000,
        ]);

        $response->assertInvalid(['firstName'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    it('should require lastName', function () {
        $department = Department::factory()->create();
        $response = postJson(route('api.v1.employees.store'), [
            'departmentId' => $department->id,
            'firstName' => 'raven',
            'jobTitle' => 'developer',
            'paymentType' => 'salary',
            'salary' => 40_000,
        ]);

        $response->assertInvalid(['lastName'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    it('should require jobTitle', function () {
        $department = Department::factory()->create();
        $response = postJson(route('api.v1.employees.store'), [
            'departmentId' => $department->id,
            'firstName' => 'raven',
            'lastName' => 'paragas',
            'paymentType' => 'salary',
            'salary' => 40_000,
        ]);

        $response->assertInvalid(['jobTitle'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    it('should require paymentType', function (?string $paymentType) {
        $department = Department::factory()->create();
        $response = postJson(route('api.v1.employees.store'), [
            'departmentId' => $department->id,
            'firstName' => 'raven',
            'lastName' => 'paragas',
            'paymentType' => $paymentType,
            'jobTitle' => 'developer',
            'salary' => 40_000,
        ]);

        $response->assertInvalid(['paymentType'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    })->with([
        '',
        'hello',
        null,
    ]);

    it('should require salary if paymentType is salary', function (?string $salary) {
        $department = Department::factory()->create();
        $response = postJson(route('api.v1.employees.store'), [
            'departmentId' => $department->id,
            'firstName' => 'raven',
            'lastName' => 'paragas',
            'paymentType' => 'salary',
            'salary' => $salary,
            'jobTitle' => 'developer',
        ]);

        $response->assertInvalid(['salary'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    })->with([
        'hello',
        '0',
    ]);

    it('should require hourlyRate if paymentType is hourly_rate', function (?string $hourlyRate) {
        $department = Department::factory()->create();
        $response = postJson(route('api.v1.employees.store'), [
            'departmentId' => $department->id,
            'firstName' => 'raven',
            'lastName' => 'paragas',
            'paymentType' => 'hourly_rate',
            'hourlyRate' => $hourlyRate,
            'jobTitle' => 'developer',
        ]);

        $response->assertInvalid(['hourlyRate'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    })->with([
        'hello',
        '0',
    ]);
});

describe('Employee update', function () {
    it('should update an employee', function () {
        $department = Department::factory()->create([
            'name' => 'Developer',
        ]);
        $employee = Employee::factory()->salary()->create([
            'department_id' => $department->id,
        ]);

        $response = putJson(route('api.v1.employees.update', ['employee' => $employee]), [
            'departmentId' => $employee->department_id,
            'firstName' => 'raven',
            'lastName' => 'paragas',
            'jobTitle' => $employee->job_title,
            'paymentType' => 'hourly_rate',
            'hourlyRate' => 20,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $employee->refresh();

        expect($employee->first_name)->toBe('raven')
            ->and($employee->last_name)->toBe('paragas')
            ->and($employee->payment_type)->toBe(PaymentTypes::HourlyRate)
            ->and($employee->salary)->toBeNull();
    });
});
