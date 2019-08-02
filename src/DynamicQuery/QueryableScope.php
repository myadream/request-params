<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * 动态查询作用域
 *
 * @author gaoj <gaojianban@gmail.com>
 */
class QueryableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model   $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        //
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    public  function extend(Builder $builder)
    {
        $builder->macro('queryable', function (Builder $builder, ...$model) {
            return (new DynamicQuery())->boot($builder, $this->handleModel($model));
        });
    }

    /**
     * 处理model类型
     *
     * @param $model
     * @return array
     */
    protected function handleModel($model): array
    {
        if (empty($model)) {
            return [];
        } elseif (is_string($model)) {
            return [$model];
        } elseif (is_array($model)) {
            return $model;
        } else {
            return [];
        }
    }
}
