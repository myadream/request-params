<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use \Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;
use LittleSuperman\RequestParams\Contracts\DynamicQuery\Grammar as GrammarContracts;

/**
 * mysql 语法
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
class Grammar implements GrammarContracts
{
    /**
     * 闭包where
     *
     * @param Builder $builder
     * @param Closure $closure
     *
     * @return Builder
     */
    public function closureWhere(Builder $builder, Closure $closure): Builder
    {
        $builder->where($closure);
        
        return $builder;
    }

    /**
     * 闭包or where
     *
     * @param Builder $builder
     * @param Closure $closure
     *
     * @return Builder
     */
    public function closureOrWhere(Builder $builder, Closure $closure): Builder
    {
        $builder->orWhere($closure);
        
        return $builder;
    }

    /**
     * where
     *
     * @param Builder $builder
     * @param string  $key
     * @param mixed   $value
     * @param string  $option
     *
     * @return Builder
     */
    public function where(Builder $builder, string $key, $value, string $option = '='): Builder
    {
        $builder->where($key, $option, $key);

        return $builder;
    }

    /**
     * or where
     *
     * @param Builder $builder
     * @param string  $key
     * @param mixed   $value
     * @param string  $option
     *
     * @return Builder
     */
    public function orWhere(Builder $builder, string $key, $value, string $option = '='): Builder
    {
        $builder->orWhere($key, $option, $value);
        
        return $builder;
    }

    /**
     * 大于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param float   $value
     *
     * @return Builder
     */
    public function whereGreater(Builder $builder, string $key, float $value): Builder
    {
        $builder->where($key, '>', $value);

        return $builder;
    }

    /**
     * 小于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param float   $value
     *
     * @return Builder
     */
    public function whereLess(Builder $builder, string $key, float $value): Builder
    {
        $builder->where($key, '<', $value);

        return $builder;
    }

    /**
     * 等于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     *
     * @return Builder
     */
    public function whereEqual(Builder $builder, string $key, string $value): Builder
    {
        $builder->where($key, '=', $value);

        return $builder;
    }

    /**
     * 不等于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     *
     * @return Builder
     */
    public function whereNotEqual(Builder $builder, string $key, string $value): Builder
    {
        $builder->where($key, '!=', $value);

        return $builder;
    }

    /**
     * in 查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param array   $value
     *
     * @return Builder
     */
    public function whereIn(Builder $builder, string $key, array $value): Builder
    {
        $builder->whereIn($key, $value);

        return $builder;
    }

    /**
     * not in 查询
     *
     * @param Builder $builder
     * @param string    $key
     * @param array    $value
     *
     * @return Builder
     */
    public function whereNotIn(Builder $builder, string $key, array $value): Builder
    {
        $builder->whereNotIn($key, $value);

        return $builder;
    }

    /**
     * 模糊搜索
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     *
     * @return Builder
     */
    public function whereLike(Builder $builder, string $key, string $value): Builder
    {
        $builder->where($key, 'like', "{$value}%");

        return $builder;
    }

    /**
     * 不等模糊搜索
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     *
     * @return Builder
     */
    public function whereNotLike(Builder $builder, string $key, string $value): Builder
    {
        $builder->where($key, 'not like', "{$value}%");

        return $builder;
    }

    /**
     * 区间搜索
     *
     * @param Builder $builder
     * @param string  $key
     * @param array   $value
     *
     * @return Builder
     */
    public function whereBetween(Builder $builder, string $key, array $value): Builder
    {
        $builder->whereBetween($key, $value);

        return $builder;
    }

    /**
     * 不等于区间搜索
     *
     * @param Builder $builder
     * @param string  $key
     * @param array   $value
     *
     * @return Builder
     */
    public function whereNotBetween(Builder $builder, string $key, array $value): Builder
    {
        $builder->whereNotBetween($key, $value);

        return $builder;
    }

    /**
     * 升序
     *
     * @param Builder $builder
     * @param string  $key
     *
     * @return Builder
     */
    public function orderByAsc(Builder $builder, string $key): Builder
    {
        $builder->orderBy($key, 'asc');

        return $builder;
    }

    /**
     * 降序
     *
     * @param Builder $builder
     * @param string  $key
     *
     * @return Builder
     */
    public function orderByDesc(Builder $builder, string $key): Builder
    {
        $builder->orderBy($key, 'desc');

        return $builder;
    }

    /**
     * 页数
     *
     * @param Builder $builder
     * @param int     $page
     *
     * @return Builder
     */
    public function page(Builder $builder, int $page): Builder
    {
        Request::merge(['page' => $page]);

        return $builder;
    }

    /**
     * 条数
     *
     * @param Builder $builder
     * @param int     $limit
     *
     * @return Builder
     */
    public function limit(Builder $builder, int $limit): Builder
    {
        $builder->getModel()->setPerPage($limit);

        return $builder;
    }
}
