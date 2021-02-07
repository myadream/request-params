<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Database\Eloquent\Builder;
use LittleSuperman\RequestParams\Contracts\DynamicQuery\ModelParser as ModelParserContracts;
use LittleSuperman\RequestParams\Contracts\DynamicQuery\ParamsParser as ParamsParserContracts;
use LittleSuperman\RequestParams\Contracts\DynamicQuery\GrammarParser as GrammarParserContracts;

/**
 * 动态查询
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
class DynamicQuery
{
    use Macroable, RequestParamsParser;

    /**
     * 模型解析器
     *
     * @var ModelParserContracts
     */
    protected $modelParser;

    /**
     * 请求参数解析
     *
     * @var ParamsParserContracts
     */
    protected $paramsParser;

    /**
     * 语法解析
     *
     * @var GrammarParserContracts
     */
    protected $grammarParser;

    /**
     * 启动解析器
     *
     * @param Builder $builder
     * @param array   $model
     * @return Builder
     */
    public function boot(Builder $builder, array $model = []): Builder
    {
        $model = $this->instanceModel($model);

        //验证是否有配置动态查询字段
        if (!$this->verify($builder, $model)) {
            return $builder;
        }

        return $this->loadParser()->parser($builder, $model)->getResult();
    }

    /**
     * 实例modl
     *
     * @param array $model
     * @return array
     */
    protected function instanceModel(array $model): array
    {
        return Collection::make($model)->map(function ($itme) {
            if ($itme instanceof Builder) {
                //从构建器获取
                return $itme;
            } elseif (is_string($itme)) {
                //重新获取新的查询
                return App::make($itme)->newModelQuery();
            } else {
                throw new \Exception("解析异常 {$itme}");
            }
        })->toArray();
    }

    /**
     * 验证是否有可用
     *
     * @param Builder $builder
     * @param array   $model
     * @return bool
     */
    protected function verify(Builder $builder, array $model): bool
    {
        if (empty($builder->getModel()->getSearchField()) && empty($model)) {
            //查询是否有设置搜索字段
            return false;
        } elseif ($this->getModelSearchFieldStatus($model)) {
            //查询其余模型中是否设置了
            return false;
        } elseif (empty($this->getDefaultRequestParams())) {
            //查询是否有请求参数
            return false;
        }

        return true;
    }

    /**
     * 获取其余模型状态
     *
     * @param array $model
     * @return bool
     */
    protected function getModelSearchFieldStatus(array $model): bool
    {
        return Collection::make($model)
            ->reject(function ($item) {
                return empty($item->getModel()->getSearchField());
            })->isEmpty();
    }

    /**
     * 加载解析器
     *
     * @return DynamicQuery
     */
    protected function loadParser(): self
    {
        $this->setModelParser($this->getDefaultModelParser())
            ->setParamsParser($this->getDefaultParamsParser())
            ->setGrammarParser($this->getDefaultGrammarParser());
        
        return $this;
    }

    /**
     * 获取默认参模型析器
     *
     * @return ModelParserContracts
     */
    public function getDefaultModelParser(): ModelParserContracts
    {
        return App::make(Config::get('dynamicQuery.defaultModelParser'));
    }

    /**
     * 获取默认参数解析器
     *
     * @return ParamsParserContracts
     */
    public function getDefaultParamsParser(): ParamsParserContracts
    {
        return App::make(Config::get('dynamicQuery.defaultParamsParser'));
    }

    /**
     * 获取默认语法解析器
     *
     * @return GrammarParserContracts
     */
    public function getDefaultGrammarParser(): GrammarParserContracts
    {
        return App::make(Config::get('dynamicQuery.defaultGrammarParser'));
    }

    /**
     * 设置模型解析器
     *
     * @param ModelParserContracts $modelParser
     *
     * @return DynamicQuery
     */
    public function setModelParser(ModelParserContracts $modelParser): self
    {
        $this->modelParser = $modelParser;

        return $this;
    }

    /**
     * 获取模型解析器
     *
     * @return ModelParserContracts
     */
    public function getModelParser(): ModelParserContracts
    {
        return $this->modelParser;
    }

    /**
     * 设置参数解析器
     *
     * @param ParamsParserContracts $paramsParser
     * @return DynamicQuery
     */
    public function setParamsParser(ParamsParserContracts $paramsParser): self
    {
        $this->paramsParser = $paramsParser;

        return $this;
    }

    /**
     * 获取参数解析器
     *
     * @return ParamsParserContracts
     */
    public function getParamsParser(): ParamsParserContracts
    {
        return $this->paramsParser;
    }

    /**
     * 语法解析器
     *
     * @param GrammarParserContracts $grammarParser
     * @return DynamicQuery
     */
    public function setGrammarParser(GrammarParserContracts $grammarParser): self
    {
        $this->grammarParser = $grammarParser;

        return $this;
    }

    /**
     * 获取语法解析器
     *
     * @return GrammarParserContracts
     */
    public function getGrammarParser(): GrammarParserContracts
    {
        return $this->grammarParser;
    }

    /**
     * 解析
     *
     * @param Builder $builder
     * @param array   $model
     * @return DynamicQuery
     */
    protected function parser(Builder $builder, array $model): self
    {
        $this->modelParser($builder, $model)
            ->paramsParser($builder, $model)
            ->grammarParser($builder, $model);

        return $this;
    }

    /**
     * 模型解析
     *
     * @param Builder $builder
     * @param array   $model
     * @return DynamicQuery
     */
    protected function modelParser(Builder $builder, array $model): self
    {
        $this->modelParser->boot($builder, $model);

        return $this;
    }

    /**
     * 请求参数解析
     *
     * @param Builder $builder
     * @param array   $model
     * @return DynamicQuery
     */
    protected function paramsParser(Builder $builder, array $model): self
    {
        $this->paramsParser->boot($this->getModelParser()->getSearchField());

        return $this;
    }

    /**
     * 语法解析器
     *
     * @param Builder $builder
     * @param array   $model
     * @return DynamicQuery
     */
    protected function grammarParser(Builder $builder, array $model): self
    {
        $this->grammarParser->boot(
            $builder,
            $this->modelParser->getSearchField(),
            $this->paramsParser->getResult()
        );

        return $this;
    }

    /**
     * 获取动态查询
     *
     * @return Builder
     */
    protected function getResult(): Builder
    {
        return $this->grammarParser->getBuilder();
    }
}
