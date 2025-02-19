<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PaymentTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpsertEmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'departmentId' => ['bail', 'required', 'string'],
            'firstName' => ['bail', 'required', 'string'],
            'lastName' => ['bail', 'required', 'string'],
            'jobTitle' => ['bail', 'required', 'string'],
            'paymentType' => ['bail', 'required', Rule::enum(PaymentTypes::class)],
            'salary' => [
                'bail',
                'nullable',
                'numeric',
                'min:1',
                Rule::excludeIf(fn () => 'paymentType' === 'hourly_rate'),
            ],
            'hourlyRate' => [
                'bail',
                'nullable',
                'numeric',
                'min:1',
                Rule::excludeIf(fn () => 'paymentType' === 'salary'),
            ],
        ];
    }
}
