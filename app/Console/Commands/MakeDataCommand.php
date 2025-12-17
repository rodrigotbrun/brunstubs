<?php

namespace App\Console\Commands;

use App\Facades\VariablesService;
use Binafy\LaravelStub\Facades\LaravelStub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDataCommand extends Command
{
    protected $signature = 'igu:make-data {model} {--types=uc} {--table=} {--force}';

    protected $description = 'Make DATA classes';

    public function handle(): void
    {
        $model = ucfirst(Str::camel($this->argument('model')));
        $types = $this->option('types');
        $force = $this->option('force');

        $table = $this->option('table') ?: Str::snake(Str::pluralStudly($this->argument('model')));

        $namespace = 'App\\Data\\' . $model;

        if(\Phar::running()) {
            $baseDir = getcwd() . '/stubs/';
        }else{
            $baseDir = base_path('stubs') / '/';
        }

        $to = getcwd() . '/app/Data/' . $model . '/';

        if (!File::exists($to))
            File::makeDirectory($to, $mode = 0777, true);

        //////

        if (Str::contains($types, 'c')) {
            $PROPS = VariablesService::asConstructorProps($table)
                ->map(fn($prop) => "\t\t" . $prop)
                ->toArray();

            $name = "Create{$model}Data";
            $props = implode(',' . PHP_EOL, $PROPS);

            $rules = collect(VariablesService::getProps($table))
                ->filter(fn($item) => !in_array($item->Field, [...VariablesService::getIgnorableColumns()]))
                ->map(function ($field) {
                    $rules = [];

                    if ($field->Null === 'NO') {
                        $rules[] = 'required';
                    } else {
                        $rules[] = 'nullable';
                    }

                    $rules = array_merge($rules, $this->getRuleTypes($field));

                    return "\t\t\t'" . Str::snake($field->Field) . "' => '" . implode('|', $rules) . "'";
                })
                ->toArray();

            $rules = implode(',' . PHP_EOL, $rules);

            if ($force || !File::exists($to . '/' . $name . '.php'))
                LaravelStub::from($baseDir . 'data.stub')
                    ->to($to)
                    ->name($name)
                    ->ext('php')
                    ->replaces([
                        'CLASS' => $name,
                        'NAMESPACE' => $namespace,
                        'PROPS' => $props,
                        'RULES' => $rules
                    ])
                    ->generate();
        }

        // Update
        if (Str::contains($types, 'u')) {
            $PROPS = VariablesService::asConstructorProps($table, allOptional: true)
                ->map(fn($prop) => "\t\t" . $prop)
                ->toArray();

            $name = "Update{$model}Data";
            $props = implode(',' . PHP_EOL, $PROPS);

            $rules = collect(VariablesService::getProps($table))
                ->filter(fn($item) => !in_array($item->Field, [...VariablesService::getIgnorableColumns()]))
                ->map(function ($field) {
                    $rules = [
                        'nullable',
                    ];

                    $rules = array_merge($rules, $this->getRuleTypes($field));

                    return "\t\t\t'" . Str::snake($field->Field) . "' => '" . implode('|', $rules) . "'";
                })
                ->toArray();

            $rules = implode(',' . PHP_EOL, $rules);

            if ($force || !File::exists($to . '/' . $name . '.php'))
                LaravelStub::from($baseDir . 'data.stub')
                    ->to($to)
                    ->name($name)
                    ->ext('php')
                    ->replaces([
                        'CLASS' => $name,
                        'NAMESPACE' => $namespace,
                        'PROPS' => $props,
                        'RULES' => $rules
                    ])
                    ->generate();
        }

        $this->info('Generated Data classes');
    }

    private function getRuleTypes(\stdClass $field): array
    {
        $rules = [];
        if (Str::contains($field->Type, 'varchar')
            || Str::contains($field->Type, 'text')
            || Str::contains($field->Type, 'date')
            || Str::contains($field->Type, 'timestamp')
            || Str::contains($field->Field, '_id')
        ) {
            $rules[] = 'string';
            $rules[] = 'max:255';
        };

        return $rules;
    }
}
