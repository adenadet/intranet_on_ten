<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EMR\ImagingRequest;
use Aranyasen\HL7\Message;
use Aranyasen\HL7\Segments\MSH;
use Aranyasen\HL7\Segments\PID;
use Aranyasen\HL7\Segments\OBR;
use Aranyasen\HL7\Segments\OBX;
use Aranyasen\HL7\Segments\ORC;


class HL7MLLPListener extends Command
{
    protected $signature = 'hl7:listen';
    protected $description = 'Start an MLLP listener for HL7 messages.';

    public function handle()
    {
        $port = 2575; // Define the port number
        $serverSocket = stream_socket_server("tcp://0.0.0.0:{$port}", $errno, $errstr);

        if (!$serverSocket) {
            $this->error("Error creating server socket: $errstr ($errno)");
            return;
        }

        $this->info("MLLP HL7 Listener started on port {$port}...");

        while (true) {
            $clientSocket = stream_socket_accept($serverSocket);

            if ($clientSocket) {
                $message = $this->receiveHL7Message($clientSocket);
                $ackMessage = $this->processHL7Message($message);
                fwrite($clientSocket, $ackMessage);
                fclose($clientSocket);
            }
        }
    }

    private function receiveHL7Message($socket)
    {
        $buffer = "";
        while (!feof($socket)) {
            $chunk = fread($socket, 1024);
            $buffer .= $chunk;
            if (strpos($buffer, "\x1C") !== false) { // MLLP End Block
                break;
            }
        }
        return trim($buffer, "\x0B\x1C\r"); // Remove MLLP framing
    }

    private function processHL7Message($rawMessage)
    {
        try {
            $message = new Message($rawMessage);
            $msh = $message->getSegmentByIndex(0);
            $messageType = $msh->getField(9);

            switch ($messageType) {
                case 'ORM^O01':
                    $this->processORMMessage($message);
                    break;
                case 'ORU^R01':
                    $this->processORUMessage($message);
                    break;
                default:
                    $this->error("Unsupported message type: $messageType");
                    break;
            }

            return $this->generateHL7Ack($msh->getField(10)); // Message Control ID
        } catch (\Exception $e) {
            $this->error("Error processing HL7 message: " . $e->getMessage());
            return $this->generateHL7Ack(null, 'AE', $e->getMessage()); // Application Error
        }
    }

    private function processORMMessage(Message $message)
    {
        $pid = $message->getSegmentByName('PID');
        $obr = $message->getSegmentByName('OBR');

        ImagingRequest::create([
            'accession_number' => $obr->getField(2),
            'patient_id' => $pid->getField(3),
            'patient_name' => $pid->getField(5),
            'exam_type' => $obr->getField(4),
            'status' => 'Pending'
        ]);

        $this->info("Processed ORM^O01 message for patient: " . $pid->getField(5));
    }

    private function processORUMessage(Message $message)
    {
        $obr = $message->getSegmentByName('OBR');
        $obx = $message->getSegmentByName('OBX');

        ImagingRequest::where('accession_number', $obr->getField(2))->update([
            'report' => $obx->getField(5),
            'radiologist' => $obx->getField(16),
            'status' => 'Completed'
        ]);

        $this->info("Processed ORU^R01 message for accession number: " . $obr->getField(2));
    }

    private function generateHL7Ack($messageControlId, $ackCode = 'AA', $errorMessage = '')
    {
        $ack = new Message();
        $msh = new MSH();
        $msa = new \Aranyasen\HL7\Segments\MSA();

        $msh->setField(7, date('YmdHis'));
        $msh->setField(9, 'ACK');
        $msh->setField(10, uniqid());
        $msh->setField(11, 'P');
        $msh->setField(12, '2.3');

        $msa->setField(1, $ackCode);
        $msa->setField(2, $messageControlId);
        if ($ackCode !== 'AA') {
            $msa->setField(3, $errorMessage);
        }

        $ack->addSegment($msh);
        $ack->addSegment($msa);

        return "\x0B" . $ack->toString(true) . "\x1C\r";
    }
}
