<!DOCTYPE html>
<html>
<head>
    <title>Willkommen</title>
</head>
<body style="font-family: Arial, sans-serif;">
<h2>Hallo {{ $user->vorname }},</h2>

<p>Ein Admin hat für dich einen Account erstellt.</p>
<p>Bitte klicke auf den folgenden Link, um dein Passwort festzulegen und dich anzumelden:</p>

<p>
    <a href="{!! $link !!}" style="background-color: #1976D2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        Passwort festlegen
    </a>
</p>

<p>
    <small>Falls der Button nicht funktioniert, kopiere diesen Link:<br>
        {!! $link !!}</small>
</p>
</body>
</html>
