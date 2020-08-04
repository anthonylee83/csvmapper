<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportContactsPostRequest;
use App\Library\CsvProcessor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrepareContactsImportController extends Controller
{
    /**
     * Receives a csv file and returns the header and a temp
     * code that can be used to reference the fields for import.
     *
     * @param ImportContactsPostRequest $request http request that includes file
     *
     * @return Response Http response that includes tempCode and headers
     */
    public function post(ImportContactsPostRequest $request)
    {
        $csvFile = $request->file;
        $csvProcessor = new CsvProcessor($csvFile);
        $tempCode = rand();
        Cache::add('contacts.import' . Auth::id() . $tempCode, $csvProcessor->body);

        // An additonal table that holds past mappings may work well here, if the headers match up
        // then we could return the last mapping the user had so they don't have to select it again
        return response()->json(['tempCode' => $tempCode, 'headers' => $csvProcessor->headers], 200);
    }
}
