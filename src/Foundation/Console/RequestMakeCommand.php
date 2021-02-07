<?php

namespace LittleSuperman\RequestParams\Foundation\Console;

use Illuminate\Foundation\Console\RequestMakeCommand as SuperRequestMakeCommand;

class RequestMakeCommand extends SuperRequestMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'customize:request';

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
