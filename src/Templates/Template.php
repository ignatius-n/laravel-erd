<?php

namespace Recca0120\LaravelErd\Templates;

use Illuminate\Support\Collection;

interface Template
{
    public function render(Collection $tables): string;

    public function save(string $output, string $path, array $options = []): int;
}