<?php

namespace App\ImportStrategies;

use App\ImportStrategies\ImportStrategy;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ImportProductException;

class ImportProductStrategy implements ImportStrategy
{
    public $count = [
        'totalCount' => 0,
        'importedCount' => 0
    ];

    public function import(array $data)
    {
        // Remove Header row
        array_shift($data); 
        $this->count['totalCount'] = count($data);

        foreach ($data as $row) {

            try{
                if (empty($row[0]) || empty($row[1])) {
                    throw new ImportProductException($row);
                }
            }
            catch(ImportProductException $e){
                report($e);
            }

            try{  
                $product = new Product([
                    'id' => $row[0],
                    'productname' => $row[1],
                    'price' => $row[2],
                ]);

                if ($product->save()) {
                    $this->count['importedCount']++;
                    Log::info("Imported product ID: {$product->id}.");
                } else {
                    Log::error("Failed to import product ID: {$product->id}.");
                }
            }catch(Exception $exception){
                Log::error('Error importing products: ' . $exception->getMessage());
            } 
        }
    }
}