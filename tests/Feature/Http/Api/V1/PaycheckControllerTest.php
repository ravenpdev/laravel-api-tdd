<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Paycheck;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\getJson;

describe('Paycheck index', function () {
    it('should return all employee paychecks', function () {
        $employee = Employee::factory()->create();
        Paycheck::factory(count: 10)->create([
            'employee_id' => $employee->id,
        ]);

        $response = getJson(route('api.v1.employees.paychecks.index', ['employee' => $employee]));
        $response->assertStatus(Response::HTTP_OK);
    });
});
