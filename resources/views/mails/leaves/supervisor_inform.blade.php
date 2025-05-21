<!DOCTYPE html>
<html>
    <head>
        <title>Leave Request Notification</title>
    </head>
    <body>
        <h1>Hello {{$supervisor->last_name}}</h1>
        <p>{{$employee->user->first_name}} {{$employee->user->last_name}} ({{$employee->unique_id}}) has requested for apporval to proceed on Leave/Vacation.
        <p>His line manager ({{$line_manager->first_name}} {{$line_manager->last_name}} ) has been notified but as his Executive Lead/Supervisor, this is to ensure that you are informed.</p>
        <p>Thank you.</p>

        <p>Regards,<br >HR Team</p>
    </body>
</html>