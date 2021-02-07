<?php

namespace LittleSuperman\RequestParams\Contracts\DynamicQuery;

use Illuminate\Database\Eloquent\Builder;

/**
 * 数据语法
 *
 * @package LittleSuperman\RequestParams\Contracts\DynamicQuery
 */
interface GrammarParser
{
    /**
     * 启动
     *
     * @param Builder $builder
     * @param array   $modleSearchField
     * @param array   $searchField
     * @return GrammarParser
     */
    public function boot(Builder $builder, array $modleSearchField, array $searchField): self;
    
    /**
     * 设置搜索字段
     *
     * @param array $field
     * @return Grammar
     */
    public function setSearchField(array $field): self;

    /**
     * 设置解析器
     *
     * @param Builder $builder
     * @return Grammar
     */
    public function setBuilder(Builder $builder): self;

    /**
     * 获取解析器
     *
     * @return Builder
     */
    public function getBuilder(): Builder; 
}
