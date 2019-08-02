<?php

namespace LittleSuperman\RequestParams\Foundation\Console;

use Illuminate\Foundation\Console\RequestMakeCommand as SupportRequestMakeCommand;

class RequestMakeCommand extends SupportRequestMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'customize:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自定义创建请求资源';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/request.stub';
    }
}
