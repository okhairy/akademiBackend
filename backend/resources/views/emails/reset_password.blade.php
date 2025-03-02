<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation de mot de passe</title>
</head>
<body>
    <p>Bonjour,</p>
    <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
    <p><a href="{{ url('http://localhost:4200/change/' . $token) }}">Réinitialiser mon mot de passe</a></p>
    <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
    <p>Merci,</p>
    <p>L'équipe de support</p>
</body>
</html>