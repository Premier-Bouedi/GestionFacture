# 📄 Cahier des Charges - Application FAC+

## 1. Présentation du Projet

**FAC+** est une application web de gestion commerciale simplifiée, conçue pour permettre aux entreprises de gérer leurs clients, leurs factures et leurs stocks de produits de manière intuitive et sécurisée.

## 2. Objectifs de l'Application

- **Automatisation** : Générer des factures professionnelles au format PDF.
- **Gestion des Stocks** : Suivre en temps réel la disponibilité des produits et alerter en cas de stock bas.
- **Centralisation** : Regrouper les informations clients et l'historique des ventes.
- **Aide à la décision** : Fournir un tableau de bord "Live" avec les indicateurs clés (CA, Ruptures, etc.).
- **Assistance intelligente** : Intégrer un chatbot pour interroger les données du système.

## 3. Analyse Fonctionnelle

### 3.1. Gestion des Utilisateurs (Authentification)

- Système de connexion sécurisé.
- Distinction des rôles (Administrateur vs Utilisateur standard).
- Espace Administration réservé aux profils autorisés.

### 3.2. Gestion des Clients (CRM)

- Création, modification et suppression de clients.
- Ajout rapide de clients directement lors de la création d'une facture.
- Archivage sécurisé (Soft Delete).

### 3.3. Gestion des Produits et Stocks

- Catalogue de produits avec désignation, prix unitaire et niveau de stock.
- Alertes visuelles (Badge rouge/orange) selon le niveau de stock restant.
- Décrémentation automatique du stock lors de la validation d'une facture.

### 3.4. Facturation

- Création de factures multi-produits.
- Calcul automatique des totaux (HT, TVA 20%, TTC).
- Génération de numéros de facture uniques (Format : FA-AAAA-XXX).
- Téléchargement des factures en version PDF professionnelle.

### 3.5. Intelligence Artificielle (Assistant Admis)

- Chatbot intégré capable de répondre à des questions sur les données :
  - "Quel est mon chiffre d'affaires total ?"
  - "Quels produits sont en rupture de stock ?"
  - "Combien ai-je de clients ?"

## 4. Spécifications Techniques

### 4.1. Architecture

- **Framework** : Laravel 11 (PHP 8.2+).
- **Base de données** : SQLite (pour une portabilité et une rapidité optimales).
- **Interface** : HTML5, CSS3, Bootstrap 5.3.

### 4.2. Sécurité

- Middleware de protection des routes.
- Protection contre les failles CSRF et injections SQL.
- Gestion des mots de passe hachés.

## 5. Interface Utilisateur (UI/UX)

- **Design Premium** : Thème moderne, cartes épurées.
- **Tableau de Bord Live** : Actualisation automatique toutes les 30 secondes.
- **Navigation Optimisée** : Menu réorganisé pour un accès rapide.
