# 🔐 Guide de Connexion - Panier

## Comptes Disponibles

### 1️⃣ Administrateur (Admin)

**Rôle**: Gestion complète du système

```
Email:    admin@panier.local
Mot de passe: admin123
```

**Accès**:
- Tableau de bord admin (👨‍💼 Admin dans la barre de navigation)
- Gérer tous les utilisateurs
- Gérer tous les paniers
- Gérer tous les produits
- Voir les statistiques système

**⚠️ IMPORTANT**: 
- Ce mot de passe est temporaire pour les tests
- À changer OBLIGATOIREMENT en production
- Seul l'admin peut accéder au tableau de bord administrateur

---

### 2️⃣ Utilisateur Test (User)

**Rôle**: Utilisateur standard

```
Email:    test@gmail.com
Mot de passe: Test1234@
```

**Accès**:
- Créer des paniers personnels
- Créer des produits personnels
- Éditer ses paniers/produits
- Consulter les paniers publics

---

## 🆕 Créer un Nouveau Compte

1. Cliquer sur **"S'inscrire"**
2. Remplir le formulaire:
   - **Nom**: Votre nom d'utilisateur
   - **Email**: Adresse email unique
   - **Mot de passe**: Doit respecter les critères:
     - Minimum 8 caractères
     - Au moins 1 MAJUSCULE
     - Au moins 1 minuscule
     - Au moins 1 chiffre (0-9)
     - Au moins 1 caractère spécial (@$!%*?&)
   - **Confirmation**: Retaper le même mot de passe

3. Exemple de mot de passe valide: `MyPassword123!` ✅

---

## 🔓 Se Connecter

1. Cliquer sur **"Connexion"**
2. Entrer votre email
3. Entrer votre mot de passe
4. Cliquer sur **"Se connecter"**

---

## 🚀 Accès à l'Application

- **URL**: `http://localhost:8000` (ou `http://localhost/panier/public`)
- **Accueil**: Visible à tout le monde (statistiques, paniers publics)
- **Connexion requise**: Pour créer/modifier ses propres paniers et produits
- **Admin requis**: Pour accéder au tableau de bord administrateur

---

## 🔓 Fonctionnalités par Rôle

### Visiteur (Non connecté)
- ✅ Voir la page d'accueil
- ✅ Consulter les paniers publics
- ✅ S'inscrire
- ✅ Se connecter
- ❌ Créer un panier
- ❌ Voir le profil

### Utilisateur (Connecté)
- ✅ Voir la page d'accueil
- ✅ Consulter les paniers publics
- ✅ **Créer/Éditer/Supprimer ses paniers**
- ✅ **Créer/Éditer/Supprimer ses produits**
- ✅ Voir son profil
- ✅ Se déconnecter
- ❌ Supprimer d'autres comptes
- ❌ Tableau de bord admin

### Administrateur (Connecté + Admin)
- ✅ **Tous les droits utilisateur**
- ✅ **Accéder au tableau de bord admin (👨‍💼 Admin)**
- ✅ **Voir tous les utilisateurs du système**
- ✅ **Voir tous les paniers du système**
- ✅ **Voir tous les produits du système**
- ✅ **Supprimer n'importe quel utilisateur** (sauf lui-même)
- ✅ **Supprimer n'importe quel panier**
- ✅ **Supprimer n'importe quel produit**
- ✅ Voir les statistiques du système

---

## 🛠️ Support

### Problèmes de connexion ?

1. **Email/Mot de passe oubliés**
   - Créer un nouveau compte avec S'inscrire
   - (Note: La fonctionnalité "Mot de passe oublié" n'est pas implémentée)

2. **Mot de passe ne respecte pas les critères**
   - Vérifier les 5 critères: 8+ caractères, 1 MAJ, 1 min, 1 chiffre, 1 spécial
   - Le message d'erreur indiquera les critères manquants

3. **Email déjà utilisé**
   - L'email doit être unique dans le système
   - Utiliser un autre email

4. **Compte non activé**
   - Les comptes sont activés immédiatement après inscription

---

## 📝 Notes

- La session reste active jusqu'à la déconnexion
- Les paniers créés sont privés par défaut
- Un panier est "publique" s'il a le statut public (non implémenté dans cette version)
- Les produits sont visibles uniquement par leur créateur et les admins
- Les calculs de total sont automatiques

---

Bon test ! 🚀

