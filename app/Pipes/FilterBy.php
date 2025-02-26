<?php

declare(strict_types=1);

namespace App\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

final class FilterBy
{
    /**
     * Create a new class instance.
     */
    public function __construct(public readonly array $fields, public readonly array $filters)
    {
        //
    }

    /**
     * @param  class-string  $modelClass
     */
    public function handle(string $modelClass, Closure $next)
    {
        $model = app()->make($modelClass);
        $queryBuilder = $model->query()
            ->when($this->filters, function (Builder $builder): Builder {
                foreach ($this->fields as $key) {
                    if (! array_key_exists($key, $this->filters)) {
                        continue;
                    }

                    $value = $this->filters[$key];
                    $builder->orWhereLike($key, "%$value%");
                }

                return $builder;
            });

        return $next($queryBuilder);
    }
}
