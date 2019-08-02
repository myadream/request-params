<?php

namespace LittleSuperman\RequestParams\DynamicQuery;

use Illuminate\Support\Collection;

/**
 * 特殊方法
 *
 * @package LittleSuperman\RequestParams\DynamicQuery
 */
trait GrammarParserConversion
{
    /**
     * 正则匹配where
     *
     * @param string $where
     * @param string $subgroups
     * @return array
     */
    protected function pregeMatchWhere(string $where, string $subgroups): array
    {
        $matches = [];

        //匹配
        preg_match_all($subgroups, $where, $matches);

        return [
            'type' => $matches['type'],
            'originField' => $matches['field'],
            'field' => $this->pregeMatchWhereFieldMapping($matches['field']),
            'expression' => $matches['expression'],
        ];;
    }

    /**
     * 字段映射
     *
     * @param array $field
     * @return array
     */
    protected function pregeMatchWhereFieldMapping(array $field): array
    {
        return Collection::make($field)->map(function ($item) {
            $mappingField = $this->mappingField($item);

            //验证是否存在搜索字段中
            if (empty($mappingField)) {
                throw new \Exception("{$item} 不存在搜索字段中");
            }

            return $mappingField;
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
     * 参数
     *
     * @param string $method
     * @param array  $val
     * @return array
     */
    protected function conversionWhere(string $method, array $val): array
    {
        if ($method == $this->getWhereFunName('equal')) {
            $method = $this->getWhereFunName('in');
            $val    = [$val];

        } elseif ($method == $this->getWhereFunName('notEqual')) {
            $method = $this->getWhereFunName('notIn');
            $val    = [$val];
        } elseif (in_array($method, [$this->getWhereFunName('like'), $this->getWhereFunName('notLike')])) {
            
        } else {
            $val = [$val];
        }

        return [$method, $val];
    }
}
