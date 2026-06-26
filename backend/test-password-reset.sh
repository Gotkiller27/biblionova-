#!/bin/bash

# Script de test pour Password Reset et Standardisation API
# Usage: bash test-password-reset.sh

echo "🔐 NEXADOC - Test Password Reset & API Standardization"
echo "===================================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Base URL
BASE_URL="http://localhost:8000/api/v1"

echo -e "${YELLOW}📋 Étape 1: Vérification des fichiers créés${NC}"

# Vérifier que les fichiers existent
FILES=(
    "app/Helpers/ApiResponse.php"
    "app/Http/Controllers/BaseApiController.php"
    "app/Services/AuthService.php"
    "app/Mail/PasswordResetMail.php"
    "app/Http/Requests/ForgotPasswordRequest.php"
    "app/Http/Requests/ResetPasswordRequest.php"
    "app/Http/Requests/ChangePasswordRequest.php"
    "resources/views/emails/password-reset.blade.php"
    "resources/views/emails/password-reset-text.blade.php"
    "app/Models/PasswordResetToken.php"
    "app/Console/Commands/CleanExpiredTokensCommand.php"
    "tests/Feature/PasswordResetTest.php"
    "database/seeders/PasswordResetSeeder.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file (MANQUANT)"
    fi
done

echo ""
echo -e "${YELLOW}📋 Étape 2: Migration et seeds${NC}"

read -p "Voulez-vous exécuter les migrations et seeds ? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Exécution des migrations..."
    php artisan migrate
    
    echo "Création des rôles..."
    php artisan db:seed --class=RoleSeeder
    
    echo "Création des utilisateurs de test..."
    php artisan db:seed --class=UserSeeder
    
    echo "Création des tokens de test..."
    php artisan db:seed --class=PasswordResetSeeder
    
    echo -e "${GREEN}✓ Base de données configurée${NC}"
fi

echo ""
echo -e "${YELLOW}📋 Étape 3: Test des endpoints${NC}"

# Créer un utilisateur de test
echo "Création d'un utilisateur de test..."
TEST_EMAIL="test-reset@example.com"

# Test 1: Forgot Password
echo ""
echo -e "${YELLOW}Test 1: Forgot Password${NC}"
curl -X POST "$BASE_URL/auth/forgot-password" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$TEST_EMAIL\"}" \
  -w "\nStatus: %{http_code}\n"

# Test 2: Forgot Password avec email invalide
echo ""
echo -e "${YELLOW}Test 2: Forgot Password - Email invalide${NC}"
curl -X POST "$BASE_URL/auth/forgot-password" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"invalid-email\"}" \
  -w "\nStatus: %{http_code}\n"

# Test 3: Reset Password avec token invalide
echo ""
echo -e "${YELLOW}Test 3: Reset Password - Token invalide${NC}"
curl -X POST "$BASE_URL/auth/reset-password" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$TEST_EMAIL\",\"token\":\"invalid-token\",\"password\":\"newpassword123\",\"password_confirmation\":\"newpassword123\"}" \
  -w "\nStatus: %{http_code}\n"

echo ""
echo -e "${YELLOW}📋 Étape 4: Test format des réponses API${NC}"

# Test format standardisé
echo ""
echo -e "${YELLOW}Test format succès:${NC}"
curl -X GET "$BASE_URL/categories" \
  -H "Accept: application/json" \
  -w "\nStatus: %{http_code}\n"

echo ""
echo -e "${YELLOW}Test format erreur:${NC}"
curl -X GET "$BASE_URL/nonexistent-endpoint" \
  -H "Accept: application/json" \
  -w "\nStatus: %{http_code}\n"

echo ""
echo -e "${YELLOW}📋 Étape 5: Commandes Artisan${NC}"

echo "Test commande de nettoyage des tokens..."
php artisan auth:clean-expired-tokens

echo ""
echo -e "${YELLOW}📋 Étape 6: Tests automatisés${NC}"

read -p "Voulez-vous exécuter les tests automatisés ? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Exécution des tests..."
    php artisan test tests/Feature/PasswordResetTest.php
fi

echo ""
echo -e "${GREEN}✅ Tests terminés !${NC}"
echo ""
echo -e "${YELLOW}📖 Documentation des endpoints :${NC}"
echo "POST /auth/forgot-password     - Demande de reset"
echo "POST /auth/reset-password      - Reset avec token"
echo "POST /auth/change-password     - Changement (auth)"
echo ""
echo -e "${YELLOW}📊 Format des réponses :${NC}"
echo "Succès: {\"success\": true, \"message\": \"...\", \"data\": {...}}"
echo "Erreur: {\"success\": false, \"message\": \"...\", \"errors\": {...}}"
echo ""
echo -e "${GREEN}🎉 Password Reset & API Standardization implémentés !${NC}"