<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\ImportStrategies\ImportCustomerStrategy;
use App\ImportStrategies\ImportProductStrategy;
use App\ImportStrategies\Importer;
use Illuminate\Support\Facades\Log;


class ImportMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:masterdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports master data from files.';
    protected $onCompleteMessage = "Data import is completed.";
    protected $onStartMessage = "Processing data...";


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info($this->onStartMessage);

        $customerCsv = Http::withOptions([
                            'verify' => false,
                            'auth' => [config('globals.LOOP_MASTER_DATA.USERNAME'), config('globals.LOOP_MASTER_DATA.PASSWORD')]
                        ])->get(config('globals.LOOP_MASTER_DATA.URL').'customers.csv')->body();

        $productCsv = Http::withOptions([
                            'verify' => false,
                            'auth' => [config('globals.LOOP_MASTER_DATA.USERNAME'), config('globals.LOOP_MASTER_DATA.PASSWORD')]
                        ])->get(config('globals.LOOP_MASTER_DATA.URL').'products.csv')->body();

        $customerData = array_map('str_getcsv', preg_split('/\r*\n+|\r+/', $customerCsv));
        $productData = array_map('str_getcsv', preg_split('/\r*\n+|\r+/', $productCsv));

        $customerImportStrategy = new ImportCustomerStrategy();
        $productImportStrategy = new ImportProductStrategy();

        $importer = new Importer();

        $importer->setImportStrategy('customer', $customerImportStrategy);
        $importer->setImportStrategy('product', $productImportStrategy);

        $importer->import('customer', $customerData);
        $importer->import('product', $productData);
        $importer->logResult();

        $this->info($this->onCompleteMessage);

        return Command::SUCCESS;
    }
}
