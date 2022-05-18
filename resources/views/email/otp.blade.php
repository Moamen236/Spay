<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email</title>
</head>
<body style="background-color: #F8F8F8">
    <div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
        <div style="margin:50px auto;width:70%;padding:20px 0">
            <div style="border-bottom:1px solid #aaa">
                <img src="{{ asset('img/logo.jpeg') }}" alt="" width="120">
            </div>
            <h1 style="font-size: 20px;">Hi, {{ $client['name'] }}</h1>
            <p style="font-size: 14px;">Thanks for starting the new Spay account creation process. We want to make sure it's really you. Please enter the following verification code when prompted. If you don’t want to create an account, you can ignore this message.</p>
            <p style="text-align: center;margin-bottom: 0;font-size: 14px;"><strong>Verification code</strong></p>
            <h2 style="color: #000; font-size: 36px; font-weight: bold;text-align:center;margin-top: -10px; margin-bottom: 0;">{{ $client['otp'] }}</h2>
            <p style="font-size:0.9em;">Regards,<br />Spay</p>
            <hr style="border:none;border-top:1px solid #aaa" />
            <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
                <p>Spay Inc</p>
                <p>Egypt</p>
            </div>
        </div>
    </div>
</body>
</html>