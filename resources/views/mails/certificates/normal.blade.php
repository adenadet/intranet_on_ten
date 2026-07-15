<!DOCTYPE html>
<html>
    <head>
        <title>UK TB Screening Certificate</title>
    </head>
    <body>
        <h1>Hello {{$appointment->patient->first_name}}</h1>
        <p>Thank you for choosing St. Nicholas Hospital.</p>
        <p>Your appointment for <strong>{{$appointment->service->name}}</strong> has been successfully completed.</p> 
        <p>Your certificate is now available for viewing and printing. For the best print quality, we recommend using Conqueror paper.</p>
        <p>To access your certificate, please <a href="https://intranet.saintnicholashospital.com/certificates/{{$appointment->id}}?p={{$appointment->patient->passport_no}}&f={{$appointment->patient->first_name}}" target="_blank"> click here </a>. </p> 
        <p>If you experience any difficulty accessing the certificate, please contact the hospital for assistance.</p> 
        <p>Thank you.</p>
        <p> Kind regards,<br> St. Nicholas Hospital </p>
    </body>
</html>