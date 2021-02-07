<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

/**
 * 请求参数解析
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
trait RequestParamsParser
{
    /**
     * 获取请求参数
     *
     * @param string $name
     * @return mixed
     */
    public function getRequestParams(string $name = '')
    {
        $params = $this->getDefaultRequestParams();

        if (empty($name)) {
            return $params;
        } elseif (strstr($name, '.')) {
            return $this->searchField($params, $name);
        } else {
            return $params[$name] ?? '';
        }
    }

    /**
     * 获取默认请求参数
     *
     * @return array
     */
    public function getDefaultRequestParams(): array
    {
        $params = Request::input(Config::get('dynamicQuery.key'));

        if (empty($params)) {
            return [];
        } elseif (is_string($params)) {
            return json_decode($params, true);
        }

        return [];
    }

    /**
     * 搜索字段key的值
     *
     * @param array $params 需要搜索参数
     * @param       $name
     *
     * @return array
     */
    private function searchField(array $params, $name)
    {
        $temp = [];

        //获取指定值
        Collection::make(explode('.', $name))->each(function ($item, $key) use (&$temp, $params) {
            if (empty($temp)) {
                $temp = $params[$key] ?? [];
            } elseif (!empty($temp[$key])) {
                $temp = $temp[$key];
            }

            return ;
        });

        return $temp;
    }
}
