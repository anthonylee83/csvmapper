<?php

namespace App\Library;

use Exception;
use Illuminate\Support\Facades\Log;

class CsvProcessor {

    protected $fileName;
    public $headers;
    public $body;

    /**
     * Creates a new instance of CsvProcessor
     *
     * @param String $fileName name of file to process
     *
     * @return void
     */
    public function __construct(String $fileName)
    {
        $this->fileName = $fileName;
        $this->_readCsvFile();
    }

    /**
     * Reads a csv file and processes headers and body
     *
     * @return void
     */
    private function _readCsvFile()
    {
        try{
            $csvContent  = file($this->fileName);
            $this->headers = self::_processRow(array_shift($csvContent));
            $this->body = $csvContent;
        }catch(Exception $e)
        {
            // Would probably setup some type of alert/loggig to indicate failure here
            // especially for file open errors.
            $this->headers = [];
            $this->body = '';
        }

    }

    /**
     * Process an array of a CSV body
     *
     * @param Array $body Array of csv lines
     *
     * @return array
     */
    public static function processCsvBody(Array $body) : array
    {
        $processedBody = array_map(
            function ($row) {
                return self::_processRow($row);
            },
            $body
        );
        return $processedBody;
    }

    /**
     * Process a row of a csv to return an array of values
     *
     * @param Mixed $row of csv file
     *
     * @return array
     */
    private static function _processRow($row) : array
    {
        if (is_array($row)) {
            return $row;
        }
        return explode(",", str_replace("\n", "", $row));
    }
}
