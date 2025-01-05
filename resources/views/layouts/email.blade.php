<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', 'Notification')</title>
    <style>
        body {
            color: #333;
            margin: 0;
            padding: 0;
        }

        .layout {
            padding: 24px 0;
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

        .button-container {
            display: flex;
        }

        .btn {
            display: block;
            align-self: center;
            padding: 12px 20px;
            width: 130px;
            margin: 32px auto;
            background-color: #C58254;
            color: #ffffff !important;
            text-transform: uppercase;
            text-align: center;
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

        hr {
            margin-top: 20px;
            border: 0;
            border-top: 1px solid #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="layout">
    <div class="container">
        <div class="header">

            @yield('header', config('app.name'))

        </div>
        <div class="email-body">

            <div class="content">

                @yield('content')

                <hr/>

                @yield('greetings')

            </div>
        </div>

        <div class="footer">

            <p> To jest wiadomość wygenerowana automatycznie. Prosimy na nią nie odpowiadać.</p>

            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Wszelkie prawa zastrzeżone.</p>

        </div>
    </div>
</div>
</body>
</html>
