<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Library\CsvProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class ImportContactsController extends Controller
{

    /**
     * Index page to show import button
     *
     * @return View index view
     */
    public function index()
    {
        return view('contacts.import.index');
    }

    /**
     * Receives a post from the PrepareContactsImport controller
     *
     * @param Request $request request containing tempCode and header mapping
     *
     * @return Response http response with success or failure
     */
    public function post(Request $request)
    {
        $tempCode = (int) $request->tempCode;
        $fieldMapping = $request->form;
        $headers = $request->csvHeaders;
        $csvData = Cache::get('contacts.import' . Auth::id() . $tempCode, []);
        $contacts = CsvProcessor::processCsvBody($csvData);
        $customAttributeKeys = array_diff(array_keys($contacts[0]), array_values($request->form));
        $errors = Contact::massInsertOrUpdate($contacts, $fieldMapping, $customAttributeKeys, $headers);

        //This would be a good spot to store a log of the import so the user can see old imports
        return response()->json(['errors' => $errors], 200);
    }
}
