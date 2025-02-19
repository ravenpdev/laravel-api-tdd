<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\ValueObjects\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'departmentId' => $this->department_id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'jobTitle' => $this->job_title,
            'paymentType' => [
                'type' => $this->payment_type->type(),
                'amount' => Money::from($this->payment_type->amount())->toArray(),
            ],
            'salary' => $this->salary,
            'hourlyRate' => $this->hourly_rate,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
