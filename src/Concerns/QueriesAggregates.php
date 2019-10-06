<?php

namespace FilippoToso\Eloquent\Aggregates\Concerns;

use FilippoToso\Eloquent\Aggregates\Exceptions\InvalidAggregateType;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

trait QueriesAggregates
{

    /**
     * Add subselect queries to max the relations.
     *
     * @param  mixed  $relations
     * @param  string $expression
     * @return $this
     */
    public function withMax($relations, $expression)
    {
        return $this->withAggregate('max', $relations, $expression);
    }

    /**
     * Add subselect queries to min the relations.
     *
     * @param  mixed  $relations
     * @param  string $expression
     * @return $this
     */
    public function withMin($relations, $expression)
    {
        return $this->withAggregate('min', $relations, $expression);
    }


    /**
     * Add subselect queries to avg the relations.
     *
     * @param  mixed  $relations
     * @param  string $expression
     * @return $this
     */
    public function withAvg($relations, $expression)
    {
        return $this->withAggregate('avg', $relations, $expression);
    }

    /**
     * Add subselect queries to sum the relations.
     *
     * @param  mixed  $relations
     * @param  string $expression
     * @return $this
     */
    public function withSum($relations, $expression)
    {
        return $this->withAggregate('sum', $relations, $expression);
    }

    /**
     * Add subselect queries to aggregate the relations.
     *
     * @param  string $aggregateType 
     * @param  mixed  $relations
     * @param  string $expression
     * @return $this
     */
    protected function withAggregate($aggregateType, $relations, $expression)
    {
        if (empty($aggregateType) || empty($relations) || empty($expression)) {
            return $this;
        }

        if (!in_array($aggregateType, ['max', 'min', 'avg', 'sum'])) {
            throw new InvalidAggregateType('Invalid aggregate type: ' . $aggregateType);
        }

        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from . '.*']);
        }

        $relations = is_array($relations) ? $relations : [$relations];
        foreach ($this->parseWithRelations($relations) as $name => $constraints) {
            // First we will determine if the name has been aliased using an "as" clause on the name
            // and if it has we will extract the actual relationship name and the desired name of
            // the resulting column. This allows multiple counts on the same relationship name.
            $segments = explode(' ', $name);
            unset($alias);
            if (count($segments) === 3 && Str::lower($segments[1]) === 'as') {
                [$name, $alias] = [$segments[0], $segments[2]];
            }
            $relation = $this->getRelationWithoutConstraints($name);
            // Here we will get the relationship count query and prepare to add it to the main query
            // as a sub-select. First, we'll get the "has" query and use that to get the relation
            // count query. We will normalize the relation name then append _count as the name.
            $query = $relation->getRelationExistenceQuery(
                $relation->getRelated()->newQuery(),
                $this,
                new Expression($aggregateType . '(' . $expression . ')')
            )->setBindings([], 'select');

            $query->callScope($constraints);
            $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();
            if (count($query->columns) > 1) {
                $query->columns = [$query->columns[0]];
            }
            // Finally we will add the proper result column alias to the query and run the subselect
            // statement against the query builder. Then we will return the builder instance back
            // to the developer for further constraint chaining that needs to take place on it.
            $column = $alias ?? Str::snake($name . '_count');
            $this->selectSub($query, $column);
        }
        return $this;
    }

}