<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zweryfikuj adres email</title>
    <style>
        body {
            color: #333;
            margin: 0;
            padding: 0;
        }

        .layout {
            padding: 32px 0;
            width: 100%;
            height: 100%;
            font-family: "Roboto", serif;
            font-weight: 300;
            font-style: normal;
            color: #333333 !important;
            background-color: #edf2f7;
        }

        .container {
            margin: 30px auto;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #56809E;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            font-size: 18px;
            text-transform: uppercase;
        }

        .content {
            padding: 20px;
        }

        .content p {
            font-size: 16px;
            margin: 20px 0;
        }

        .btn-container {
           display: flex;
        }

        .btn {
            display: block;
            align-self: center;
            padding: 12px 20px;
            width: 130px;
            margin: 20px auto;
            background-color: #C58254;
            color: #ffffff !important;
            text-transform: uppercase;
            font-weight: 400;
            text-decoration: none;
            font-size: 14px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn:hover {
            background-color: #8F5830;
        }

        .footer {
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="layout">
    <div class="container">
        <div class="header">
            Zweryfikuj adres email
        </div>
        <div class="content">
            <p>Dzień dobry <strong>{{ $user->getUserFullName() }}</strong>,</p>
            <p>Dziękuję za rejestrację na mojej stronie. Proszę, kliknij na przycisk poniżej, aby zweryfikować Twój adres email:</p>
                <p class="btn-container">
                    <a href="{{ $verificationUrl }}" class="btn">Zweryfikuj email</a>
                </p>
            <hr/>
            <p>Jeżeli to nie Ty utworzyłeś konto, zignoruj tą wiadomość.</p>
            <p>Z poważaniem, <br> Katarzyna Trzeciakiewicz</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Wszelkie prawa zastrzeżone.
        </div>
    </div>
</div>
</body>
</html>
