<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body>
    <p>Hello {{ $fullName }},</p>
    <p>Click <a href="{{ $resetUrl }}">here</a> to reset your password.</p>
    <p>This link will expire in 6 hours.</p>
</body>
</html>
