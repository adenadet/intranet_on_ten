<?php

namespace App\Imports\ConsultantPractice;

use App\Models\ConsultantPractice\Consultant;
use App\Models\ConsultantPractice\ConsultantService;
use App\Models\ConsultantPractice\Service;
use Exception;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ServiceImport implements ToCollection, WithHeadingRow, WithChunkReading{

    public int $successful = 0;

    public array $errors = [];

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $index => $row){
            $excelRow = $index + 2;
            try{
                if(empty($row['service_name'])){throw new Exception('Service Name is required');}
                if(!empty($row['icp_code'])){throw new Exception('Service Name is required');}
                if(!empty($row['specialty_id'])){throw new Exception('Specialty ID is required');}

                $service = Service::where('icp_code', $row['icp_code'])->first();
 
                if(!$service){
                    $service = Service::create([
                        'name' => $row['service_name'],
                        'icp_code' => $row['icp_code'],
                        'specialty_id' => $row['specialty_id'],
                        'status' => Service::StatusActive,
                    ]);
                }
                else{
                    $service->update([
                        'name' => $row['service_name'],
                        'icp_code' => $row['icp_code'],
                        'specialty_id' => $row['specialty_id'],
                        'status' => Service::StatusActive,
                        'deleted_at' => null,
                    ]);
                }
                
                $this->successful++;
            }    
            catch(Exception $e){
                $this->errors[] = ['row' => $excelRow, 'message' => $e->getMessage()];
            }
        }
    }
}