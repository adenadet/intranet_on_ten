<?php

namespace App\Http\Controllers\Api\EMR;

use App\Http\Controllers\Controller;
use App\Http\Traits\EService\AppointmentTrait;
use App\Models\EMR\Appointment;
use App\Models\EMR\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Aranyasen\HL7\Message;
use Aranyasen\HL7\Segments\MSH;
use Aranyasen\HL7\Segments\PID;
use Aranyasen\HL7\Segments\OBR;
use Aranyasen\HL7\Segments\OBX;
use Aranyasen\HL7\Segments\PV1;
use Aranyasen\HL7\Segments\ORC;
use Uhin\HL7\hl7\HL7;

class HL7Controller extends Controller
{
    use AppointmentTrait;
    /*
    public function sendHL7(Request $request)
    {
        $hl7 = new HL7();
        $hl7->setMshSegment([
            'sendingApplication' => 'UHIN',
            'sendingFacility' => 'UHIN',
            'receivingApplication' => 'UHIN',
            'receivingFacility' => 'UHIN',
            'dateTimeOfMessage' => date('YmdHis'),
            'security' => '',
            'messageType' => 'ADT',
            'messageControlId' => '123456789',
            'processingId' => 'P',
            'versionId' => '2.5',
            'sequenceNumber' => '',
            'continuationPointer' => '',
            'acceptAcknowledgmentType' => '',
            'applicationAcknowledgmentType' => '',
            'countryCode' => '',
            'characterSet' => '',
            'principalLanguageOfMessage' => '',
            'alternateCharacterSetHandlingScheme' => '',
            'messageProfileIdentifier' => '',
            'sendingResponsibleOrganization' => '',
            'receivingResponsibleOrganization' => '',
            'sendingNetworkAddress' => '',
            'receivingNetworkAddress' => ''
        ]);

        $hl7->addSegment('PID', [
            'setID' => '1',
            'patientID' => '123456',
            'patientIdentifierList' => [
                'id' => '123456',
                'identifierTypeCode' => 'MR',
                'assigningFacility' => 'UHIN',
                'effectiveDate' => '',
                'expirationDate' => '',
                'assigningJurisdiction' => '',
                'assigningAgencyOrDepartment' => ''
            ],
            'alternatePatientID' => '',
            'patientName' => [
                'familyName' => 'Doe',
                'givenName' => 'John',
                'secondAndFurtherGivenNamesOrInitialsThereof' => '',
                'suffix' => '',
                'prefix' => '',
                'degree' => ''
            ],
            'mothersMaidenName' => '',
            'dateOfBirth' => '19700101',
        ]);
    }
        */
        
    public function handleORM(Request $request)
    {
        try {
            $hl7Message = new Message($request->getContent());
            
            $msh = $hl7Message->getSegmentByIndex(0);
            $pid = $hl7Message->getSegmentByIndex(1);
            $orc = $hl7Message->getSegmentByIndex(3);
            $obr = $hl7Message->getSegmentByIndex(4);
            
            return response()->json([
                'message_type' => $msh->getField(9),
                'patient_id' => $pid->getField(3),
                'patient_name' => $pid->getField(5),
                'order_id' => $orc->getField(2),
                'exam_type' => $obr->getField(4),
                'status' => 'Order Received'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handleORU(Request $request)
    {
        try {
            $hl7Message = new Message($request->getContent());
            
            $msh = $hl7Message->getSegmentByIndex(0);
            $pid = $hl7Message->getSegmentByIndex(1);
            $obr = $hl7Message->getSegmentByIndex(3);
            $obx = $hl7Message->getSegmentByIndex(4);
            
            return response()->json([
                'message_type' => $msh->getField(9),
                'patient_id' => $pid->getField(3),
                'exam_type' => $obr->getField(4),
                'report' => $obx->getField(5),
                'status' => 'Report Received'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}