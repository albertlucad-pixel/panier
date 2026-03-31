# 🎭 Schéma des Use Cases - PANIER

## Vue globale des Use Cases

```
                        ┌─────────────────────────┐
                        │      SYSTÈME PANIER     │
                        └─────────────────────────┘
                                    │
                    ┌───────────────┼───────────────┐
                    │               │               │
                    ▼               ▼               ▼
            ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
            │  Visiteur    │  │ Utilisateur  │  │ Administrateur
            │  (Anonyme)   │  │  (Connecté)  │  │   (Admin)    │
            └──────────────┘  └──────────────┘  └──────────────┘
```

---

## 1️⃣ VISITEUR (Non connecté)

```
                        ┌──────────────────────────┐
                        │        VISITEUR          │
                        │      (Non connecté)      │
                        └──────────────────────────┘
                                    │
        ┌───────────────────────────┼───────────────────────────┐
        │                           │                           │
        ▼                           ▼                           ▼
   ┌─────────────┐         ┌──────────────┐         ┌──────────────┐
   │   Accueil   │         │ Consulter    │         │  S'inscrire  │
   │             │         │ Paniers pub. │         │              │
   │  UC-V1      │         │              │         │  UC-V2       │
   │             │         │   UC-V2      │         │              │
   └─────────────┘         └──────────────┘         └──────────────┘
        │                           │                           │
        └─ Voir la page d'accueil   └─ Consulter en lecture    └─ Créer compte
        └─ Voir les stats           └─ Voir créateur          └─ Email unique
        └─ Navigation               └─ Voir articles          └─ Mdp fort
                                    └─ Voir total coût        └─ Redirection
                                                              login
                                    
        ▼
   ┌─────────────┐
   │  Se connecter│
   │             │
   │  UC-V3      │
   │             │
   └─────────────┘
        │
        └─ Email valide
        └─ Mdp correct
        └─ Rôle chargé
        └─ Redirection dashboard

```

### **UC-V1: Accéder à l'accueil**
- **Acteur**: Visiteur
- **Précondition**: Aucune
- **Flux principal**:
  1. Visiteur clique sur "Accueil"
  2. Système affiche page d'accueil
  3. Affichage stats (utilisateurs, paniers, produits)
- **Postcondition**: Page d'accueil affichée
- **Scénarios alternatifs**: Aucun

### **UC-V2: Consulter les paniers publics**
- **Acteur**: Visiteur
- **Précondition**: Aucune
- **Flux principal**:
  1. Visiteur clique sur "Paniers publics"
  2. Système affiche tous les paniers publics (non complétés)
  3. Pour chaque panier: nom, créateur, date, nombre articles
  4. Visiteur clique sur un panier
  5. Système affiche détail: articles, quantités, prix unitaires, total
- **Postcondition**: Détail du panier affiché en lecture seule
- **Scénarios alternatifs**:
  - Aucun panier public → message vide

### **UC-V3: S'inscrire**
- **Acteur**: Visiteur
- **Précondition**: Visiteur non connecté
- **Flux principal**:
  1. Visiteur clique sur "S'inscrire"
  2. Système affiche formulaire (Nom, Email, Mdp, Confirmation)
  3. Visiteur remplit formulaire
  4. Système valide:
     - Email non utilisé ✓
     - Mdp respecte critères (8+ car, 1 maj, 1 min, 1 chiffre, 1 spécial) ✓
     - Mots de passe correspondent ✓
  5. Création compte
  6. Redirection page connexion
- **Postcondition**: Compte créé, utilisateur redirigé login
- **Scénarios alternatifs**:
  - Email déjà utilisé → Erreur
  - Mdp faible → Erreur détaillée
  - Mots de passe non-identiques → Erreur

### **UC-V4: Se connecter**
- **Acteur**: Visiteur
- **Précondition**: Compte existant
- **Flux principal**:
  1. Visiteur clique sur "Connexion"
  2. Système affiche formulaire (Email, Mdp)
  3. Visiteur remplit formulaire
  4. Système valide identifiants
  5. Session créée avec rôle (user/admin)
  6. Redirection tableau de bord
- **Postcondition**: Utilisateur connecté, session active
- **Scénarios alternatifs**:
  - Identifiants incorrects → Erreur

---

## 2️⃣ UTILISATEUR CONNECTÉ

```
                    ┌───────────────────────────────────┐
                    │  UTILISATEUR CONNECTÉ             │
                    │  (Rôle: user)                     │
                    └───────────────────────────────────┘
                                    │
    ┌───────────────────────────────┼───────────────────────────────┐
    │                               │                               │
    ▼                               ▼                               ▼
┌──────────────┐         ┌──────────────────┐         ┌──────────────┐
│  Tableau de  │         │   Gestion des    │         │  Gestion des │
│   bord       │         │   paniers        │         │   produits   │
│              │         │                  │         │              │
│  UC-U1       │         │  UC-U2..U7       │         │  UC-U8..U11  │
│              │         │                  │         │              │
└──────────────┘         └──────────────────┘         └──────────────┘
     │                            │                           │
     │                            ├─ Voir paniers            ├─ Voir produits
     │                            ├─ Créer panier            ├─ Créer produit
     │                            ├─ Éditer panier           ├─ Éditer produit
     │                            ├─ Ajouter produits        ├─ Supprimer produit
     │                            ├─ Modifier quantités      └─ Consulter prix
     │                            ├─ Voir détails
     │                            └─ Supprimer panier
     │
     └─ Voir mon profil
     └─ Déconnexion
     └─ Accès au tableau de bord

                    ┌──────────────────────────┐
                    │    Profil Utilisateur    │
                    │       (UC-U12)           │
                    └──────────────────────────┘
                            │
                            ├─ Voir infos (nom, email)
                            ├─ Voir date inscription
                            └─ Déconnexion
```

### **UC-U1: Accéder au tableau de bord**
- **Acteur**: Utilisateur
- **Précondition**: Utilisateur connecté
- **Flux principal**:
  1. Utilisateur clique "Mes paniers"
  2. Système affiche tableau de bord personnel
  3. Affichage liste des paniers personnels (Nom, Description, Date création)
- **Postcondition**: Tableau de bord affiché
- **Scénarios alternatifs**:
  - Aucun panier → message vide

### **UC-U2: Consulter ses paniers**
- **Acteur**: Utilisateur
- **Précondition**: Utilisateur connecté
- **Flux principal**:
  1. Utilisateur clique sur un panier
  2. Système affiche détail: nom, description, articles avec quantités, total coût
  3. Affichage option "Éditer" et "Supprimer"
- **Postcondition**: Détail panier affiché
- **Scénarios alternatifs**: Aucun

### **UC-U3: Créer un panier**
- **Acteur**: Utilisateur
- **Précondition**: Utilisateur connecté
- **Flux principal**:
  1. Utilisateur clique "Créer un panier"
  2. Système affiche formulaire (Nom*, Description)
  3. Utilisateur remplit formulaire
  4. Système valide Nom non vide
  5. Panier créé (vide initialement)
  6. Redirection vers édition panier (ajouter produits)
- **Postcondition**: Panier créé, utilisateur en mode édition
- **Scénarios alternatifs**:
  - Nom vide → Erreur

### **UC-U4: Éditer un panier**
- **Acteur**: Utilisateur
- **Précondition**: Panier existe et appartient à l'utilisateur
- **Flux principal**:
  1. Utilisateur clique "Éditer" sur un panier
  2. Système affiche formulaire (Nom, Description)
  3. Affichage TOUS les produits de l'utilisateur en tableau:
     - Colonne 1: Case à cocher
     - Colonne 2: Nom produit
     - Colonne 3: Prix
     - Colonne 4: Quantité
  4. Produits actuels du panier sont cochés et quantité affichée
  5. Produits non dans le panier sont décochés et quantité désactivée
  6. Utilisateur peut:
     - Cocher/Décocher produits
     - Modifier quantités des produits cochés
  7. Soumission du formulaire:
     - Produits cochés = ajoutés ou quantité mise à jour
     - Produits décochés = supprimés
- **Postcondition**: Panier mis à jour avec nouveaux produits/quantités
- **Scénarios alternatifs**:
  - Aucun produit disponible → message

### **UC-U5: Ajouter un produit à un panier**
- **Acteur**: Utilisateur
- **Précondition**: Panier ouvert en édition, produit de l'utilisateur existe
- **Flux principal**:
  1. Cocher un produit dans le tableau
  2. Système active champ quantité
  3. Produit ajouté au panier avec quantité = 1
- **Postcondition**: Produit ajouté au panier
- **Scénarios alternatifs**: Aucun

### **UC-U6: Modifier quantité d'un produit**
- **Acteur**: Utilisateur
- **Précondition**: Produit déjà dans panier et panier en édition
- **Flux principal**:
  1. Utilisateur modifie valeur dans champ quantité
  2. Formulaire soumis
  3. Quantité mise à jour dans liste_item
- **Postcondition**: Quantité mise à jour
- **Scénarios alternatifs**:
  - Quantité = 0 → article supprimé

### **UC-U7: Supprimer un panier**
- **Acteur**: Utilisateur
- **Précondition**: Panier existe et appartient à l'utilisateur
- **Flux principal**:
  1. Utilisateur clique "Supprimer" sur un panier
  2. Système affiche confirmation
  3. Utilisateur confirme
  4. Système supprime panier et tous les list_item associés
  5. Redirection "Mes paniers"
- **Postcondition**: Panier supprimé
- **Scénarios alternatifs**:
  - Utilisateur annule → pas de suppression

### **UC-U8: Consulter ses produits**
- **Acteur**: Utilisateur
- **Précondition**: Utilisateur connecté
- **Flux principal**:
  1. Utilisateur clique "Mes produits"
  2. Système affiche tableau avec produits de l'utilisateur (Nom, Prix, Catégorie)
  3. Actions: "Éditer", "Supprimer"
- **Postcondition**: Liste produits affichée
- **Scénarios alternatifs**:
  - Aucun produit → message vide

### **UC-U9: Créer un produit**
- **Acteur**: Utilisateur
- **Précondition**: Utilisateur connecté
- **Flux principal**:
  1. Utilisateur clique "Créer un produit"
  2. Système affiche formulaire (Nom*, Prix*, Description, Catégorie, Bio, NutriScore)
  3. Utilisateur remplit formulaire
  4. Système valide Nom et Prix non vides
  5. Produit créé
  6. Redirection "Mes produits"
- **Postcondition**: Produit créé et visible
- **Scénarios alternatifs**:
  - Données manquantes → Erreur

### **UC-U10: Éditer un produit**
- **Acteur**: Utilisateur
- **Précondition**: Produit existe et appartient à l'utilisateur
- **Flux principal**:
  1. Utilisateur clique "Éditer" sur produit
  2. Système affiche formulaire pré-rempli
  3. Utilisateur modifie données
  4. Soumission
  5. Produit mis à jour
  6. Redirection "Mes produits"
- **Postcondition**: Produit modifié
- **Scénarios alternatifs**: Aucun

### **UC-U11: Supprimer un produit**
- **Acteur**: Utilisateur
- **Précondition**: Produit existe et appartient à l'utilisateur
- **Flux principal**:
  1. Utilisateur clique "Supprimer" sur produit
  2. Système affiche confirmation
  3. Utilisateur confirme
  4. Système supprime le produit (CASCADE supprime aussi les list_item)
  5. Redirection "Mes produits"
- **Postcondition**: Produit supprimé
- **Scénarios alternatifs**:
  - Utilisateur annule → pas de suppression

### **UC-U12: Consulter profil**
- **Acteur**: Utilisateur
- **Précondition**: Utilisateur connecté
- **Flux principal**:
  1. Utilisateur clique "Profil"
  2. Système affiche informations: Nom, Email, Date inscription, Rôle
  3. Bouton "Déconnexion"
- **Postcondition**: Profil affiché
- **Scénarios alternatifs**: Aucun

### **UC-U13: Se déconnecter**
- **Acteur**: Utilisateur
- **Précondition**: Utilisateur connecté
- **Flux principal**:
  1. Utilisateur clique "Déconnexion"
  2. Session détruite
  3. Redirection "Accueil"
- **Postcondition**: Session fermée, utilisateur déconnecté
- **Scénarios alternatifs**: Aucun

---

## 3️⃣ ADMINISTRATEUR

```
                    ┌───────────────────────────────────┐
                    │      ADMINISTRATEUR               │
                    │      (Rôle: admin)                │
                    └───────────────────────────────────┘
                                    │
                    ┌───────────────┴───────────────┐
                    │                               │
                    ▼                               ▼
            ┌──────────────────┐         ┌──────────────────┐
            │ Tous les droits  │         │  Tableau de      │
            │  des utilisateurs│         │  bord Admin      │
            │                  │         │  (Gestion)       │
            │  UC-A1..U13      │         │                  │
            │  (Héritage)      │         │  UC-A1..A5       │
            │                  │         │                  │
            └──────────────────┘         └──────────────────┘
                    │                           │
                    │                           ├─ Voir tous les users
                    │                           ├─ Voir tous les paniers
                    │                           ├─ Voir tous les produits
                    │                           ├─ Supprimer user
                    │                           ├─ Supprimer panier
                    │                           └─ Supprimer produit

            ┌──────────────────────────────────────┐
            │  Actions étendues (héritées + Admin) │
            │                                      │
            │  - Tous les UC utilisateur           │
            │  - Créer/Éditer/Supprimer           │
            │    ses propres ressources            │
            │  - Accéder tableau de bord admin    │
            │  - Gérer ressources autres users    │
            └──────────────────────────────────────┘
```

### **UC-A1: Accéder au tableau de bord admin**
- **Acteur**: Admin
- **Précondition**: Utilisateur connecté avec rôle 'admin'
- **Flux principal**:
  1. Admin clique sur "👨‍💼 Admin" dans navigation
  2. Système affiche tableau de bord avec 3 onglets:
     - Onglet 1: Utilisateurs (👥)
     - Onglet 2: Paniers (📋)
     - Onglet 3: Produits (🛒)
  3. Affichage statistiques: nombre total de chaque
- **Postcondition**: Tableau de bord admin affiché
- **Scénarios alternatifs**:
  - Utilisateur non-admin → Redirection dashboard

### **UC-A2: Gérer les utilisateurs**
- **Acteur**: Admin
- **Précondition**: Admin dans tableau de bord
- **Flux principal**:
  1. Admin clique onglet "Utilisateurs"
  2. Système affiche tableau: Nom | Email | Rôle | Date création | Actions
  3. Rôle affiché avec badge (bleu=admin, vert=user)
  4. Admin peut cliquer "Supprimer" (🗑️) sur n'importe quel user
  5. Système demande confirmation
  6. Utilisateur et toutes ses ressources supprimées (CASCADE)
- **Postcondition**: Utilisateur supprimé
- **Scénarios alternatifs**:
  - Admin ne peut pas se supprimer lui-même
  - Annulation → pas de suppression

### **UC-A3: Gérer les paniers**
- **Acteur**: Admin
- **Précondition**: Admin dans tableau de bord
- **Flux principal**:
  1. Admin clique onglet "Paniers"
  2. Système affiche tableau: Nom | Créateur | Articles | Date | Actions
  3. Admin peut cliquer "Voir" (👁️) pour consulter
  4. Admin peut cliquer "Supprimer" (🗑️)
  5. Système demande confirmation
  6. Panier et ses articles supprimés
- **Postcondition**: Panier supprimé
- **Scénarios alternatifs**:
  - Annulation → pas de suppression

### **UC-A4: Gérer les produits**
- **Acteur**: Admin
- **Précondition**: Admin dans tableau de bord
- **Flux principal**:
  1. Admin clique onglet "Produits"
  2. Système affiche tableau: Nom | Créateur | Prix | Catégorie | Date | Actions
  3. Admin peut cliquer "Supprimer" (🗑️)
  4. Système demande confirmation
  5. Produit supprimé (CASCADE supprime associations list_item)
- **Postcondition**: Produit supprimé
- **Scénarios alternatifs**:
  - Annulation → pas de suppression

### **UC-A5: Voir statistiques système**
- **Acteur**: Admin
- **Précondition**: Admin dans tableau de bord
- **Flux principal**:
  1. Système affiche 3 cartes de statistiques:
     - Total utilisateurs
     - Total paniers
     - Total produits
  2. Nombres mis à jour en temps réel
- **Postcondition**: Stats affichées
- **Scénarios alternatifs**: Aucun

---

## 📊 Matrice de permissions

| Use Case | Visiteur | User | Admin |
|----------|----------|------|-------|
| UC-V1: Accueil | ✅ | ✅ | ✅ |
| UC-V2: Paniers publics | ✅ | ✅ | ✅ |
| UC-V3: S'inscrire | ✅ | ❌ | ❌ |
| UC-V4: Se connecter | ✅ | ❌ | ❌ |
| UC-U1: Dashboard user | ❌ | ✅ | ❌ |
| UC-U2 à U13: Gestion perso | ❌ | ✅ | ✅ |
| UC-A1: Dashboard admin | ❌ | ❌ | ✅ |
| UC-A2 à A5: Gestion système | ❌ | ❌ | ✅ |
| Supprimer propres ressources | ❌ | ✅ | ✅ |
| Supprimer ressources autres | ❌ | ❌ | ✅ |
| Se supprimer soi-même | ❌ | ❌ | ❌ |

---

## 🔐 Modèle de sécurité

- **Authentification**: Session PHP avec PASSWORD_BCRYPT
- **Autorisation**: Basée sur le rôle (role-based access control)
- **Validation**: Côté serveur (data + permissions)
- **Protection CSRF**: À implémenter si nécessaire
- **Mots de passe forts**: 8+ caractères, 1 maj, 1 min, 1 chiffre, 1 spécial

