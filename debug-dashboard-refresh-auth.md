# Debug Session: dashboard-refresh-auth

Status: OPEN

## Symptom
- Après actualisation de la page dashboard, l'application renvoie vers `/auth/login` alors que l'utilisateur venait de se connecter.

## Expected
- Un utilisateur déjà connecté doit rester authentifié après refresh.
- Un utilisateur déjà connecté ne doit plus pouvoir accéder aux routes invitées comme `/auth/login` tant qu'il ne clique pas sur déconnexion.

## Hypotheses
- H1: `initAuth()` reçoit une réponse négative après refresh parce que la session/cookie n'est pas renvoyée correctement au backend.
- H2: Le guard Vue Router déclenche une redirection vers `/auth/login` avant la fin réelle de la réhydratation auth.
- H3: La route backend `/api/v1/auth/check` ne reconnaît pas l'utilisateur après refresh à cause d'un problème Sanctum/session middleware.
- H4: La configuration frontend (`axios`, domaine, `withCredentials`, cookies) fait perdre la session entre `localhost` et `127.0.0.1`.
- H5: La navigation vers les routes `guest` n'est pas bloquée de façon fiable parce que `redirectUrl` / `isAuthenticated` ne sont pas cohérents après reload.

## Plan
1. Lire les fichiers auth/router/axios/backend impliqués.
2. Ajouter une instrumentation minimale sur le frontend pour tracer `initAuth()` et le guard.
3. Reproduire le refresh et collecter les observations.
4. Confirmer ou invalider les hypothèses.
5. Appliquer le correctif minimal puis revérifier.
