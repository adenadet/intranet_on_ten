<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$appointment->patient->last_name}}, {{$appointment->patient->first_name}} {{$appointment->patient->middle_name}} | St. Nicholas Hospital UK TB Screening Certificate </title>
    <link rel="stylesheet" href="{{asset('lte/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('lte/dist/css/adminlte.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        @page {size: A4 landscape; margin: 0;}
        body {margin: 0; font-family: Arial, sans-serif; background: #fff;}
        .applicant-photo img { width: 50mm; height: auto; object-fit: contain; max-height: none;}
        .page {width: 297mm; height: 210mm; padding: 10mm; box-sizing: border-box; display: flex; flex-direction: column;}
        .applicant-photo .value {min-height: 55mm;}
        .header {display: flex; justify-content: space-between; align-items: center; height: 20mm;}
        .title {font-size: 18px; font-weight: bold;}
        .content {display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 4mm;flex: 1;}
        .section {display: flex; flex-direction: column;}
        .bordered{ border: 1px solid #000; padding: 3mm; }
        .section h3 {margin: 0 0 0 0; font-size: 15px; font-weight: bold;}
        .field {margin-bottom: 2mm;}
        .label {font-size: 10px; font-weight: bold;}
        .muted {font-size: 9px; margin: 0 0 10px 0; }
        .value {border: 1px solid #000; padding: 1mm;min-height: 5mm; font-size: 11px;}
        .images {display: flex; justify-content: space-between; margin-bottom: 3mm;}
        .images > img {max-height: 25mm; object-fit: contain;}
        .footer {margin-top: 1mm; font-size: 9px;}
        .qr img {height: 20mm;}
        .signature img {height: 10mm;}
    </style>
</head>
<div class="page">

    <!-- HEADER -->
    <div class="header">
        <div class="title">
            UKVI Tuberculosis (TB) Clearance Certificate
        </div>
        <div class="qr">
            <img src="/img/background/logo/snh-square.png">
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">

        <!-- LEFT COLUMN -->
        <div class="section">
            <div class="images">
                <img src="/img/background/logo/uk-visa.png">
                <div class="field applicant-photo">
                    <div class="label">Applicant Photo</div>
                    <div class="value">
                        <img src="/img/applicants/{{$appointment->patient['image']}}">
                    </div>
                </div>
                
            </div>
            <div class="bordered">
                <div class="field">
                    <div class="label">Clinic Name</div>
                    <div class="value">St. Nicholas Hospital</div>
                </div>

                <div class="field">
                    <div class="label">Town / City</div>
                    <div class="value">Lagos Island</div>
                </div>

                <div class="field">
                    <div class="label">Certificate Ref</div>
                    <div class="value">{{$appointment->unique_id}}</div>
                </div>

                <div class="field">
                    <div class="label">Issue Date</div>
                    <div class="value">{{date('d M Y', strtotime($appointment->issue_at))}}</div>
                </div>

                <div class="field">
                    <div class="label">Expiry Date</div>
                    <div class="value">{{date('d M Y', strtotime("+6 months", strtotime($appointment->issue_at)))}}</div>
                </div>
            </div>
        </div>

        <!-- MIDDLE COLUMN -->
        <div class="section">
            <div class="bordered">
                <h3>Applicant Details</h3>
                <span class="muted">As shown in passport</span>
                <div class="field">
                    <div class="label">Full Name</div>
                    <div class="value">{{$appointment->patient->last_name}}, {{$appointment->patient->first_name}} {{$appointment->patient->middle_name}}</div>
                </div>

                <div class="field">
                    <div class="label">Nationality</div>
                    <div class="value">{{$appointment->patient->nationality->name}}</div>
                </div>

                <div class="field">
                    <div class="label">Date of Birth</div>
                    <div class="value">{{date('d M Y', strtotime($appointment->patient->dob))}}</div>
                </div>

                <div class="field">
                    <div class="label">Sex</div>
                    <div class="value">{{$appointment->patient->sex}}</div>
                </div>

                <div class="field">
                    <div class="label">Passport No</div>
                    <div class="value">{{$appointment->patient->passport_no}}</div>
                </div>
            </div>

            <div class="bordered" style="margin-top: 10px">
                <h3>Screening</h3>
                <span class="muted">No clinical suspicion of active pulmonary TB</span>
                <div class="field">
                    <div class="label">Physician</div>
                    <div class="value">Dr. {{$appointment->issuing_officer->first_name}} {{$appointment->issuing_officer->last_name}}</div>
                </div>

                <div class="field signature">
                    <div class="label">Signature</div>
                    <div class="value">
                        <img src="https://intranet.saintnicholashospital.com/img/consents/{{$appointment->issuer == 56 ? 'rusman.png' :($appointment->issuer == 57 ? 'rabudah.png' : ($appointment->issuer == 55 ? 'bsalami.png' : ($appointment->issuer == 2771 ? 'bakinloye.png' : 'bsalami.png')))}}">
                    </div>
                </div>
            </div>
        </div>
        <!-- RIGHT COLUMN -->
        <div class="section">
            <div class="bordered">
                <h3>Contact Information</h3>
                <div class="field">
                    <div class="label">Residential address line 1</div>
                    <div class="value">{{$appointment->patient->nigerian_address_street}}</div>
                </div>
                <div class="field">
                    <div class="label">Residential address line 2</div>
                    <div class="value">{{$appointment->patient->nigerian_address_street2}}</div>
                </div>
                <div class="field">
                    <div class="label">Town or City</div>
                    <div class="value">{{$appointment->patient->nigerian_address_city}}</div>
                </div>
                <div class="field">
                    <div class="label">Country</div>
                    <div class="value">{{$appointment->patient->nigerian_address_country}}</div>
                </div>
                <div class="field">
                    <div class="label">Proposed UK address line 1</div>
                    <div class="value">{{$appointment->patient->uk_address_street}}</div>
                </div>
                <div class="field">
                    <div class="label">Proposed UK address line 2</div>
                    <div class="value">{{$appointment->patient->uk_address_street2}}</div>
                </div>
                <div class="field">
                    <div class="label">Town or City</div>
                    <div class="value">{{$appointment->patient->uk_address_city}}</div>
                </div>
                <div class="field">
                    <div class="label">Postcode</div>
                    <div class="value">{{$appointment->patient->uk_address_postcode}}</div>
                </div>
            </div>
            <div class="qr" style="text-float: right; margin-top: 10px">
                {!! QrCode::size(80)->generate(
                    'https://intranet.saintnicholashospital.com/certificates/'.$appointment->id.'?f='.$appointment->patient->first_name.'&p='.$appointment->patient->passport_no
                ) !!}
            </div>
        </div>
    </div>
    <div class="footer">
        The information contained within this document is for UK visa application purposes only. Scan the QR code to verify authenticity.
    </div>
</div>
</body>
</html>