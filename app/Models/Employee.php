<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentTypes;
use App\Models\Concerns\PaymentType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $id
 * @property-read string $department_id
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read string $last_name
 * @property-read string $job_title
 * @property-read PaymentType $payment_type
 * @property-read int $salary
 * @property-read int $hourly_rate
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 */
final class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory, HasUlids;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function paychecks(): HasMany
    {
        return $this->hasMany(Paycheck::class);
    }

    public function timelogs(): HasMany
    {
        return $this->hasMany(Timelog::class);
    }

    // protected function casts(): array
    // {
    //     return [
    //         'payment_type' => PaymentTypes::class,
    //     ];
    // }

    protected function paymentType(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => PaymentTypes::from($value)
                ->makePaymentType($this),
        );
    }
}
