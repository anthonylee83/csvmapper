<?php

namespace Tests\Unit;

use App\Library\CsvProcessor;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    private $_headers = ["first_name", "last_name" ,"city", "country"];
    private $_body = ["Robert", "Johnson", "Topeka", "Kansas"];
    private $_fileName = './contacts.csv';

    /**
     * Setup the csv file we will use to test with
     *
     * @return void
     */
    protected function setUp(): void
    {
        $list = [
            $this->_headers,
            $this->_body
        ];

        $file = fopen($this->_fileName, "w");

        foreach ($list as $line) {
            fputcsv($file, $line);
        }
        fclose($file);

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCsvProcessorImport()
    {

        $csvProcessor = new CsvProcessor($this->_fileName);
        //dd($csvProcessor->headers);
        $this->assertTrue($csvProcessor->headers === $this->_headers);
    }

    public function testCsvProcessorImportInvalidFile()
    {
        $csvProcess = new CsvProcessor('./someunknownfileerror.csv');
        $this->assertTrue($csvProcess->headers === []);
    }

    public function testCSVProcessorBodyParse()
    {
        $csvProcessor = new CsvProcessor($this->_fileName);
        $body = CsvProcessor::processCsvBody($csvProcessor->body);
        $this->assertEquals([$this->_body], $body);
    }

    /**
     * Teardown and remove file
     */
    protected function tearDown(): void
    {
        unlink($this->_fileName);

    }
}
