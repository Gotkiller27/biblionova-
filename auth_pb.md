# auth_pb.md — Problème de connexion (cause + preuves)

## 1) Sanctum SPA + CSRF : le front ne fait pas le handshake CSRF
### Pourquoi ça casse la connexion
- Le backend utilise une authentification **session-based** via `Auth::attempt()` (pas un token stateless type JWT).
- Dans une configuration Laravel Sanctum “SPA cookies”, il faut généralement :
  - appeler `GET /sanctum/csrf-cookie` avant le premier `POST /auth/login` (pour que le cookie CSRF soit mis en place)
  - puis envoyer la requête avec les cookies (`withCredentials: true`) + CSRF valide.
- Côté frontend, `axios` est bien configuré avec `withCredentials: true` (donc cookies OK), **mais aucun appel** à `/sanctum/csrf-cookie` n’apparaît avant le `login()`.

### Preuves (fichiers)
- Front axios (cookies envoyés)
  - `frontend/src/plugins/axios.js`
  - `withCredentials: true`
- Front login n’exécute aucun `sanctum/csrf-cookie`
  - `frontend/src/stores/auth.js`
  - `login(credentials) { await axios.post('/api/v1/auth/login', credentials) }`
  - Aucun `csrf-cookie` dans `initAuth()` ni dans `login()`.

### Symptôme probable
- `GET /api/v1/auth/check` renvoie `authenticated=false`, car la session n’a jamais été correctement créée (requête login probablement rejetée côté session/CSRF).
- Tu peux aussi voir des erreurs 419 “CSRF token mismatch” dans la console réseau (même si ton code intercepteur ne log pas le body).

---

## 2) `auth/check` dépend d’une session qui n’est peut-être pas persistée
### Pourquoi ça casse
- `initAuth()` fait `GET /api/v1/auth/check`.
- Le backend `checkAuth()` renvoie `authenticated = Auth::check()`.
- Donc si la session n’est pas établie après le `POST /auth/login`, `check` restera toujours false.

### Preuves
- Backend
  - `backend/app/Http/Controllers/Api/Auth/AuthController.php`
  - `public function checkAuth(): JsonResponse { $authenticated = Auth::check(); ... }`
- Front
  - `frontend/src/stores/auth.js`
  - `const response = await axios.get('/api/v1/auth/check')`

---

## 3) Dépendance session + `request()->session()->regenerate()` possible, mais sans visibilité côté client
### Pourquoi c’est un risque
- Le backend régénère la session lors du login :
  - `request()->session()->regenerate();`
- Si la session cookie n’est pas correctement reçue/stockée (à cause de domaine/port CORS/cookies/CSRF), la régénération n’aidera pas : l’auth ne “persistera” pas.

### Preuves
- `backend/app/Services/AuthService.php`
  - dans `login()` : `request()->session()->regenerate();`
  - dans `logout()` : `request()->session()->invalidate()` + `regenerateToken()`.

---

## 4) Incohérence potentielle des endpoints (VITE_API_URL vs hardcodé debug)
### Pourquoi ça peut masquer un vrai problème
- Les appels applicatifs utilisent `baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000'`.
- Mais partout dans le front, tu fais aussi des `fetch('http://127.0.0.1:7777/event' ...)` (debug). Ça ne casse pas l’auth, mais ça montre que différents ports/hostnames sont utilisés.
- Si `VITE_API_URL` n’est pas le même host/port que le backend réellement servi, alors `login` et `check` ne toucheront pas le même serveur/basetoken/cookies.

### Preuves
- `frontend/src/plugins/axios.js`
  - baseURL configurable
- Appels API auth:
  - utilisent `axiosInstance` donc baseURL VITE.

---

## Conclusion (la cause la plus probable)
**La connexion ne marche pas très probablement parce que le front ne fait pas `GET /sanctum/csrf-cookie` avant le `POST /api/v1/auth/login`, alors que le backend est en logique session-based/Sanctum cookies.**

---

## Ce qu’il faut vérifier côté navigateur (pour confirmer)
1. Onglet Network → filtre `sanctum/csrf-cookie` : est-ce qu’il est appelé ? (ici: non)
2. Network → voir le `POST /api/v1/auth/login` :
   - status 419 ?
   - ou status 401/403 ?
3. Cookies (Application → Cookies):
   - est-ce que les cookies de session & CSRF apparaissent après le login ?


