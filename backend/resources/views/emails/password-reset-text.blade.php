Bonjour {{ $user->first_name }} {{ $user->last_name }},

Vous avez demandé la réinitialisation de votre mot de passe pour votre compte NEXADOC.

Cliquez sur le lien ci-dessous pour créer un nouveau mot de passe :

{{ $resetUrl }}

Ce lien expirera dans 1 heure.

Si vous n'avez pas demandé cette réinitialisation, ignorez cet email. Votre mot de passe restera inchangé.

Cordialement,
L'équipe NEXADOC

---
Cet email a été envoyé automatiquement. Merci de ne pas y répondre.
© {{ date('Y') }} NEXADOC. Tous droits réservés.
