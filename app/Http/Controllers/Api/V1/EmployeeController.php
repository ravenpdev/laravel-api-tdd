<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpsertEmployeeAction;
use App\Enums\PaymentTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = 10;
        $pageNumber = 1;

        if ($request->has('page') && $request->array('page')) {
            $data = $request->page;

            if (array_key_exists('number', $data)) {
                $pageNumber = (int) $data['number'];
            }

            if (array_key_exists('size', $data)) {
                $pageSize = (int) $data['size'];
            }
        }

        $employees = Employee::query()
            ->when($request->has('filter'), function (Builder $query) use ($request) {
                if (! is_array($request->filter)) {
                    return $query;
                }

                $filters = $request->array('filter');

                foreach ($filters as $key => $value) {
                    $query->orWhereLike($key, $value);
                }

                return $query;
            })
            ->when($request->has('sort'), function (Builder $query) use ($request) {
                $sort = $request->string('sort')->toString();
                if (empty($sort)) {
                    return $query;
                }

                if ($sort[0] === '-') {
                    return $query->orderByDesc(mb_substr($sort, 1));
                }

                if ($sort[0] === '+') {
                    return $query->orderBy(mb_substr($sort, 1));
                }

                return $query->orderBy($sort);
            })
            ->simplePaginate(
                perPage: $pageSize,
                page: $pageNumber
            );

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
