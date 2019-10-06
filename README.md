# Eloquent Aggregates

A simple trait to add support for aggregate functions in Eloquent models.

## Requirements

- PHP 7.2+
- Laravel 5.7+

## Installing

Use Composer to install it:

```
composer require filippo-toso/eloquent-aggregates
```

## How to use it

Add the `FilippoToso\Eloquent\Aggregates\Concerns\HasAggregates` trait to your models. 
Then you can use the following methods:

```
// Get the sum of all the amount fields in the transactions relationship
$users = App\Users::withSum('amount', 'transactions')->get();

// Get the max of all the amount fields in the transactions relationship
$users = App\Users::withMax('amount', 'transactions')->get();

// Get the min of all the amount fields in the transactions relationship
$users = App\Users::withMin('amount', 'transactions')->get();

// Get the average of all the amount fields in the transactions relationship
$users = App\Users::withAvg('amount', 'transactions')->get();
```

You can also add constraints to the relationship query:

```
$users = App\Users::withAvg('amount', ['transactions' => function ($query) {
    // Include only the transaction created in the last seven days
    $query->whereDate('created_at', '>=', Carbon\Carbon::today()->subDays(7));
}])->get();
```

## Already extending the Eloquent Builder?

If you are already extending the `Illuminate\Database\Eloquent\Builder`, just add the trait `FilippoToso\Eloquent\Aggregates\Concerns\QueriesAggregates` to your Builder class. 
