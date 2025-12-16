<?php

namespace App\Console\Commands;

use App\Facades\VariablesService;
use Binafy\LaravelStub\Facades\LaravelStub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeActionCommand extends Command
{
    protected $signature = 'igu:make-action {model} {--types=ucd} {--force}';

    protected $description = 'Make ACTION classes';

    public function handle(): void
    {
        $model = ucfirst(Str::camel($this->argument('model')));
        $types = $this->option('types');
        $force = $this->option('force');

        $namespace = 'App\\Actions\\' . $model;
        $baseDir = __DIR__ . '/../../../stubs/';

        $to = getcwd() . '/app/Actions/' . $model . '/';

        if (!File::exists($to))
            File::makeDirectory($to, $mode = 0777, true);

        //////

        if (Str::contains($types, 'c')) {
            $name = "Create{$model}";

            if ($force || !File::exists($to . '/' . $name . '.php'))
                LaravelStub::from($baseDir . 'create-action.stub')
                    ->to($to)
                    ->name($name)
                    ->ext('php')
                    ->replaces([
                        'CLASS' => $name,
                        'DATA_CLASS' => "{$name}Data",
                        'NAMESPACE' => $namespace,
                        'MODEL' => $model,
                    ])
                    ->generate();
        }

        if (Str::contains($types, 'u')) {
            $name = "Update{$model}";

            if ($force || !File::exists($to . '/' . $name . '.php'))
                LaravelStub::from($baseDir . 'update-action.stub')
                    ->to($to)
                    ->name($name)
                    ->ext('php')
                    ->replaces([
                        'CLASS' => $name,
                        'DATA_CLASS' => "{$name}Data",
                        'NAMESPACE' => $namespace,
                        'MODEL' => $model,
                        'MODEL_VARIABLE' => Str::camel(Str::snake($model)),
                    ])
                    ->generate();
        }

        if (Str::contains($types, 'd')) {
            $name = "Delete{$model}";

            if ($force || !File::exists($to . '/' . $name . '.php'))
                LaravelStub::from($baseDir . 'delete-action.stub')
                    ->to($to)
                    ->name($name)
                    ->ext('php')
                    ->replaces([
                        'CLASS' => $name,
                        'NAMESPACE' => $namespace,
                        'MODEL' => $model,
                        'MODEL_VARIABLE' => Str::camel(Str::snake($model)),
                    ])
                    ->generate();
        }

    }

}
