<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use LittleSuperman\RequestParams\Contracts\DynamicQuery\GrammarParser as GrammarParserContracts;

/**
 * 语法解析器
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
class GrammarParser implements GrammarParserContracts
{
    use GrammarParserSymobls, GrammarParserConversion;

    /**
     * 语法
     *
     * @var Grammar
     */
    protected $grammar;

    /**
     * 搜索字段
     *
     * @var array
     */
    protected $searchField;

    /**
     * 获取模型搜索字段
     *
     * @var array
     */
    protected $modelSearchField;

    /**
     * 构建器
     *
     * @var Builder
     */
    protected $builder;

    /**
     * 匹配模板
     *
     * @var string
     */
    protected $subgroups = "/(?P<type>(\||&)?)(?P<field>(\w+))(\[)?(?P<expression>({symbols})+)?(\])?/";

    /**
     * 条件模板
     *
     * @var array
     */
    protected $whereSymbols = [
        'equal' => 'whereEqual',
        'notEqual' => 'whereNotEqual',
        'greater' => 'whereGreater',
        'less' => 'whereLess',
        'in' => 'whereIn',
        'notIn' => 'whereNotIn',
        'betwwen' => 'whereBetwwen',
        'notBetwwen' => 'whereNotBetwwen',
        'like' => 'whereLike',
        'notLike' => 'whereNotLike',
    ];

    /**
     * 排序模板
     *
     * @var array
     */
    protected $orderSymbols = [
        //'对外参数' => '对应方法'
        'asc' => 'orderByAsc',
        'desc' => 'orderByDesc',
    ];

    /**
     * GrammarParser constructor.
     *
     * @param Grammar $grammar
     */
    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;

        $this->handleSymbols();
    }

    /**
     * 处理模板
     *
     * @return void
     */
    protected function handleSymbols(): void
    {
        $this->handleOrderSymobls()->handleWhereSymobls()->handleSubgroups();
    }

    /**
     * 启动
     *
     * @param Builder $builder
     * @param array   $field
     * @return GrammarParserContracts
     */
    public function boot(Builder $builder, array $modleSearchField, array $searchField): GrammarParserContracts
    {
        $this->setBuilder($builder)
            ->setSearchField($searchField)
            ->setModelSearchField($modleSearchField)
            ->parser();

        return $this;
    }

    /**
     * 解析
     *
     * @return GrammarParser
     */
    protected function parser(): self
    {
        $this->whereParser()
            ->orderParser()
            ->pageParser();

        return $this;
    }

    /**
     * where 条件解析
     *
     * @return GrammarParser
     */
    protected function whereParser(): self
    {
        $where = [];

        if (!empty($where = $this->searchField[$this->getOptionName('where')])) {

            Collection::make($where)->each(function ($item, $key) {
                $result = $this->pregeMatchWhere($key, $this->subgroups);

                $this->tectonic($result, $item);
            });
        }

        return $this;
    }

    /**
     * 构建
     *
     * @param array $preproccess
     * @param mixed $value
     * @return void
     */
    protected function tectonic(array $preproccess, $value): void
    {
        $type = $preproccess['type'][0];

        //判断是否为or 和 and 关系
        if ($type === $this->getWhereSymbols('or')) {
            $this->setBuilder(
                $this->grammar->closureOrWhere($this->getBuilder(), function (Builder $query) use ($preproccess, $value) {
                    return $this->callQueryMethod($query, $preproccess, $value);
                })
            );
        } else {
            $this->setBuilder(
                $this->grammar->closureWhere($this->getBuilder(), function (Builder $query) use ($preproccess, $value) {
                    return $this->callQueryMethod($query, $preproccess, $value);
                })
            );
        }
    }

    /**
     * 调用方法
     *
     * @param Builder $query
     * @param array   $preproccess
     * @param mixed   $value
     * @return Builder
     */
    protected function callQueryMethod(Builder $query, array $preproccess, $value): Builder
    {
        foreach ($preproccess['originField'] as $key => $item) {
            $method = $this->getWhereOptionFun($preproccess['expression'][$key]);
            $tempValue = $value;

            //处理特殊条件
            if (is_array($tempValue)) {
                [$method, $tempValue] = $this->conversionWhere($method, $tempValue);
            } else {
                $tempValue = [$tempValue];
            }

            //设置参数
            foreach ($preproccess['field'][$key] as $k => $v) {

                //设置
                foreach ($tempValue as $result) {
                    if ($preproccess['type'][$key] === $this->getWhereSymbols('or')) {
                        $query = $query->orWhere(function (Builder $query) use ($v, $method, $result) {
                            $query = $this->callGrammar($query, $method, $v, $result);
                        });
                    } else {
                        $query = $this->callGrammar($query, $method, $v, $result);
                    }
                }
            }
        }

        return $query;
    }

    /**
     * 排序解析
     *
     * @return GrammarParser
     */
    protected function orderParser(): self
    {
        $sort = $this->searchField[$this->getOptionName('order')] ?? [];

        //判断是否有开启排序
        if (!empty($sort)) {
            Collection::make($sort)->each(function ($item, $key) {
                //获取操作方法
                $option = $this->getOrderOptionFun($item);

                //设置排序
                Collection::make($this->mappingField($key))
                    ->each(function ($field) use ($option, $item) {
                        $this->setBuilder($this->callGrammar($this->getBuilder(), $option, $field, $item));
                    });
            });
        }

        return $this;
    }

    /**
     * 调用语法
     *
     * @param Builder     $builder
     * @param string      $option
     * @param string|null $key
     * @param null        $value
     * @return Builder
     */
    protected function callGrammar(Builder $builder, string $option, string $key = null, $value = null): Builder
    {
        if (!is_null($key) && is_null($value)) {
            return $this->grammar->{$option}($builder, $key);
        } elseif (is_null($key) && !is_null($value)) {
            return $this->grammar->{$option}($builder, $value);
        } else {
            return $this->grammar->{$option}($builder, $key, $value);
        }
    }

    /**
     * 解析字段
     *
     * @param string $field
     * @return array
     */
    protected function mappingField(string $field): array
    {
        return Collection::make($this->modelSearchField)->map(function ($item, $key) use ($field) {
            $key = $this->exploadName($key);

            if ($key === $field) {
                return $item;
            }

            return '';
        })->reject(function ($item) {
            return empty($item);
        })->toArray();
    }

    /**
     * 分页解析
     *
     * @return GrammarParser
     */
    protected function pageParser(): self
    {
        //设置当前页
        if (!empty($page = $this->searchField[$this->getOptionName('page')])) {
            $this->setBuilder($this->callGrammar($this->getBuilder(), 'page', null, $page));
        }

        //设置最大页数
        if (!empty($limit = $this->searchField[$this->getOptionName('limit')])) {
            //判断是否大于最大条数
            if ($limit > (int)$this->getConfigSymobls('maxLimit')) {
                $limit = $this->getConfigSymobls('maxLimit');
            }

            //调用语法
            $this->setBuilder($this->callGrammar($this->getBuilder(), 'limit', null, $limit));

        }

        return $this;
    }

    /**
     * 设置搜索字段
     *
     * @param array $field
     * @return GrammarParserContracts
     */
    public function setSearchField(array $field): GrammarParserContracts
    {
        $this->searchField = $field;

        return $this;
    }

    /**
     * 设置解析器
     *
     * @param Builder $builder
     * @return GrammarParserContracts
     */
    public function setBuilder(Builder $builder): GrammarParserContracts
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * 获取解析器
     *
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * 获取where模板
     *
     * @return array
     */
    public function getWhereSymbold(): array
    {
        return $this->whereSymbols;
    }

    /**
     * 设置where模板
     *
     * @param array $symobld
     * @return GrammarParser
     */
    public function setWhereSymobld(array $symobld): self
    {
        $this->getWhereSymbold = $symobld;

        return $this;
    }

    /**
     * 获取排序模板
     *
     * @return array
     */
    public function getOrdeBySymobld(): array
    {
        return $this->orderSymbols;
    }

    /**
     * 设置排序模板
     *
     * @param array $symobld
     * @return GrammarParser
     */
    public function setOrderBySymobld(array $symobld): self
    {
        $this->orderSymbols = $symobld;

        return $this;
    }

    /**
     * 获取模型搜索字段
     *
     * @return array
     */
    public function getModelSearchField(): array
    {
        return $this->modelSearchField;
    }

    /**
     * 设置模型搜索字段
     *
     * @param array $field
     * @return GrammarParser
     */
    public function setModelSearchField(array $field): self
    {
        $this->modelSearchField = $field;

        return $this;
    }
}
