<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpsertEmployeeAction;
use App\Enums\PaymentTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Pipes\FilterBy;
use App\Pipes\SortBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Response;

final class EmployeeController extends Controller
{
    public function index(Request $request): Response
    {
        $employees = app(Pipeline::class)
            ->send(Employee::class)
            ->through([
                new FilterBy(
                    fields: ['first_name', 'last_name'],
                    filters: $request->array('filter')
                ),
                new SortBy(keyword: $request->string('sort')->toString()),
            ])
            ->then(function (Builder $builder) use ($request) {
                return $builder->simplePaginate(
                    perPage: $request->integer('page.size', 10),
                    page: $request->integer('page.number', 1)
                );
            });

        return response()->json(
            [
                'employees' => EmployeeResource::collection($employees),
                'meta' => [
                    'perPage' => $employees->perPage(),
                    'currentPage' => $employees->currentPage(),
                ],
            ],
        )->setStatusCode(Response::HTTP_OK);
    }

    public function show(Employee $employee): Response
    {
        return response()->json(
            [
                'employee' => new EmployeeResource($employee),
            ]
        )->setStatusCode(Response::HTTP_OK);
    }

    public function store(UpsertEmployeeRequest $request, UpsertEmployeeAction $upsertEmployeeAction): Response
    {
        $employee = $upsertEmployeeAction->execute(
            employee: new Employee(),
            departmentId: $request->string('departmentId')->toString(),
            firstName: $request->string('firstName')->toString(),
            lastName: $request->string('lastName')->toString(),
            jobTitle: $request->string('jobTitle')->toString(),
            paymentType: $request->enum('paymentType', PaymentTypes::class)->value,
            salary: $request->integer('salary'),
            hourlyRate: $request->integer('hourlyRate'),
        );

        return response()->json(
            data: [
                'employee' => new EmployeeResource($employee),
            ]
        )->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpsertEmployeeRequest $request, Employee $employee, UpsertEmployeeAction $upsertEmployeeAction): Response
    {
        $upsertEmployeeAction->execute(
            employee: $employee,
            departmentId: $request->string('departmentId')->toString(),
            firstName: $request->string('firstName')->toString(),
            lastName: $request->string('lastName')->toString(),
            jobTitle: $request->string('jobTitle')->toString(),
            paymentType: $request->string('paymentType')->toString(),
            salary: $request->salary,
            hourlyRate: $request->hourlyRate
        );

        return response()->noContent();
    }

    public function destroy(Employee $employee): Response
    {
        $employee->delete();

        return response()->noContent();
    }
}
