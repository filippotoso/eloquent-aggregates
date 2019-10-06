<?php

namespace FilippoToso\Eloquent\Aggregates;

use Illuminate\Database\Eloquent\Builder;

class AggregatesBuilder extends Builder
{
    use Concerns\QueriesAggregates;
}