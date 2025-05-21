<?php

namespace App\Http\Controllers\Api\EMR;

use App\Http\Controllers\Controller;
use App\Imports\EServices\LaboratoryReportImport;
use App\Models\EMR\Appointment;
use App\Models\EMR\Laboratory;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaboratoryController extends Controller
{
    public function import(Request $request){
        $dent = explode("base64,", $request->input('file'));
        $decodedData = base64_decode($dent[1], true);
        if ($decodedData === false) {
            return response()->json([
                'result' => null,
                'message' => 'The provided string is not valid Base64.',
            ], 500);
        }
        
        $fileSignature = substr($decodedData, 0, 4);
        $validCsvSignature = chr(0xEF) . chr(0xBB) . chr(0xBF); // Optional BOM for UTF-8 CSV
        $validXlsxSignature = chr(0x50) . chr(0x4B) . chr(0x03) . chr(0x04); // XLSX files (PKZIP format)

        if ($fileSignature === $validCsvSignature || strpos($decodedData, ',') !== false) {
            $fileType = "xlsx";
        } 
        elseif ($fileSignature === $validXlsxSignature) {
            $fileType = "xlsx";
        }
        else {
            return response()->json([
                'result' => null,
                'message' => "The Base64 string does not represent a valid CSV or Excel file."
            ]);
        }

        $fileName = 'uploaded_lab_report_'.time().'.'. $fileType;
        $tempPath = public_path('uploads/files/' . $fileName);
        file_put_contents($tempPath, $decodedData);

        try {
            $query = Excel::import(new LaboratoryReportImport, $tempPath);
            @unlink($tempPath);
            return response()->json([
                'result' => $query,
                'message' => 'The file was imported successfully',
            ]);
        }

        catch(Exception $e){
            @unlink($tempPath);
            return response()->json(['error' => 'Failed to process the file', 'details' => $e->getMessage()], 500);
        }
    }
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'appointment_id' => 'required',
            'summary' => 'required',
            'details' => 'required',
        ]);

        $appointment = Appointment::where('id', '=', $request->input('appointment_id'))->first();

        Laboratory::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'summary' => $request->input('summary'), 
            'details' => $request->input('details'), 
            'created_by' => auth('api')->id(), 
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'appointment' => Appointment::where('id','=', $request->input('appointment_id'))->with(['front_officer', 'medical_officer', 'radiologist','service', 'patient.nationality', 'payment.employee', 'consent', 'consultation', 'laboratory', 'lab_officer', 'report.findings', 'issuing_officer'])->first(),
        ]);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
