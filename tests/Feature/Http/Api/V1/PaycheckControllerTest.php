<?php

declare(strict_types=1);

use App\Enums\PaymentTypes;
use App\Models\Employee;
use App\Models\Paycheck;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('should return all employee paychecks', function () {
    $employee = Employee::factory()->create();
    Paycheck::factory(count: 10)->create([
        'employee_id' => $employee->id,
    ]);

    $response = getJson(route('api.v1.employees.paychecks.index', ['employee' => $employee]));
    $response->assertStatus(Response::HTTP_OK);
});

it('should return employee paycheck', function () {
    $employee = Employee::factory()->create();
    $pcheck = Paycheck::factory()->create([
        'employee_id' => $employee,
    ]);

    $response = getJson(route('api.v1.employees.paychecks.show', ['employee' => $employee, 'paycheck' => $pcheck]));
    $response->assertStatus(Response::HTTP_OK);

    $paycheck = $response->json('paycheck');

    expect($paycheck['id'])->toBe($pcheck->id);
});

it('should return 404 not found', function () {
    $employee = Employee::factory()->create();

    $response = getJson(
        route('api.v1.employees.paychecks.show', [
            'employee' => $employee,
            'paycheck' => '1',
        ])
    );
    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

it('should create paychecks for salary employees', function () {
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
        )->create();

    $response = postJson(
        route(
            'api.v1.paychecks.store',
            ['employee' => $employees[0]->id]
        )
    );
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    assertDatabaseHas('paychecks', [
        'employee_id' => $employees[0]->id,
        'net_amount' => 416666,
    ]);

    assertDatabaseHas('paychecks', [
        'employee_id' => $employees[1]->id,
        'net_amount' => 583333,
    ]);
});
