<?php

namespace LittleSuperman\RequestParams\Contracts\DynamicQuery;

interface ParamsParser
{
    /**
     * 启动
     *
     * @param  array $field
     * @return ParamsParser
     */
    public function boot(array $field): self;
    
    /**
     * 获取结果
     *
     * @return array
     */
    public function getResult(): array;
}
