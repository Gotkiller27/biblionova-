# 🚀 NEXADOC - Guide de démarrage Frontend

## ✅ Installation et démarrage

### 1. Installer les dépendances
```bash
cd frontend
npm install
```

### 2. Configurer l'environnement
Le fichier `.env` est déjà créé avec la configuration :
```env
VITE_API_URL=http://localhost:8000
VITE_APP_URL=http://localhost:5173
VITE_APP_NAME=NEXADOC
```

### 3. Démarrer l'application
```bash
npm run dev
```

L'application sera accessible sur : **http://localhost:5173**

## 🔐 Test de l'authentification

### Comptes de test disponibles

Une fois que le backend Laravel est démarré et les seeders exécutés, vous pouvez vous connecter avec :

#### Admin
- **Email**: admin@nexadoc.com
- **Password**: password
- **Dashboard**: `/admin/dashboard`

#### Responsable RH  
- **Email**: rh@nexadoc.com
- **Password**: password
- **Dashboard**: `/rh/dashboard`

#### Responsable Validation
- **Email**: validation@nexadoc.com  
- **Password**: password
- **Dashboard**: `/validation/dashboard`

#### Bibliothécaire
- **Email**: bibliothecaire@nexadoc.com
- **Password**: password  
- **Dashboard**: `/bibliothecaire/dashboard`

#### Utilisateur
- **Email**: user@nexadoc.com
- **Password**: password
- **Dashboard**: `/dashboard`

### Création de nouveaux comptes
Vous pouvez également créer de nouveaux comptes via la page d'inscription : `/auth/register`

## 🎯 Fonctionnalités implémentées

### ✅ Authentification complète
- [x] Connexion/Déconnexion
- [x] Inscription
- [x] Mot de passe oublié 
- [x] Réinitialisation mot de passe
- [x] Gestion des sessions sécurisées (Sanctum SPA)
- [x] Protection CSRF
- [x] Vérification automatique d'authentification

### ✅ Gestion des rôles
- [x] 5 rôles avec permissions distinctes
- [x] Redirections automatiques par rôle
- [x] Protection des routes par rôle
- [x] Menus dynamiques selon le rôle

### ✅ Dashboards par rôle
- [x] **Admin** : Vue d'ensemble système, gestion utilisateurs, statistiques
- [x] **RH** : Gestion utilisateurs, rapports RH  
- [x] **Validation** : Demandes à valider, historique validations
- [x] **Bibliothécaire** : Catalogue, publications, archives
- [x] **Utilisateur** : Mes demandes, catalogue, nouveau dépôt

### ✅ Interface utilisateur
- [x] Design moderne avec Tailwind CSS
- [x] Icônes Iconify
- [x] Responsive design
- [x] Animations et transitions
- [x] États de chargement
- [x] Gestion d'erreurs
- [x] Messages de succès/erreur

### ✅ Navigation et UX
- [x] Router avec protection des routes
- [x] Layout principal avec sidebar
- [x] Menu utilisateur avec avatar
- [x] Breadcrumb navigation
- [x] Page 404 personnalisée
- [x] Page profil utilisateur

## 🔄 Flow d'authentification

### 1. Première visite
- Redirection automatique vers `/auth/login`
- Formulaire de connexion avec validation
- "Se souvenir de moi" optionnel

### 2. Après connexion réussie  
- Redirection automatique selon le rôle :
  - Admin → `/admin/dashboard`
  - RH → `/rh/dashboard`  
  - Validation → `/validation/dashboard`
  - Bibliothécaire → `/bibliothecaire/dashboard`
  - Utilisateur → `/dashboard`

### 3. Navigation protégée
- Vérification automatique des permissions
- Redirection si rôle insuffisant
- Déconnexion automatique si session expirée

### 4. Déconnexion
- Nettoyage complet des cookies/sessions
- Redirection vers page de connexion

## 🛠 Architecture technique

### Structure des fichiers
```
frontend/src/
├── components/          # Composants réutilisables
├── layouts/            # Layouts (DashboardLayout)
├── plugins/            # Configuration (axios)
├── router/             # Configuration des routes
├── stores/             # Stores Pinia (auth)
├── views/
│   ├── auth/          # Pages d'authentification  
│   ├── dashboards/    # Dashboards par rôle
│   └── ...            # Autres vues
├── App.vue            # Composant racine
└── main.js           # Point d'entrée
```

### Technologies utilisées
- **Vue 3** : Framework principal
- **Pinia** : Gestion d'état
- **Vue Router** : Routage
- **Axios** : HTTP client
- **Tailwind CSS** : Framework CSS
- **Iconify** : Icônes
- **Vite** : Build tool

### Configuration Axios/Sanctum
- Configuration automatique CSRF
- Gestion cookies HTTPOnly
- Intercepteurs pour erreurs 401
- Base URL configurable

## 🧪 Points de test

### Authentification
1. **Connexion** : Tester avec différents rôles
2. **Redirection** : Vérifier redirection correcte par rôle  
3. **Protection** : Essayer d'accéder aux routes protégées sans auth
4. **Permissions** : Tester accès limité par rôle
5. **Déconnexion** : Vérifier nettoyage sessions

### Interface
1. **Responsive** : Tester sur mobile/tablet/desktop
2. **Navigation** : Tester tous les liens du menu
3. **Formulaires** : Validation côté client
4. **États** : Loading, erreurs, succès
5. **Accessibilité** : Navigation clavier, contraste

## 🔧 Développement

### Commandes utiles
```bash
# Développement
npm run dev

# Build production  
npm run build

# Preview build
npm run preview

# Lint
npm run lint
```

### Variables d'environnement
Modifiez `.env` selon vos besoins :
```env
VITE_API_URL=http://localhost:8000    # URL API Laravel
VITE_APP_URL=http://localhost:5173    # URL Frontend
```

## 🎉 Résultat attendu

Vous devriez avoir une application Vue.js 3 complètement fonctionnelle avec :
- ✅ Authentification sécurisée Sanctum SPA
- ✅ 5 dashboards distincts par rôle
- ✅ Interface moderne et responsive  
- ✅ Navigation fluide et intuitive
- ✅ Gestion d'erreurs robuste

**L'application est prête pour la démonstration et le développement des fonctionnalités métier !** 🚀