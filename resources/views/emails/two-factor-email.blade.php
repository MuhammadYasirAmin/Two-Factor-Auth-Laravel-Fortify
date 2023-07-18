<!DOCTYPE html>
<html lang="en">
<head>
    <title>1Click Portal.com</title>
</head>
<body>
<h1>{{ $mailData['title'] }}</h1>
<p>{{ $mailData['body'] }}</p>

<p>Your Email Verification Code is {{ $mailData['code'] }}</p>

<p>Thank you</p>
</body>
</html>
