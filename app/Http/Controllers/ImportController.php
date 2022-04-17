<?php

namespace App\Http\Controllers;

use App\Models\CsvData;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Bus;
use App\Jobs\CsvProcess;
class ImportController extends Controller
{
    public function parseImport(CsvImportRequest $request)
    {

        if ($request->has('header')) {
            $headings = (new HeadingRowImport)->toArray($request->file('csv_file'));
            $data = Excel::toArray(new ContactsImport, $request->file('csv_file'))[0];
        } else {
            $data = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        }

        if (count($data) > 0) {
            $csv_data = array_slice($data, 0, 2);

            $csv_data_file = CsvData::create([
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_header' => $request->has('header'),
                'csv_data' => json_encode($data)
            ]);
        } else {
            return redirect()->back();
        }

        return view('import_fields', [
            'headings' => $headings ?? null,
            'csv_data' => $csv_data,
            'csv_data_file' => $csv_data_file
        ]);

    }

    public function processImport(Request $request)
    {
        $data = CsvData::find($request->csv_data_file_id);
        $csv_data = json_decode($data->csv_data, true);

        $chunks = array_chunk($csv_data, 1000);
        $batch  = Bus::batch([])->dispatch();
        foreach ($chunks as $key => $chunk) {
            $batch->add(new CsvProcess($chunk, $data->csv_header, $request->fields));
        }

        return redirect()->route('contacts.index')->with('success', 'Import finished.');
    }
}