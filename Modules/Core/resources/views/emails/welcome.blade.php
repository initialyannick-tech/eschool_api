<x-mail::message>
# Bienvenue sur Eschool +, {{ $user->prenom }} !

Bonjour {{ $user->prenom }} {{ $user->nom }},

Votre compte a été créé avec succès sur **Eschool +**, notre application de gestion scolaire. Vous pouvez désormais accéder à votre compte et utiliser nos services.

Voici vos informations de connexion :

- **Nom d'utilisateur** : {{ $user->email }}
- **Rôle** : {{ $user->role->libelle }}
- **Mot de passe** : {{ $password }}

Vous pouvez vous connecter en utilisant l'email que vous avez fourni et le mot de passe que vous avez défini.

Si vous avez des questions ou des préoccupations, n'hésitez pas à nous contacter.

Merci,
L'équipe IT & Système Eschool

</x-mail::message>
