<?php

namespace LittleSuperman\RequestParams\Routing\Console;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Routing\Console\ControllerMakeCommand as SupportControllerMakeCommand;

/**
 * 重置
 *
 * @package LittleSuperman\RequestParams\Routing\Console
 */
class ControllerMakeCommand extends SupportControllerMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'customize:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自定义创建资源控制器和表单验证';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = null;

        if ($this->option('parent')) {
            $stub = '/stubs/controller.nested.stub';
        } elseif ($this->option('model')) {
            $stub = '/stubs/controller.model.stub';
        } elseif ($this->option('invokable')) {
            $stub = '/stubs/controller.invokable.stub';
        } elseif ($this->option('resource')) {
            $stub = '/stubs/controller.stub';
        }

        if ($this->option('api') && is_null($stub)) {
            $stub = '/stubs/controller.api.stub';
        } elseif ($this->option('api') && ! is_null($stub) && ! $this->option('invokable')) {
            $stub = str_replace('.stub', '.api.stub', $stub);
        }

        $stub = $stub ?? '/stubs/controller.plain.stub';

        return __DIR__.$stub;
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('resource')) {
            $replace = $this->createRequest($name)->buildRequestReplacements($name);
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the replacements for a request controller.
     *
     * @return array
     */
    protected function buildRequestReplacements(string $name): array
    {
        $name = "{$this->rootNamespace()}Http\\Requests\\{$this->getNowInputName()}\\";

        return [
            'DummyRequest' => $this->getNamespace($name)
        ];
    }

    /**
     * 获取输入名称
     *
     * @return string
     */
    protected function getNowInputName(): string
    {
        return str_replace('/', '\\', $this->getNameInput());
    }


    /**
     * create requrest
     *
     * @return ControllerMakeCommand
     */
    protected function createRequest(string $name): self
    {
        //创建表单验证
        $this->call('customize:request', [
            'name' => "{$this->getNameInput()}/CreateRequest"
        ]);

        //修改表单验证
        $this->call('customize:request', [
            'name' => "{$this->getNameInput()}/UpdateRequest"
        ]);
        
        return $this;
    }
}
