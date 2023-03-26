<?php

namespace App\ImportStrategies;

use App\ImportStrategies\ImportStrategy;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ImportCustomerException;
use Exception;

class ImportCustomerStrategy implements ImportStrategy
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
            $name =  explodeFullName($row[3]);

            // Check for empty row values
            try{
                if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4]) || empty($row[5])) {
                    throw new ImportCustomerException($row);
                }
            }
            catch(ImportCustomerException $e){
                report($e);
            }

            try{
             
                $customer = new Customer([
                    'id' => $row[0],
                    'job_title' => $row[1],
                    'email' => $row[2],
                    'first_name' => $name['firstName'],
                    'last_name' => $name['lastName'],
                    'registered_since' =>  Carbon::createFromFormat('l, F j, Y', $row[4])->format('Y-m-d'),
                    'phone' => $row[5],
                ]);
            
                if ($customer->save()) {
                    $this->count['importedCount']++;
                    Log::info("Imported customer ID: {$customer->id}.");
                } else {
                    Log::error("Failed to import customer ID: {$product->id}.");

                }

            }catch(Exception $exception){
                Log::error('Error importing customers: ' . $exception->getMessage());
            } 

        }

    }

}