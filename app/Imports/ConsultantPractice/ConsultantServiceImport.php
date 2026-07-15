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

class ConsultantServiceImport implements ToCollection, WithHeadingRow, WithChunkReading{

    public int $successful = 0;

    public array $errors = [];

    public function __construct(protected int $consultant_id, 
        ){}

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $index => $row){
            $excelRow = $index + 2;
            try{
                if(empty($consultant_id)){throw new Exception('Consultant ID is required');}
                else{
                    $consultant = Consultant::find($this->consultant_id);
                    if(!$consultant){throw new Exception('Consultant not found');}
                }
                if(empty($row['service_name'])){throw new Exception('Service Name is required');}
                if(!empty($row['icp_code'])){
                    $service = Service::where('icp_code', $row['icp_code'])->first();

                    if (!$service) {
                        $service = Service::where('name', $row['service_name'])->first();
                    }
                    
                }
                if(!$service){
                    $service = Service::create([
                        'name' => $row['service_name'],
                        'icp_code' => $row['icp_code'],
                        'specialty_id' => $consultant->specialty_id,
                        'status' => Service::StatusActive,
                    ]);
                }

                ConsultantService::updateOrCreate(
                    ['consultant_id' => $this->consultant_id, 'service_id' => $service->id],
                    ['price' => $row['price'], 'status' => ConsultantService::StatusActive,]
                );

                $this->successful++;

            }
            catch(Exception $e){
                $this->errors[] = ['row' => $excelRow, 'message' => $e->getMessage()];
            }
        }
    }
}