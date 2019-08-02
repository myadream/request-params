<?php

namespace LittleSuperman\RequestParams\Contracts\DynamicQuery;

use Illuminate\Database\Eloquent\Builder;

/**
 * 模型解析器
 *
 * @package LittleSuperman\RequestParams\Contracts\DynamicQuery
 */
interface ModelParser
{
    /**
     * 启动
     *
     * @param Builder $builder
     * @param array   $model
     * @return ModelParser
     */
    public function boot(Builder $builder, array $model): self;

    /**
     * 获取搜索字段
     *
     * @return array
     */
    public function getSearchField(): array;
}
