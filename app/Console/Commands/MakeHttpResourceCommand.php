<?php

namespace App\Console\Commands;

use App\Facades\VariablesService;
use Binafy\LaravelStub\Facades\LaravelStub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeHttpResourceCommand extends Command
{
    protected $signature = 'igu:make-http-resource {model} {--table=} {--force}';

    protected $description = 'Make HTTP Resource class';

    public function handle(): void
    {
        $model = ucfirst(Str::camel($this->argument('model')));
        $force = $this->option('force');

        $table = $this->option('table') ?: Str::snake(Str::pluralStudly($this->argument('model')));

        $namespace = 'App\\Http\\Resources';
        $baseDir = __DIR__ . '/../../../stubs/';

        $to = getcwd() . '/app/Http/Resources/';

        if (!File::exists($to))
            File::makeDirectory($to, $mode = 0777, true);

        //////

        $skipFields = ['created_at', 'updated_at', 'deleted_at', 'id', 'prefixed_id', 'password', 'token', 'access_token'];

        // OpenApi annotations
        $annotations = collect(VariablesService::getProps($table))
            ->filter(fn($field) => !in_array($field->Field, $skipFields))
            ->map(function ($field) {
                return "\t\tnew OA\Property(property: '" . Str::snake($field->Field) . "', type: '" . VariablesService::getDataType($field) . "'),";
            })
            ->toArray();

        $annotations = implode(PHP_EOL, $annotations);

        // resource fields
        $fields = collect(VariablesService::getProps($table))
            ->filter(fn($field) => !in_array($field->Field, $skipFields))
            ->map(function ($field) {
                return "\t\t\t'" . Str::snake($field->Field) . "' => \$this->" . Str::snake($field->Field) . ",";
            })
            ->toArray();

        $fields = implode(PHP_EOL, $fields);

        $name = "{$model}Resource";

        if ($force || !File::exists($to . '/' . $name . '.php')) {
            LaravelStub::from($baseDir . 'http-resource.stub')
                ->to($to)
                ->name($name)
                ->ext('php')
                ->replaces([
                    'CLASS' => $name,
                    'NAMESPACE' => $namespace,
                    'MODEL' => $model,
                    'OPEN_API_ANNOTATIONS' => $annotations,
                    'RESOURCE_FIELDS' => $fields,
                ])
                ->generate();
        }
    }

}
