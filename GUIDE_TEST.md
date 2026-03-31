# 🛒 PANIER - Setup Complet & Test

## ✅ Vues créées aujourd'hui

```
✅ layout.php - Template réutilisable (header/footer/nav)
✅ home.php - Page d'accueil publique
✅ auth/login.php - Formulaire de connexion
✅ auth/register.php - Formulaire d'inscription
✅ dashboard.php - Tableau de bord utilisateur
✅ shopping_lists/public_list.php - Listes publiques
✅ shopping_lists/detail.php ⭐ - COÛT TOTAL (CORE)
✅ shopping_lists/my_lists.php - Mes listes
✅ products/my_products.php - Mes produits
✅ style.css - CSS complet + responsive
✅ js/main.js - JavaScript interactions
```

---

## 🚀 Comment tester?

### 1️⃣ Importer la BD

```bash
# Dans Laragon, ouvrir MySQL et exécuter:
mysql -u root
> CREATE DATABASE panier_db;
> USE panier_db;
> source G:\SIO2B\laragon\www\panier\script.sql;
```

**OU via Laragon GUI:**
- Adminer → Créer BD "panier_db"
- Importer script.sql

### 2️⃣ Vérifier les permissions .env

```bash
cd G:\SIO2B\laragon\www\panier
# Vérifier que .env existe avec:
cat .env
```

Doit contenir:
```env
DB_DATABASE=panier_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3️⃣ Démarrer le serveur PHP

```bash
cd G:\SIO2B\laragon\www\panier

# Terminal PowerShell:
$env:Path += ";G:\SIO2B\laragon\bin\php\php-8.1.10-Win32-vs16-x64"
php -S localhost:8000 -t public/
```

### 4️⃣ Ouvrir dans le navigateur

```
http://localhost:8000/index.php?page=home
```

---

## 🧪 Parcours de test

### Test 1: Accueil (Pas connecté)
- **URL**: `http://localhost:8000/index.php?page=home`
- ✅ Voir la page d'accueil avec features
- ✅ Boutons "Se connecter" et "S'inscrire"

### Test 2: Listes publiques (Pas connecté)
- **URL**: `http://localhost:8000/index.php?page=public-lists`
- ✅ Voir les listes publiques (données de test)
- ✅ Bouton "Voir la liste" pour chaque liste

### Test 3: Inscription
- **URL**: `http://localhost:8000/index.php?page=register`
- **Données**: 
  - Nom: Votre Nom
  - Email: test@example.com
  - Password: password123 (×2)
  - ✅ Accepter les conditions
- **Résultat**: Créé et auto-connecté → dashboard

### Test 4: Connexion (logout + login)
- **URL**: `http://localhost:8000/index.php?page=logout`
- Puis revenir et se connecter avec:
  - Email: `alice@example.com`
  - Password: (généré dans script.sql - voir le hash)

### Test 5: Dashboard (Connecté)
- **URL**: `http://localhost:8000/index.php?page=dashboard`
- ✅ Voir 6 cartes de navigation
- ✅ Admin badge (pour alice ou charlie)
- ✅ Stats section

### Test 6: Mes listes
- **URL**: `http://localhost:8000/index.php?page=my-lists`
- ✅ Affiche listes de l'utilisateur
- ✅ Boutons voir/éditer/supprimer
- ✅ Statut (En cours / Complétée)

### Test 7: Mes produits
- **URL**: `http://localhost:8000/index.php?page=my-products`
- ✅ Affiche produits de l'utilisateur
- ✅ Badges bio/nutri-score
- ✅ Prix affiché en €

### Test 8: 🌟 Détail liste + COÛT TOTAL
- **URL**: `http://localhost:8000/index.php?page=public-detail&id=1`
- ✅ **Affiche tous les articles**
- ✅ **Coût total en EVIDence (green box)**
- ✅ Formule: price × quantity = subtotal
- ✅ SUM de tous les subtotals = TOTAL ⭐
- ✅ Boutons action (checkbox, supprimer)

---

## 🔧 Données de test disponibles

**Utilisateurs** (script.sql):
```
alice@example.com   - Role: user
bob@example.com     - Role: user
charlie@example.com - Role: admin
```

**Produits** (15 articles):
- Lait: €1.50
- Pain: €0.95
- Pommes: €2.50
- ... (13 autres)

**Listes** (4 listes de test avec items):
- Liste 1: 5 items
- Liste 2: 4 items
- Liste 3: 6 items
- Liste 4: 5 items

---

## ⚠️ Problèmes possibles & Solutions

### ❌ "PDOException: SQLSTATE[HY000]"
**Cause**: BD non importée
**Solution**:
```bash
mysql -u root panier_db < script.sql
```

### ❌ "BASE_URL not defined"
**Cause**: Constante manquante
**Solution**: Vérifier public/index.php ligne ~10
```php
define('BASE_URL', '');
```

### ❌ Vues blanches
**Cause**: Erreur PHP
**Solution**: Vérifier console PHP et errors en terminal

### ❌ 404 sur page
**Cause**: Route mal nommée
**Solution**: Vérifier case-sensitivity dans public/index.php

---

## 📋 Reste à faire

### Views (simples, pas impératif pour test):
- [ ] form.php (Créer/Éditer liste) - Basique
- [ ] form_edit.php - Éditer liste
- [ ] form.php (Produit) - Créer/Éditer produit
- [ ] form_edit.php (Produit)
- [ ] form_add_item.php - Ajouter article à liste

### Functionality:
- [ ] AJAX toggle checkbox (itemChecked)
- [ ] Admin panel (optionnel)

### Tests:
- [ ] Unit tests (Models)
- [ ] Feature tests (Controllers)

---

## ✨ Ce qui fonctionne maintenant

✅ Routeur complet
✅ Navigation avec authentification
✅ Layout réutilisable
✅ CSS propre et lisible
✅ Affichage des listes
✅ **Coût total calculé et affiché** ⭐
✅ Badges (bio, nutri-score)
✅ Formatage date/currency
✅ Messages alertes (succès/erreur)
✅ Responsive (mobile-ready)

---

## 🎯 Prochaine étape

Après les tests, créer les formulaires manquants:
1. form.php (liste)
2. form_add_item.php (ajouter article)
3. form.php (produit)

**Puis:** `git add . && git commit && git push`

---

## 💡 Tips pour debug

### Voir les erreurs PHP
```bash
# Dans le terminal où court le serveur:
php -S localhost:8000 -t public/

# Les erreurs s'affichent directement
```

### Voir les logs BD
```bash
# Dans les contrôleurs, ajouter:
var_dump($query);
die();
```

### Vérifier la session
```php
<?php
session_start();
var_dump($_SESSION);
?>
```

---

**Tu es prêt à tester! 🚀**

Dis-moi si tu rencontres des erreurs!
