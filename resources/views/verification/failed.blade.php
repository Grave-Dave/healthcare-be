<!-- resources/views/verification/failed.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Failed</title>
</head>
<body style="text-align: center; font-family: Arial, sans-serif; padding-top: 50px;">

<h1>{{ $message }}</h1>
<p style="padding-bottom: 16px">Nie udało się zweryfikować adresu email.</p>

<!-- Button to redirect back to the login page or try again -->
<a href="{{ env('APP_URL') }}"
   style="padding: 10px 20px; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px; font-size: 16px;">
    Wróć do aplikacji
</a>

</body>
</html>
