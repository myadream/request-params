<?php


namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

/**
 * 处理前端的请求参数转换为对应的模板
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
trait GrammarParserSymobls
{
    /**
     * 获取存在名称
     *
     * @param string $name
     * @return string
     */
    protected function getOptionName(string $name = ''): string
    {
        $config = $this->getConfigSymobls('options');

        if (empty($config[$name])) {
            return '';
        }

        return $config[$name];
    }

    /**
     * 获取排序方法
     *
     * @param string $key
     * @return string
     */
    protected function getOrderOptionFun(string $key): string
    {
        $key = strtolower($key);

        if (array_key_exists($key, $this->orderSymbols)) {
            return $this->orderSymbols[$key];
        } else {
            return $this->orderSymbols['asc'];
        }
    }

    /**
     * 获取where方法
     *
     * @param string $key
     * @return string
     */
    protected function getWhereOptionFun(string $key): string
    {
        if (array_key_exists($key, $this->whereSymbols)) {
            return $this->whereSymbols[$key];
        } else {
            return $this->whereSymbols['='];
        }
    }

    /**
     * 获取方法名称
     *
     * @param string $naem
     * @return string
     * @throws \Exception
     */
    protected function getWhereFunName(string $naem): string
    {
        if (empty($naem) || empty($this->getWhereSymbols($naem))) {
            throw new \Exception('为空');
        }

        return $this->getWhereOptionFun($this->getWhereSymbols($naem));
    }

    /**
     * 处理默认where模板
     *
     * @return GrammarParserSymobls
     */
    protected function handleWhereSymobls(): self
    {
        $this->whereSymbols = Collection::make($this->whereSymbols)->mapWithKeys(function ($item, $key) {
            return [$this->getWhereSymbols($key) => $item];
        })->toArray();

        return $this;
    }

    /**
     * 处理正则匹配
     *
     * @return GrammarParserSymobls
     */
    protected function handleSubgroups(): self
    {
        $this->subgroups = str_replace(
            "{symbols}",
            Collection::make($this->getWhereSymbold())->keys()->implode('|'),
            $this->subgroups
        );

        return $this;
    }

    /**
     * 处理默认order模板
     *
     * @return GrammarParserSymobls
     */
    protected function handleOrderSymobls(): self
    {
        $this->orderSymbols = Collection::make($this->orderSymbols)->mapWithKeys(function ($item, $key) {
            return [$this->getOrderSymbols($key) => $item];
        })->toArray();

        return $this;
    }

    /**
     * 获取where条件模板
     *
     * @param string|null $name
     * @return mixed
     */
    protected function getWhereSymbols(string $name = null)
    {
        //合并
        $whereSymobls = array_merge($this->getDefaultWhereSymobls(), $this->getConfigSymobls('whereSymbols'));

        if (is_null($name)) {
            return $whereSymobls;
        }

        return $whereSymobls[$name] ?? '';
    }

    /**
     * 获取默认order模板
     *
     * @param string|null $name
     * @return mixed
     */
    protected function getOrderSymbols(string $name = null)
    {
        $orderSymobls = array_merge($this->getDefaultOrderSymoble(), $this->getConfigSymobls('orderSymbols'));

        if (is_null($name)) {
            return $orderSymobls;
        }

        return $orderSymobls[$name] ?? '';
    }

    /**
     * 获取配置模板
     *
     * @param string $name
     * @return mixed
     */
    protected function getConfigSymobls(string $name = '')
    {
        return Config::get("dynamicQuery.{$name}") ?? [];
    }

    /**
     * 获取默认模板
     *
     * @return GrammarParserSymobls
     */
    protected function getDefaultWhereSymobls(): array
    {
        return [
            'and' => '&',
            'or' => '|',
            'equal' => '=',
            'notEqual' => '!',
            'greater' => '>',
            'less' => '<',
            'in' => 'in',
            'notIn' => 'notIn',
            'betwwen' => '<>',
            'notBetwwen' => '><',
            'like' => '~',
            'notLike' => '!=',
        ];
    }

    /**
     * 获取默认排序模板
     *
     * @return array
     */
    protected function getDefaultOrderSymoble(): array
    {
        return [
            'asc' => 'asc',
            'desc' => 'desc',
        ];
    }
}