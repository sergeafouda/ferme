
# 🌾 Ferme de l'Amitié - Tunnel de Précommande & Paiement

Ce projet intègre un tunnel de précommande de produits agricoles avec gestion des livraisons multiples, calcul d'acomptes de sécurisation et paiement en ligne via **PayDunya**.

## ⚙️ Modifications de la Base de Données & Déploiement

Pour que l'application fonctionne correctement, plusieurs migrations successives ont été créées pour enrichir la structure de la table `preorders`. **Il est impératif d'exécuter les migrations** pour appliquer ces changements sur votre environnement local ou de production :

```bash
php artisan migrate
```

### Détail des Migrations de la table `preorders` :
1. **`2026_05_09_085249_create_preorders_table`** : Création initiale de la table (champs clients, JSON des livraisons, total, acompte, statut).
2. **`2026_05_16_214343_add_lapin_and_poulet_to_preorders_table`** : Ajout des colonnes `lapin_kg` et `poulet_goliath_kg` pour gérer les nouveaux produits agricoles.
3. **`2026_05_16_221114_add_confirmation_sent_to_preorders_table`** : Ajout du booléen `confirmation_sent` pour sécuriser les envois d'emails et éviter les doublons.
4. **`2026_05_16_223500_add_receipt_url_to_preorders_table`** : Ajout de la colonne `receipt_url` pour stocker et mettre à disposition le reçu PDF officiel généré par PayDunya.

## 📅 Chronologie des Travaux & Fonctionnalités Implémentées

### 1️⃣ Phase 1 : Tunnel de Précommande & Formulaire Dynamique
- **Base de données (`preorders`)** : Enregistrement des informations clients (prénom, nom, email, téléphone, note de projet) et des livraisons planifiées au format JSON.
- **Règles de validation strictes** : 
  - Date de livraison minimum de **3 mois** pour les tomates (fruit et purée).
  - Date de livraison minimum de **1 semaine** pour les nouveaux produits ajoutés (**Lapin** et **Poulet Goliath**).
- **Acompte de sécurisation** : Possibilité pour le client de payer un acompte facultatif (minimum 100 FCFA/kg) pour obtenir un statut de **Priorité Absolue**. Mentions claires de la politique de remboursement sous 48h en cas d'indisponibilité.
- **Persistance immédiate** : Sauvegarde des données en base dès le clic sur "Enregistrer ma réservation", avant la redirection vers le paiement.

### 2️⃣ Phase 2 : Système de Notifications par Email
- **Mails automatiques (`Mailables`)** :
  - `PreorderUserConfirmation` : Récapitulatif complet envoyé au client.
  - `PreorderAdminNotification` : Alerte envoyée à l'administrateur (`fermedelamitie@contact.com`).
- **Sécurité anti-doublon** : Utilisation de la colonne `confirmation_sent` (booléen) pour garantir qu'aucune notification n'est envoyée en double.

### 3️⃣ Phase 3 : Intégration de la Passerelle PayDunya (Remplacement de KKiaPay)
- **SDK Officiel** : Installation et configuration du package `paydunya/paydunya`.
- **Variables d'environnement (`.env`)** : Configuration des clés d'API (`PAYDUNYA_MASTER_KEY`, `PAYDUNYA_PUBLIC_KEY`, `PAYDUNYA_PRIVATE_KEY`, `PAYDUNYA_TOKEN`, `PAYDUNYA_MODE`).
- **Contrôleur de Paiement (`PreorderController`)** : Création de factures de paiement (PAR) avec redirection dynamique vers PayDunya et gestion des retours (`success`, `thankYou`).

### 4️⃣ Phase 4 : Webhook IPN (Instant Payment Notification) & Sécurité
- **Endpoint IPN (`/preorder/ipn`)** : Traitement asynchrone des notifications de paiement serveur à serveur.
- **Sécurité & Hash** : Vérification stricte de l'authenticité des requêtes via la comparaison du hash `SHA-512` de la `MASTER_KEY`.
- **Exclusion CSRF** : Ajout de la route IPN dans les exceptions CSRF (`bootstrap/app.php`) pour autoriser les requêtes POST entrantes de PayDunya.

### 5️⃣ Phase 5 : Gestion Automatique des Factures PDF
- **Migration & Modèle** : Ajout de la colonne `receipt_url` dans la table `preorders`.
- **Récupération du reçu** : Stockage automatique de l'URL du reçu PDF électronique généré par PayDunya lors du retour client (`success`) et via le Webhook (`ipn`).
- **Accessibilité Client** : Ajout d'un bouton de téléchargement direct de la facture PDF sur la page de remerciement (`thank_you.blade.php`) et dans l'email de confirmation.

### 6️⃣ Phase 6 : Améliorations UI/UX & Responsive Design
- **Bouton WhatsApp** : Intégration d'un bouton de contact rapide en haut de page configuré via `WHATSAPP_PHONE`.
- **Harmonisation de la section Héros** : Refonte de la grille d'introduction en conteneur unique centré et élargi (`850px`), aligné esthétiquement avec le formulaire de réservation.
- **Page de Remerciement Premium** : Ajustement du titre principal sur une seule ligne fluide et mise en valeur des boutons d'action (retour accueil et téléchargement de facture) sous forme de capsules premium colorées et contrastées.
