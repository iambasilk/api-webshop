<?php

namespace App\ImportStrategies;

use App\ImportStrategies\ImportStrategy;
use Illuminate\Support\Facades\Log;

class Importer
{
    protected $importStrategies = [];

    public function setImportStrategy(string $key, ImportStrategy $strategy)
    {
        $this->importStrategies[$key] = $strategy;
    }

    public function import(string $key, array $data)
    {
        if (!array_key_exists($key, $this->importStrategies)) {
            throw new InvalidArgumentException("No import strategy found for key {$key}.");
        }

        $this->importStrategies[$key]->import($data);
    }

    public function logResult()
    {
        foreach($this->importStrategies as $importStrategyKey => $strategy){
            Log::info("Total ".$importStrategyKey.": " . $strategy->count['totalCount'] .", Imported ".$importStrategyKey." :". $strategy->count['importedCount']);
        }
    }
}
