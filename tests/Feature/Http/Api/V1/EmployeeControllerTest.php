<?php

declare(strict_types=1);

use App\Models\Employee;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\getJson;

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
        $names = collect(array_values($employees))->pluck('first_name');

        expect($names->toArray())->toBe([
            'elia',
            'kristine',
            'zianna',
        ]);
        expect($meta['perPage'])->toBe(10)
            ->and($meta['currentPage'])->toBe(1);
    });
});
