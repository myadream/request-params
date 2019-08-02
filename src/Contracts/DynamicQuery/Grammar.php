<?php

namespace LittleSuperman\RequestParams\Contracts\DynamicQuery;

use \Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * 语法
 *
 * @package LittleSuperman\RequestParams\Contracts\DynamicQuery
 */
interface Grammar
{
    /**
     * 闭包where
     *
     * @param Builder $builder
     * @param Closure $closure
     * @return Builder
     */
    public function closureWhere(Builder $builder, Closure $closure): Builder;
    
    /**
     * 闭包or where
     *
     * @param Builder $builder
     * @param Closure $closure
     * @return Builder
     */
    public function closureOrWhere(Builder $builder, Closure $closure): Builder;
    
    /**
     * where
     *
     * @param Builder $builder
     * @param string  $key
     * @param         $value
     * @param string  $option
     * @return Builder
     */
    public function where(Builder $builder, string $key, $value, string $option = '='): Builder;
    
    /**
     * or where
     *
     * @param Builder $builder
     * @param string  $key
     * @param         $value
     * @param string  $option
     * @return Builder
     */
    public function orWhere(Builder $builder, string $key, $value, string $option = '='): Builder;
    
    /**
     * 大于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param float   $value
     * @return Builder
     */
    public function whereGreater(Builder $builder, string $key, float $value): Builder;

    /**
     * 小于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param float   $value
     * @return Builder
     */
    public function whereLess(Builder $builder, string $key, float $value): Builder;

    /**
     * 等于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     * @return Builder
     */
    public function whereEqual(Builder $builder, string $key, string $value): Builder;

    /**
     * 不等于查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     * @return Builder
     */
    public function whereNotEqual(Builder $builder, string $key, string $value): Builder;

    /**
     * in 查询
     *
     * @param Builder $builder
     * @param string  $key
     * @param array   $value
     * @return Builder
     */
    public function whereIn(Builder $builder, string $key, array $value): Builder;

    /**
     * not in 查询
     *
     * @param sBuilder $builder
     * @param tring    $key
     * @param array    $value
     * @return Builder
     */
    public function whereNotIn(Builder $builder, string $key, array $value): Builder;

    /**
     * 模糊搜索
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     * @return Builder
     */
    public function whereLike(Builder $builder, string $key, string $value): Builder;

    /**
     * 不等模糊搜索
     *
     * @param Builder $builder
     * @param string  $key
     * @param string  $value
     * @return Builder
     */
    public function whereNotLike(Builder $builder, string $key, string $value): Builder;

    /**
     * 区间搜索
     *
     * @param Builder $builder
     * @param string  $key 名称
     * @param array   $value
     * @return Builder
     */
    public function whereBetwwen(Builder $builder, string $key, array $value): Builder;

    /**
     * 不等于区间搜索
     *
     * @param Builder $builder
     * @param string  $key
     * @param array   $value
     * @return Builder
     */
    public function whereNotBetwwen(Builder $builder, string $key, array $value): Builder;

    /**
     * 升序
     *
     * @param Builder $builder
     * @param string  $key
     * @return Builder
     */
    public function orderByAsc(Builder $builder, string $key): Builder;

    /**
     * 降序
     *
     * @param Builder $builder
     * @param string  $key
     * @return Builder
     */
    public function orderByDesc(Builder $builder, string $key): Builder;

    /**
     * 页数
     *
     * @param Builder $builder
     * @param int     $page
     * @return Builder
     */
    public function page(Builder $builder, int $page): Builder;

    /**
     * 条数
     *
     * @param Builder $builder
     * @param int     $limit
     * @return Builder
     */
    public function limit(Builder $builder, int $limit): Builder;
}
