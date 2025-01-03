<!-- resources/views/verification/success.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="text-align: center; font-family: Arial, sans-serif; padding-top: 50px;">

<h1>{{ $message }}</h1>
<p style="padding-bottom: 16px">Adres email zweryfikowany pomyślnie!</p>

<!-- Button to navigate to your frontend page (app url) -->
<a href="{{ env('APP_URL') }}"
   style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; font-size: 16px;">
    Wróć do aplikacji
</a>

</body>
</html>
