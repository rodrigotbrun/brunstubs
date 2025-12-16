<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function Laravel\Prompts\text;

class VariablesService
{
    public function __construct(
        public array $ignoreColumns = ['created_at', 'updated_at', 'id', 'prefixed_id', 'deleted_at']
    )
    {
    }

    public function getIgnorableColumns(): array
    {
        return $this->ignoreColumns;
    }

    public function getProps(&$table): array
    {
        try {
            return DB::select('SHOW COLUMNS FROM ' . $table);
        } catch (QueryException $exception) {
            $table = text('Table "' . $table . '" not found. What\'s the correct table name?');

            return DB::select('SHOW COLUMNS FROM ' . $table);
        }
    }

    public function getType(\stdClass $field, bool $allOptional = false): string
    {
        if ($allOptional) {
            $nullable = '?';
        } else {
            $nullable = $field->Null == 'NO' ? '?' : '';
        }

        return $nullable . $this->getDataType($field);
    }

    public function getDataType(\stdClass $field): string
    {
        if (Str::contains($field->Type, 'varchar')) return 'string';
        if (Str::contains($field->Type, 'timestamp')) return 'string';
        if (Str::contains($field->Type, 'date')) return 'string';
        if (Str::contains($field->Type, 'text')) return 'string';
        if (Str::contains($field->Field, '_id')) return 'string'; // considering the system is using prefixed_id
        if (Str::contains($field->Type, 'int')) return 'int';

        return 'mixed';
    }

    public function asConstructorProps(string $table, array $skip = [], bool $allOptional = false): Collection
    {
        $props = collect($this->getProps($table));

        return $props
            ->filter(fn($item) => !in_array($item->Field, [...$this->ignoreColumns, ...$skip]))
            ->map(function ($item, $key) use ($allOptional) {
                return 'public ' . $this->getType($item, $allOptional) . ' $' . Str::camel($item->Field);
            });
    }

    public function asRules(string $table, array $skip = [], bool $allOptional = false): Collection
    {
        $props = collect($this->getProps($table));

        return $props
            ->filter(fn($item) => !in_array($item->Field, [...$this->ignoreColumns, ...$skip]))
            ->map(function ($item, $key) use ($allOptional) {
                return 'public ' . $this->getType($item, $allOptional) . ' $' . Str::camel($item->Field);
            });
    }
}
