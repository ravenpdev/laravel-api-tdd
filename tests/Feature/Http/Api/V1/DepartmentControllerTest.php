<?php

declare(strict_types=1);

use App\Models\Department;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

describe('Get All Department', function () {
    it('should an empty array if no department', function () {
        $response = getJson(route('api.v1.departments.index'));
        $response->assertStatus(Response::HTTP_OK);

        $deparments = $response->json('departments');

        expect($deparments)->toHaveCount(0);
    });

    it('should return all departments', function () {
        Department::factory(5)->create();

        $response = getJson(route('api.v1.departments.index'));
        $response->assertStatus(Response::HTTP_OK);

        $deparments = $response->json('departments');
        expect($deparments)->toHaveCount(5);
    });
});

describe('Create Department', function () {
    it('should create a department', function (string $name, string $description) {
        $response = postJson(route('api.v1.departments.store'), [
            'name' => $name,
            'description' => $description,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $department = $response->json('department');
        expect($department['name'])->toBe($name)
            ->and($department['description'])->toBe($description);
    })->with([
        [
            'name' => 'Development',
            'description' => 'Awesome developers across the board',
        ],
    ]);

    it('should validate name', function (?string $name) {
        $response = postJson(route('api.v1.departments.store'), [
            'name' => $name,
            'description' => 'this is the description',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    })->with(['', null, 'tt']);

    it('should validate description', function (?string $description) {
        $response = postJson(route('api.v1.departments.store'), [
            'name' => 'hello',
            'description' => $description,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    })->with(['', null, 'tt']);
});

describe('Get single department', function () {
    it('should return a department', function () {
        $department = Department::factory()->create(
            attributes: [
                'name' => 'Developer',
                'description' => 'Software Development',
            ]
        )->refresh();

        $response = getJson(route('api.v1.departments.show', ['department' => $department]));

        $response->assertStatus(Response::HTTP_OK);
        $data = $response->json('department');

        expect($data['id'])->toBe($department->id);
    });

    it('should return 404 not found', function () {
        $response = getJson(route('api.v1.departments.show', ['department' => 1]));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
});

describe('Update department', function () {
    it('should update a department', function (string $name, string $description) {
        $department = Department::factory()->create();

        $response = putJson(route('api.v1.departments.update', ['department' => $department]), [
            'name' => $name,
            'description' => $description,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $department->refresh();

        expect($department->name)->toBe($name)
            ->and($department->description)->toBe($description);
    })->with([
        ['name' => 'updated name', 'description' => 'updated description'],
    ]);

    it('should validate name', function (?string $name) {
        $department = Department::factory()->create();

        putJson(route('api.v1.departments.update', ['department' => $department]), [
            'name' => $name,
            'description' => $department->description,
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['name']);
    })->with([
        '',
        null,
        'tt',
    ]);

    it('should validate description', function (?string $description) {
        $department = Department::factory()->create();

        putJson(route('api.v1.departments.update', ['department' => $department]), [
            'name' => $department->name,
            'description' => $description,
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertInvalid(['description']);
    })->with([
        '',
        null,
        'tt',
    ]);
});

describe('Delete department', function () {
    it('should delete a department', function () {
        $department = Department::factory()->create();
        Department::factory(4)->create();

        $response = deleteJson(route('api.v1.departments.destroy', ['department' => $department]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        expect(Department::query()->get())->toHaveCount(4);
    });

    it('should return 404 not found', function () {
        Department::factory(4)->create();

        $response = deleteJson(route('api.v1.departments.destroy', ['department' => 'abc']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        expect(Department::query()->get())->toHaveCount(4);
    });
});
