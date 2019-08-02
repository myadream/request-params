<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

/**
 * Trait Query
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
trait Query {

    /**
     * 加载
     *
     * @return void
     */
    public static function bootQuery(): void
    {
        static::addGlobalScope(new QueryableScope());
    }

    /**
     * 获取动态查询指字段
     *
     * @return array
     */
    public function getSearchField(): array
    {
        return $this->searchField ?? [];
    }
}