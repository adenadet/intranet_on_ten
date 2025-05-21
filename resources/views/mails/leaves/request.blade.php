<!DOCTYPE html>
<html>
    <head>
        <title>Leave Request Notification</title>
    </head>
    <body>
        <h1>Dear {{$line_manager->first_name}},</h1>
        <p>{{$employee->user->first_name}} {{$employee->user->last_name}} ({{$employee->username}}) has requested your approval to proceed on a Leave/Vacation.</p>
        <p>You can view this leave request by logging in to the portal using the link below. <b><a href="https://intranet.saintnicholashospital.com/hrms/leave_management/confirm_request/{{$leave_request->id}}" target="_blank">click here</a></b></p>
        <p>Thank you for your cooperation in this matter.</p>
        <p>Regards,<br >HR Team</p>
    </body>
</html>