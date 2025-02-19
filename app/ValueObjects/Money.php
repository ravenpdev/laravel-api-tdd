<?php

declare(strict_types=1);

namespace App\ValueObjects;

final class Money
{
    public function __construct(private readonly int $valueInCents) {}

    public static function from(int $valueInCents): self
    {
        return new self($valueInCents);
    }

    public function toDollars(): string
    {
        return '$'.number_format($this->valueInCents / 100, 2);
    }

    public function toCents(): int
    {
        return $this->valueInCents;
    }

    public function toArray(): array
    {
        return [
            'cents' => $this->toCents(),
            'dollars' => $this->toDollars(),
        ];
    }
}
