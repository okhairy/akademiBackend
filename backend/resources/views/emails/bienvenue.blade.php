<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
</head>
<body>
    <h2>Bonjour {{ $details['nom'] }} {{ $details['prenom'] }},</h2>
    <p>Bienvenue sur notre plateforme !</p>
    <p>Votre mot de passe temporaire est : <strong>{{ $details['mot_de_passe'] }}</strong></p>
    <p>Veuillez le modifier dès votre première connexion.</p>
    <p>Accédez à votre compte via le lien suivant :</p>
    <a href="{{ url('http://localhost:4200/login') }}">Se connecter</a>

    <p>Merci de nous faire confiance !</p>
</body>
</html>
