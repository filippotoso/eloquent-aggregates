<?php

namespace FilippoToso\Eloquent\Aggregates\Concerns;

use FilippoToso\Eloquent\Aggregates\AggregatesBuilder;

trait HasAggregates
{
    public function newEloquentBuilder($query)
    {
        return new AggregatesBuilder($query);
    }
}