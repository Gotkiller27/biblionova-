# bugs.md — Bug report (problèmes techniques repérés)

## A) Backend / API

### A1) Controller/AuthService: `resetPassword()` n’utilise pas `success`/message de façon standard (mais surtout dépend de `Password::reset`)
- Le code renvoie une exception si le status n’est pas `PASSWORD_RESET`.
- Pas forcément un bug, mais la gestion d’erreur côté front peut être limitée (le front attend parfois `message`/`errors`).
- Fichier: `backend/app/Services/AuthService.php` (méthode `resetPassword`)

### A2) Middleware/compte: blocage possible sur login si `status !== active`
- C’est intentionnel, mais ça peut apparaître comme “connexion cassée”.
- Fichier: `backend/app/Services/AuthService.php` (dans `login()`)
- Fichier: `backend/app/Http/Middleware/CheckAccountStatus.php`

### A3) Redirection role: route strings potentiellement incorrectes par rapport au router Vue
- Backend renvoie :
  - `'admin' => '/admin/dashboard'`, etc.
- Front router a bien ces paths (vue), mais attention au mismatch si un jour le front change.
- Fichier: `backend/app/Services/AuthService.php` (getRedirectUrlByRole)

---

## B) Frontend / Vue / Pinia / Axios

### B1) Probable bug critique : absence de CSRF handshake avant login (cause connexion)
- Déjà décrit dans `auth_pb.md`.
- Fichier: `frontend/src/stores/auth.js`, `frontend/src/plugins/axios.js`

### B2) `axios` baseURL configurable : risque de mismatch env (host/port)
- Si `VITE_API_URL` n’est pas défini, fallback sur `http://localhost:8000`.
- Or le debug utilise `http://127.0.0.1:7777/event` (autre port).
- Risque: l’app hit un autre backend que celui que tu penses.
- Fichier: `frontend/src/plugins/axios.js`

### B3) Router meta: `admin` route sans `.meta.roles` dans un guard explicite
- Ton guard Vue utilise `to.meta.requiresAuth` + `to.meta.roles`.
- Pour `/admin/dashboard`, tu as : `meta: { requiresAuth: true, roles: ['admin'] }`.
- Donc OK. Mais pour d’autres routes (ex `/dashboard`), `meta: { requiresAuth: true }` sans roles : OK.
- Pas un bug, mais point fragile à maintenir.
- Fichier: `frontend/src/router/index.js`

### B4) Gardes: variable `authInitialized`/`isLoading` peut empêcher des checks nécessaires
- Tu init à chaque navigation seulement si `!authStore.authInitialized || authStore.isLoading`.
- Si `authInitialized` devient true trop tôt, tu peux rater un changement de session (ex: cookie invalide, session expirée).
- Fichier: `frontend/src/router/index.js`, `frontend/src/stores/auth.js`

### B5) Intercepteur axios: logs mais pas de traitement cohérent 419
- Dans `plugins/axios.js`:
  - si 419 → console.log('CSRF token expiré')
  - mais aucune tentative de refresh/CSRF-cookie n’est faite.
- Donc au lieu d’autoguérir, l’erreur remonte et la connexion reste cassée.
- Fichier: `frontend/src/plugins/axios.js`

### B6) Login/logout: incohérence sur la forme de retour et mapping `user` vs `data.data.user`
- Dans `auth.js`:
  - `login()` : `this.user = response.data.data.user` (OK si backend renvoie `user` dans `sendResponse`)
  - `register()` : `this.user = response.data.data` (potentielle incohérence si `sendResponse` enveloppe).
- Sans voir `BaseApiController::sendResponse`, ça peut casser.
- Fichier: `frontend/src/stores/auth.js`
- Dépendant: `backend/app/Http/Controllers/Api/BaseApiController.php` (non lu ici)

---

## C) Tests / Qualité

### C1) L’existence de tests ne garantit pas le comportement front
- Tu as `backend/tests/Feature/PasswordResetTest.php`.
- Ça ne couvre pas forcément le login/session/CSRF côté SPA.

---

## Liste “top suspects” à traiter en priorité
1. CSRF/Sanctum handshake avant login (bloquant probable)
2. Confirmer que `VITE_API_URL` pointe bien sur le bon backend (cookies)
3. Ajouter une stratégie de récupération si 419 (refetch `sanctum/csrf-cookie` puis retry)
4. Vérifier la structure JSON retournée par `sendResponse` vs ce que le store `auth.js` lit


