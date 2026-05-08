<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Patient Referral Form</title>

<style>
    @page {
        size: A4 portrait;
        margin: 0;
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #fff;
    }

    .page {
        width: 210mm;
        height: 297mm;
        padding: 15mm;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }

    /* HEADER */
    .header {
        text-align: center;
        margin-bottom: 10mm;
    }

    .header h1 {font-size: 18px; margin: 0; letter-spacing: 1px;}

    /* INFO ROW */
    .info { display: flex; justify-content: space-between; margin-bottom: 8mm; font-size: 12px;}

    .info .left, .info .right {width: 48%;}

    .line {margin-bottom: 3mm;}

    .label {
        font-weight: bold;
    }

    .value {
        display: inline-block;
        border-bottom: 1px solid #000;
        min-width: 60%;
        padding: 1mm 2mm;
    }

    /* SECTION TITLE */
    .section-title {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 3mm;
        border-bottom: 1px solid #000;
        padding-bottom: 2mm;
    }

    /* CONTENT */
    .content {
        flex: 1;
        font-size: 15px;
        line-height: 1.5;
        border: 1px solid #000;
        padding: 5mm;
        margin-bottom: 10mm;
        overflow: hidden;
    }

    /* SIGNATURE */
    .signature {
        margin-top: auto;
        font-size: 12px;
    }

    .signature img {
        height: 25mm;
        margin: 5mm 0;
    }

    /* FOOTER */
    .footer {
        font-size: 10px;
        margin-top: 5mm;
        text-align: center;
        color: #555;
    }
</style>
</head>

<body>

<div class="page">
    <div class="header">
        <img src="{{ asset('/img/background/SNH_logo.png') }}"  style="height: 130px" />
        <h1>PATIENT REFERRAL FORM</h1>
    </div>

    <!-- INFO -->
    <div class="info">
        <div class="left">
            <div class="line">
                <span class="label">REF:</span>
                <span class="value">
                    {{ $appointment->patient->last_name }}, {{ $appointment->patient->first_name }} {{ $appointment->patient->middle_name }} 
                </span>
            </div>

            <div class="line">
                <span class="label">SNH No:</span>
                <span class="value">
                    {{ $appointment->unique_id }}
                </span>
            </div>

            <div class="line">
                <span class="label">Date of Birth:</span>
                <span class="value">
                    {{ date('d M Y', strtotime($appointment->patient->dob)) }}
                </span>
            </div>
        </div>

        <div class="right">
            <div class="line">
                <span class="label">Date:</span>
                <span class="value">
                    {{ date('d M Y', strtotime($referral->updated_at)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- MEDICAL HISTORY -->
    <div class="section-title">Medical History</div>

    <div class="content">
        {!! $referral->details !!}
    </div>

    <!-- SIGNATURE -->
    <div class="signature">
        <strong>
            Yours Sincerely,<br>
            For: St. Nicholas Hospital
        </strong>

        <div>
            @php
                $signature = match($referral->created_by) {
                    56 => 'rusman.png',
                    57 => 'rabudah.png',
                    55 => 'bsalami.png',
                    331 => 'mnwachukwu.png',
                    default => 'bakinloye.png',
                };
            @endphp
            <img src="{{ asset('img/consents/'.$signature) }}" alt="Signature">
        </div>
        <div><strong>Dr. {{ $referral->creator->first_name}} {{ $referral->creator->last_name}}</strong></div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        This referral is issued by St. Nicholas Hospital for clinical purposes only.
    </div>
</div>
</body>
</html>