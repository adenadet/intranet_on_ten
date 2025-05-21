<!DOCTYPE html>
<html>
    <head>
        <title>Leave Request Confirmation Notification</title>
    </head>
    <body>
        <h1>Dear {{$supervisor->last_name}},</h1>
        <p>This is to inform you that the leave request of {{$employee->user->first_name}} {{$employee->user->last_name}} has been approved by {{$line_manager->first_name}} {{$line_manager->last_name}}. The leave will be from {{$leave_request->start_date}} to {{$leave_request->end_date}}.</p>
        <p>This email is to ensure that you are kept informed</p>
        <p>Best regards,<br >Webmaster, <br >HRMS</p>
    </body>
</html>