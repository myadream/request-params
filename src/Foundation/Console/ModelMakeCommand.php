<?php

namespace LittleSuperman\RequestParams\Foundation\Console;

use Illuminate\Support\Str;
use Illuminate\Foundation\Console\ModelMakeCommand as SuperModelMakeCommand;

/**
 * 重写mold
 * 
 * @package LittleSuperman\RequestParams\Foundation\Console
 */
class ModelMakeCommand extends SuperModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model';

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel', '{TABLE}'],
            [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel(), $this->getTable()],
            $stub
        );

        return $this;
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = $this->getTable();

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call('customize:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * 获取表名称
     *
     * @return string
     */
    protected function getTable(): string
    {
        return Str::snake(class_basename($this->argument('name')));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('pivot')) {
            return __DIR__.'/stubs/pivot.model.stub';
        }

        return __DIR__.'/stubs/model.stub';
    }
}
