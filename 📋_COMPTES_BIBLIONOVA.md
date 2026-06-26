# 📋 COMPTES UTILISATEURS - BIBLIONOVA

## 🔐 COMPTES DE TEST POUR TOUS LES RÔLES

### 🌟 **ADMINISTRATEUR**
- **Email** : admin@biblionova.com
- **Mot de passe** : admin123
- **Nom** : Super Admin
- **Rôle** : admin
- **Dashboard** : `/admin/dashboard`
- **Permissions** : Accès complet au système

### 👥 **RESPONSABLE RH**
- **Email** : rh@biblionova.com
- **Mot de passe** : rh123
- **Nom** : Marie Ressources
- **Rôle** : responsable_rh
- **Dashboard** : `/rh/dashboard`
- **Permissions** : Gestion des utilisateurs

### ✅ **RESPONSABLE VALIDATION**
- **Email** : validation@biblionova.com
- **Mot de passe** : validation123
- **Nom** : Jean Validation
- **Rôle** : responsable_validation
- **Dashboard** : `/validation/dashboard`
- **Permissions** : Validation des demandes de dépôt

### 📚 **BIBLIOTHÉCAIRE**
- **Email** : bibliothecaire@biblionova.com
- **Mot de passe** : biblio123
- **Nom** : Sophie Bibliothèque
- **Rôle** : bibliothecaire
- **Dashboard** : `/bibliothecaire/dashboard`
- **Permissions** : Gestion du catalogue et publication

### 👤 **UTILISATEUR STANDARD**
- **Email** : user@biblionova.com
- **Mot de passe** : user123
- **Nom** : Pierre Utilisateur
- **Rôle** : utilisateur
- **Dashboard** : `/dashboard`
- **Permissions** : Consultation et dépôt de demandes

---

## 👥 **COMPTES SUPPLÉMENTAIRES POUR TESTS**

### 📖 **Utilisateurs Standards**
| Email | Mot de passe | Nom | Rôle |
|-------|-------------|-----|------|
| alice.martin@biblionova.com | alice123 | Alice Martin | utilisateur |
| bob.dupont@biblionova.com | bob123 | Bob Dupont | utilisateur |

### 📚 **Bibliothécaires Supplémentaires**
| Email | Mot de passe | Nom | Rôle |
|-------|-------------|-----|------|
| claire.durand@biblionova.com | claire123 | Claire Durand | bibliothecaire |

---

## 🎯 **COMMENT UTILISER CES COMPTES**

### 1. **Test de Connexion par Rôle**
```bash
# Tester chaque rôle sur http://localhost:5173/auth/login
1. admin@biblionova.com / admin123 → Redirection vers /admin/dashboard
2. rh@biblionova.com / rh123 → Redirection vers /rh/dashboard  
3. validation@biblionova.com / validation123 → Redirection vers /validation/dashboard
4. bibliothecaire@biblionova.com / biblio123 → Redirection vers /bibliothecaire/dashboard
5. user@biblionova.com / user123 → Redirection vers /dashboard
```

### 2. **Test de Permissions**
- **Admin** : Peut accéder à toutes les sections
- **RH** : Peut gérer les utilisateurs
- **Validation** : Peut approuver/rejeter les demandes
- **Bibliothécaire** : Peut publier les références
- **Utilisateur** : Peut soumettre des demandes

### 3. **Test de Sécurité**
- Chaque rôle ne peut accéder qu'à ses dashboards autorisés
- Tentative d'accès non autorisé → Redirection automatique
- Déconnexion sécurisée pour tous les comptes

---

## 🚀 **CRÉATION DES COMPTES**

### Via Seeder (Recommandé)
```bash
cd backend
php artisan db:seed --class=UsersSeeder
```

### Via Script Artisan
```bash
php artisan tinker
# Puis utiliser les données du seeder
```

---

## 🎨 **PERSONNALISATION BIBLIONOVA**

### Nom de l'Application
- **Ancien** : NEXADOC
- **Nouveau** : BiblioNova
- **Description** : Bibliothèque numérique nouvelle génération

### Domaines Email
- **Format** : [role]@biblionova.com
- **Exemple** : admin@biblionova.com, user@biblionova.com

### Dashboards Personnalisés
- Interface adaptée au branding BiblioNova
- Couleurs et design cohérents
- Navigation optimisée par rôle

---

## 🎊 **RÉSUMÉ**

**8 comptes utilisateur créés** couvrant tous les rôles :
- ✅ **1 Administrateur** - Contrôle total
- ✅ **1 Responsable RH** - Gestion utilisateurs  
- ✅ **1 Responsable Validation** - Validation demandes
- ✅ **2 Bibliothécaires** - Gestion catalogue
- ✅ **3 Utilisateurs** - Utilisation standard

**Tous les comptes sont actifs et prêts à utiliser !** 🚀

---

*BiblioNova - Votre bibliothèque numérique moderne* 📚