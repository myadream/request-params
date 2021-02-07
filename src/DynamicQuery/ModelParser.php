<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Database\Eloquent\Builder;
use LittleSuperman\RequestParams\Contracts\DynamicQuery\ModelParser as ModelParserContracts;

/**
 * 模型解析器
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
class ModelParser implements ModelParserContracts
{
    use Macroable;

    /**
     * 可用搜索字段
     *
     * @var array
     */
    protected $searchField = [];
    
    /**
     * 启动
     *
     * @param Builder $builder
     * @param array   $model
     * @return ModelParserContracts
     */
    public function boot(Builder $builder, array $model): ModelParserContracts
    {
        $this->setSearchField($builder)
            ->setSearchField($model);

        return $this;
    }

    /**
     * 获取搜索字段
     *
     * @return array
     */
    public function getSearchField(): array
    {
        return $this->searchField;
    }

    /**
     * 设置参数解析
     *
     * @param Builder|array $params
     * @return ModelParser
     */
    protected function setSearchField($params): self
    {
        if ($params instanceof Builder) {
            $this->parser($params);
        } elseif (is_array($params)) {
            //解析其他model可搜索字段
            Collection::make($params)->each(function ($item) {
                $this->parser($item);
            });
        }

        return $this;
    }

    /**
     * 解析
     *
     * @param Builder $builder
     * @return void
     */
    protected function parser(Builder $builder): void
    {
        $params = $builder->getModel()->getSearchField();

        if (!empty($params) && is_array($params)) {
            $searchField = Collection::make($params)->mapWithKeys(function ($values, $key) use ($builder) {
                return ["{$builder->getModel()->getTable()}.{$key}" => "{$builder->getModel()->getTable()}.{$values}"];
            })->toArray();

            $this->searchField = array_merge($this->searchField, $searchField);
        }
    }
}
