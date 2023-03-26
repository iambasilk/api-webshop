<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ImportProductException extends Exception
{
    protected $exceptionName = 'ImportProductException';

    public function __construct($message = null)
    {
        if ($message !== null) {
            $this->message = json_encode($message);
        }
    }

    public function report()
    {
        Log::error($this->exceptionName.$this->message);
    }

}
