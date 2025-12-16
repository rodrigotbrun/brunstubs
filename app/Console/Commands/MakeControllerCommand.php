<?php

namespace App\Console\Commands;

use App\Facades\VariablesService;
use Binafy\LaravelStub\Facades\LaravelStub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeControllerCommand extends Command
{
    protected $signature = 'igu:make-controller {model} {--table=} {--force}';

    protected $description = 'Make Controller class';

    public function handle(): void
    {
        $model = ucfirst(Str::camel($this->argument('model')));
        $force = $this->option('force');

        $namespace = 'App\\Http\\Controllers\\Dashboard';
        $baseDir = __DIR__ . '/../../../stubs/';

        $to = getcwd() . '/app/Http/Controllers/Dashboard/';

        if (!File::exists($to))
            File::makeDirectory($to, $mode = 0777, true);

        //////

        $name = ucfirst(Str::camel(Str::plural($model))) . "Controller";

        if ($force || !File::exists($to . '/' . $name . '.php')) {
            LaravelStub::from($baseDir . 'controller.stub')
                ->to($to)
                ->name($name)
                ->ext('php')
                ->replaces([
                    'CLASS' => $name,
                    'NAMESPACE' => $namespace,
                    'MODEL' => $model,
                    'MODEL_VARIABLE' => Str::camel($model),
                    'MODEL_PLURAL_VARIABLE' => Str::camel(Str::plural($model)),
                ])
                ->conditions([
                    'hasCreateAction' => true,
                    'hasUpdateAction' => true,
                    'hasDeleteAction' => true,
                ])
//                ->conditions([
//                    'hasCreateAction' => File::exists(getcwd() . '/app/Actions/Create' . $model . '.php'),
//                    'hasUpdateAction' => File::exists(getcwd() . '/app/Actions/Update' . $model . '.php'),
//                    'hasDeleteAction' => File::exists(getcwd() . '/app/Actions/Delete' . $model . '.php'),
//                ])
                ->generate();
        }

        $this->info('Generated Controller class');
    }

}
