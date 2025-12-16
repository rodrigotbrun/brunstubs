<?php

namespace App\Console\Commands;

use App\Facades\VariablesService;
use Binafy\LaravelStub\Facades\LaravelStub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCRUDCommand extends Command
{
    protected $signature = 'igu:make-crud {model} {--table=} {--force}';

    protected $description = 'Make CRUD classes';

    public function handle(): void
    {
        $table = $this->option('table') ?: Str::snake(Str::pluralStudly($this->argument('model')));
        $model = $this->argument('model');
        $force = $this->option('force');

        VariablesService::getProps($table); // try table exists, and fix if needed

        Artisan::call('igu:make-data', [
            'model' => $model,
            '--types' => 'uc',
            '--table' => $table,
            '--force' => $force,
        ]);
        $this->info(Artisan::output());

        Artisan::call('igu:make-action', [
            'model' => $model,
            '--types' => 'ucd',
            '--force' => $force,
        ]);
        $this->info(Artisan::output());

        Artisan::call('igu:make-http-resource', [
            'model' => $model,
            '--table' => $table,
            '--force' => $force,
        ]);
        $this->info(Artisan::output());

        Artisan::call('igu:make-controller', [
            'model' => $model,
            '--table' => $table,
            '--force' => $force,
        ]);
        $this->info(Artisan::output());
    }

}
