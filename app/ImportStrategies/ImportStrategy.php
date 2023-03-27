<?php

namespace App\ImportStrategies;

interface ImportStrategy
{
    public function import(array $data);
}
