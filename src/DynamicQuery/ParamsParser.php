<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Traits\Macroable;
use LittleSuperman\RequestParams\Contracts\DynamicQuery\ParamsParser as ParamsParserContracts;

/**
 * 请求参数解析
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
class ParamsParser implements ParamsParserContracts
{
    use Macroable, RequestParamsParser;

    /**
     * 可用搜索字段
     *
     * @var array
     */
    protected $searchField = [];

    /**
     * 启动
     *
     * @param  array $field
     * @return ParamsParserContracts
     */
    public function boot(array $field): ParamsParserContracts
    {
        $this->parser($field);

        return $this;
    }

    /**
     * 获取结果
     *
     * @return array
     */
    public function getResult(): array
    {
        return $this->searchField;
    }

    /**
     * 解析
     *
     * @parms array $field
     * @return void
     */
    protected function parser(array $field): void
    {
        $this->searchField = Collection::make(Config::get('dynamicQuery.options'))->map(function ($option) use ($field) {
            if (strpos($option, 'page') !== false || strpos($option, 'limit') !== false) {
                return $this->handleSpecialOption($option);
            }

            //验证是否存在
            return Collection::make($field)->mapWithKeys(function ($item, $key) use ($option) {
                //拆分名称
                $name = $this->exploadName($key);

                //判断请求参数中的条件是存在模型的可用搜索字段中
                return Collection::make($this->getRequestParams($option))->filter(function ($item, $key) use ($name) {
                    if (strpos($key, $name) !== false) {
                        return true;
                    }

                    return false;
                })->toArray();
            })->toArray();
        })->toArray();
    }

    /**
     * 拆分名称
     *
     * @param string $name
     * @return string
     */
    protected function exploadName(string $name): string
    {
        $name = explode('.', $name);

        return end($name);
    }

    /**
     * 处理特殊操作
     *
     * @param string $name
     * @return int
     */
    protected function handleSpecialOption(string $name): int
    {
        return empty($this->getRequestParams($name)) ? 0 : $this->getRequestParams($name);
    }
}
