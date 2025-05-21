<!DOCTYPE html>
<html>
    <head>
        <title>Leave Request Confirmation</title>
    </head>
    <body>
        <h1>Hello {{$employee->first_name}}</h1>
        <p>This is to notify you that the status of your leave/vacation request has been modified.</p>
        <p>You can view this leave request by logging in to the portal using the link below.</p>
        <p><a href="{{config('app.url')}}/{{route('login')}}">Click here to login</a></p>
        <p>Best regards,<br >Webmaster, <br >HRMS</p>
    </body>
</html>