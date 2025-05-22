<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset Request</h1>
    <p>Hello {{ $fullName }},</p>
    <p>Please click the link below to reset your password:</p>
    <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    <p>This link will be valid for 60 minutes.</p>
    <br>
    <p>Kind regards,</p>
    <p>{{ config('mail.from.name') }}</p>
</body>
</html>
