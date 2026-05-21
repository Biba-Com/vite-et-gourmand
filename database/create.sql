/********************************************************************************
 * Vite & Gourmand — Database Structure
 * Version: 1.1.0
 * Engine: MySQL 8.x
 * Charset: utf8mb4
 * Collation: utf8mb4_unicode_ci
 *
 * Commenting conventions:
 *  - Block headers describe module purpose and scope
 *  - Inline comments explain constraints, indexes, and design rationale
 *  - All comments are in English and follow a consistent style
 *
 * Usage notes:
 *  - Run in staging first; ensure backups and binlog snapshots are available
 *  - Requires MySQL 8.x features (CHECK constraints, JSON, etc.)
 *  - Order matters: tables are created in dependency order
 *  - Cross-module FKs are added via ALTER TABLE at the end
 ********************************************************************************/
-- ============================================================
-- Database creation and session settings
-- ============================================================
CREATE DATABASE IF NOT EXISTS vite_et_gourmand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vite_et_gourmand;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
-- ============================================================
-- Drop existing objects (safe reset for development)
-- ============================================================
DROP TABLE IF EXISTS log_audit;
DROP TABLE IF EXISTS notification;
DROP TABLE IF EXISTS log_email;
DROP TABLE IF EXISTS message_contact;
DROP TABLE IF EXISTS historique_statut;
DROP TABLE IF EXISTS abonnement_alerte;
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS ligne_commande;
DROP TABLE IF EXISTS commande;
DROP TABLE IF EXISTS cache_distance;
DROP TABLE IF EXISTS menu_certification;
DROP TABLE IF EXISTS menu_badge_eco;
DROP TABLE IF EXISTS menu_regime;
DROP TABLE IF EXISTS composition_menu;
DROP TABLE IF EXISTS plat_allergene;
DROP TABLE IF EXISTS offre_anti_gaspi;
DROP TABLE IF EXISTS service;
DROP TABLE IF EXISTS plat;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS badge_eco;
DROP TABLE IF EXISTS certification;
DROP TABLE IF EXISTS partenaire;
DROP TABLE IF EXISTS regime;
DROP TABLE IF EXISTS theme;
DROP TABLE IF EXISTS allergene;
DROP TABLE IF EXISTS contenu_rse;
DROP TABLE IF EXISTS horaires_ouverture;
DROP TABLE IF EXISTS membre_equipe;
DROP TABLE IF EXISTS token_mot_de_passe;
DROP TABLE IF EXISTS session;
DROP TABLE IF EXISTS utilisateur;
-- ============================================================
-- MODULE 1 — Users and Authentication
-- ============================================================
-- Table: utilisateur
CREATE TABLE utilisateur (
  id_utilisateur INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL COMMENT 'bcrypt or argon2id hash',
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  telephone VARCHAR(20) NULL,
  adresse VARCHAR(255) NULL,
  code_postal VARCHAR(10) NULL,
  ville VARCHAR(100) NULL,
  role ENUM('visitor', 'client', 'employee', 'admin') NOT NULL DEFAULT 'client',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  email_verified_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (id_utilisateur),
  UNIQUE KEY uq_utilisateur_email (email),
  INDEX idx_utilisateur_role (role),
  INDEX idx_utilisateur_is_active (is_active),
  INDEX idx_utilisateur_ville (ville),
  INDEX idx_utilisateur_deleted_at (deleted_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Application users with role-based access control';
-- Table: session
CREATE TABLE session (
  id_session VARCHAR(128) NOT NULL,
  id_utilisateur INT NOT NULL,
  csrf_token VARCHAR(64) NOT NULL,
  ip_address VARCHAR(45) NOT NULL COMMENT 'Supports IPv6',
  user_agent TEXT NULL COMMENT 'Full UA string (TEXT avoids truncation)',
  last_activity DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_session),
  INDEX idx_session_utilisateur (id_utilisateur),
  INDEX idx_session_last_activity (last_activity),
  CONSTRAINT fk_session_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Active user sessions with CSRF tokens';
-- Table: token_mot_de_passe
CREATE TABLE token_mot_de_passe (
  id_token INT NOT NULL AUTO_INCREMENT,
  id_utilisateur INT NOT NULL,
  token_hash CHAR(64) NOT NULL COMMENT 'SHA-256 hex digest',
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_token),
  INDEX idx_token_utilisateur (id_utilisateur),
  INDEX idx_token_expires (expires_at),
  CONSTRAINT fk_token_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'One-time password reset tokens';
-- ============================================================
-- MODULE 2 — Catalog
-- ============================================================
-- Table: theme
CREATE TABLE theme (
  id_theme INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  description TEXT NULL,
  couleur VARCHAR(7) NULL COMMENT 'Hex color e.g. #063A1F',
  PRIMARY KEY (id_theme),
  UNIQUE KEY uq_theme_nom (nom)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Event themes';
-- Table: allergene
CREATE TABLE allergene (
  id_allergene INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(50) NOT NULL,
  icone VARCHAR(255) NULL,
  PRIMARY KEY (id_allergene),
  UNIQUE KEY uq_allergene_nom (nom)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'EU standard allergens';
-- Table: regime
CREATE TABLE regime (
  id_regime INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  description TEXT NULL,
  PRIMARY KEY (id_regime),
  UNIQUE KEY uq_regime_nom (nom)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Dietary requirements and restrictions';
-- Table: badge_eco
CREATE TABLE badge_eco (
  id_badge INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  description TEXT NULL,
  icone VARCHAR(255) NULL,
  couleur VARCHAR(7) NULL,
  PRIMARY KEY (id_badge),
  UNIQUE KEY uq_badge_nom (nom)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Eco-responsibility badges';
-- Table: certification
CREATE TABLE certification (
  id_certification INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(150) NOT NULL,
  description TEXT NULL,
  logo_url VARCHAR(255) NULL,
  organisme VARCHAR(150) NULL,
  date_validite DATE NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_certification)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Organic and quality certifications';
-- Table: menu
CREATE TABLE menu (
  id_menu INT NOT NULL AUTO_INCREMENT,
  id_theme INT NULL,
  titre VARCHAR(150) NOT NULL,
  slug VARCHAR(100) NOT NULL COMMENT 'URL-friendly identifier (e.g., menu-elegance)',
  description TEXT NULL,
  prix_par_personne DECIMAL(8, 2) NOT NULL,
  nb_personnes_min INT NOT NULL DEFAULT 10,
  nb_personnes_max INT NULL,
  image_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_menu),
  UNIQUE KEY uq_menu_slug (slug),
  INDEX idx_menu_theme (id_theme),
  INDEX idx_menu_is_active (is_active),
  INDEX idx_menu_prix (prix_par_personne),
  CONSTRAINT fk_menu_theme FOREIGN KEY (id_theme) REFERENCES theme (id_theme) ON DELETE
  SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Catering menus with pricing';
-- Table: plat
CREATE TABLE plat (
  id_plat INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(150) NOT NULL,
  description TEXT NULL,
  categorie ENUM('starter', 'main', 'dessert', 'drink', 'other') NOT NULL DEFAULT 'main',
  image_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_plat),
  INDEX idx_plat_categorie (categorie)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Individual dishes';
-- Table: service
CREATE TABLE service (
  id_service INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(150) NOT NULL,
  description TEXT NULL,
  categorie ENUM(
    'staff',
    'equipment',
    'drinks',
    'supplement',
    'other'
  ) NOT NULL,
  prix_base DECIMAL(8, 2) NOT NULL,
  unite_tarification ENUM(
    'per_person',
    'per_hour',
    'per_unit',
    'flat_rate'
  ) NOT NULL DEFAULT 'flat_rate',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_service),
  INDEX idx_service_categorie (categorie)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Additional services';
-- Table: composition_menu
CREATE TABLE composition_menu (
  id_menu INT NOT NULL,
  id_plat INT NOT NULL,
  quantite INT NOT NULL DEFAULT 1,
  ordre_affichage INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id_menu, id_plat),
  CONSTRAINT fk_comp_menu FOREIGN KEY (id_menu) REFERENCES menu (id_menu) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_comp_plat FOREIGN KEY (id_plat) REFERENCES plat (id_plat) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Menu composition';
-- Table: plat_allergene
CREATE TABLE plat_allergene (
  id_plat INT NOT NULL,
  id_allergene INT NOT NULL,
  PRIMARY KEY (id_plat, id_allergene),
  CONSTRAINT fk_pa_plat FOREIGN KEY (id_plat) REFERENCES plat (id_plat) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pa_allergene FOREIGN KEY (id_allergene) REFERENCES allergene (id_allergene) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Allergens per dish';
-- Table: menu_regime
CREATE TABLE menu_regime (
  id_menu INT NOT NULL,
  id_regime INT NOT NULL,
  PRIMARY KEY (id_menu, id_regime),
  CONSTRAINT fk_mr_menu FOREIGN KEY (id_menu) REFERENCES menu (id_menu) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_mr_regime FOREIGN KEY (id_regime) REFERENCES regime (id_regime) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Dietary requirements per menu';
-- Table: menu_badge_eco
CREATE TABLE menu_badge_eco (
  id_menu INT NOT NULL,
  id_badge INT NOT NULL,
  PRIMARY KEY (id_menu, id_badge),
  CONSTRAINT fk_mbe_menu FOREIGN KEY (id_menu) REFERENCES menu (id_menu) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_mbe_badge FOREIGN KEY (id_badge) REFERENCES badge_eco (id_badge) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Eco badges per menu';
-- Table: menu_certification
CREATE TABLE menu_certification (
  id_menu INT NOT NULL,
  id_certification INT NOT NULL,
  PRIMARY KEY (id_menu, id_certification),
  CONSTRAINT fk_mc_menu FOREIGN KEY (id_menu) REFERENCES menu (id_menu) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_mc_certification FOREIGN KEY (id_certification) REFERENCES certification (id_certification) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Certifications per menu';
-- ============================================================
-- MODULE 5 — Eco-responsible Offers (created BEFORE orders to satisfy FK)
-- ============================================================
-- Table: offre_anti_gaspi
-- Note: created before ligne_commande to allow direct FK reference
CREATE TABLE offre_anti_gaspi (
  id_offre INT NOT NULL AUTO_INCREMENT,
  id_menu INT NOT NULL,
  titre VARCHAR(150) NOT NULL,
  description TEXT NULL,
  prix_original DECIMAL(8, 2) NOT NULL,
  prix_remise DECIMAL(8, 2) NOT NULL,
  quantite_disponible INT NOT NULL DEFAULT 1,
  disponible_jusqua DATETIME NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_offre),
  INDEX idx_offre_menu (id_menu),
  INDEX idx_offre_disponible (disponible_jusqua),
  INDEX idx_offre_is_active (is_active),
  CONSTRAINT fk_offre_menu FOREIGN KEY (id_menu) REFERENCES menu (id_menu) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT chk_offre_prix CHECK (prix_remise < prix_original)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Anti-waste promotional offers';
-- Table: abonnement_alerte
CREATE TABLE abonnement_alerte (
  id_abonnement INT NOT NULL AUTO_INCREMENT,
  id_utilisateur INT NOT NULL,
  type_alerte ENUM('anti_waste', 'new_menu', 'special_offer') NOT NULL DEFAULT 'anti_waste',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_abonnement),
  INDEX idx_abonnement_utilisateur (id_utilisateur),
  UNIQUE KEY uq_abonnement (id_utilisateur, type_alerte),
  CONSTRAINT fk_abonnement_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'User alert subscriptions';
-- Table: partenaire
CREATE TABLE partenaire (
  id_partenaire INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(150) NOT NULL,
  description TEXT NULL,
  logo_url VARCHAR(255) NULL,
  site_web VARCHAR(255) NULL,
  categorie ENUM('supplier', 'certifier', 'sponsor') NOT NULL DEFAULT 'supplier',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_partenaire)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Local partners and suppliers';
-- ============================================================
-- MODULE 3 — Orders
-- ============================================================
-- Table: cache_distance
CREATE TABLE cache_distance (
  id_cache INT NOT NULL AUTO_INCREMENT,
  ville VARCHAR(100) NOT NULL,
  distance_km DECIMAL(6, 2) NOT NULL,
  cached_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  PRIMARY KEY (id_cache),
  UNIQUE KEY uq_cache_ville (ville),
  INDEX idx_cache_expires (expires_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cached delivery distances (TTL)';
-- Table: commande
CREATE TABLE commande (
  id_commande INT NOT NULL AUTO_INCREMENT,
  id_utilisateur INT NOT NULL,
  date_evenement DATETIME NOT NULL,
  adresse_livraison VARCHAR(255) NOT NULL,
  code_postal_livraison VARCHAR(10) NOT NULL,
  ville_livraison VARCHAR(100) NOT NULL,
  distance_km DECIMAL(6, 2) NULL DEFAULT 0.00,
  nb_personnes INT NOT NULL,
  sous_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  montant_remise DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  frais_livraison DECIMAL(8, 2) NOT NULL DEFAULT 0.00,
  total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  statut ENUM(
    'pending',
    'confirmed',
    'in_preparation',
    'in_delivery',
    'completed',
    'cancelled'
  ) NOT NULL DEFAULT 'pending',
  motif_annulation TEXT NULL,
  id_utilisateur_annulation INT NULL,
  annulee_a DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_commande),
  INDEX idx_commande_utilisateur (id_utilisateur),
  INDEX idx_commande_statut (statut),
  INDEX idx_commande_date_evenement (date_evenement),
  CONSTRAINT fk_commande_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_commande_annulation FOREIGN KEY (id_utilisateur_annulation) REFERENCES utilisateur (id_utilisateur) ON DELETE
  SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Customer orders';
-- Table: ligne_commande
-- Note: CHECK constraint added via ALTER TABLE due to MySQL 8.x limitation
-- with CHECK + FK referential actions on the same column
CREATE TABLE ligne_commande (
  id_ligne INT NOT NULL AUTO_INCREMENT,
  id_commande INT NOT NULL,
  id_menu INT NULL COMMENT 'Nullable FK to menu',
  id_service INT NULL COMMENT 'Nullable FK to service',
  id_offre INT NULL COMMENT 'Nullable FK to anti-waste offer',
  quantite INT NOT NULL DEFAULT 1,
  duree_heures INT NULL COMMENT 'Duration for hourly services',
  prix_unitaire DECIMAL(8, 2) NOT NULL COMMENT 'Frozen unit price at order time',
  sous_total DECIMAL(10, 2) NOT NULL,
  notes TEXT NULL,
  PRIMARY KEY (id_ligne),
  INDEX idx_lc_commande (id_commande),
  INDEX idx_lc_menu (id_menu),
  INDEX idx_lc_service (id_service),
  INDEX idx_lc_offre (id_offre),
  CONSTRAINT fk_lc_commande FOREIGN KEY (id_commande) REFERENCES commande (id_commande) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_lc_menu FOREIGN KEY (id_menu) REFERENCES menu (id_menu) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_lc_service FOREIGN KEY (id_service) REFERENCES service (id_service) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_lc_offre FOREIGN KEY (id_offre) REFERENCES offre_anti_gaspi (id_offre) ON DELETE
  SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Order line items with price snapshot';
-- ============================================================
-- MODULE 4 — Reviews
-- ============================================================
-- Table: avis
CREATE TABLE avis (
  id_avis INT NOT NULL AUTO_INCREMENT,
  id_utilisateur INT NOT NULL,
  id_commande INT NOT NULL,
  note INT NOT NULL COMMENT 'Rating 1-5',
  commentaire TEXT NULL,
  statut ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  id_moderateur INT NULL,
  moderated_at DATETIME NULL,
  motif_moderation TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_avis),
  INDEX idx_avis_utilisateur (id_utilisateur),
  INDEX idx_avis_commande (id_commande),
  INDEX idx_avis_statut (statut),
  INDEX idx_avis_note (note),
  CONSTRAINT fk_avis_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_avis_commande FOREIGN KEY (id_commande) REFERENCES commande (id_commande) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_avis_moderateur FOREIGN KEY (id_moderateur) REFERENCES utilisateur (id_utilisateur) ON DELETE
  SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_avis_note CHECK (
      note BETWEEN 1 AND 5
    )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Customer reviews with moderation';
-- ============================================================
-- MODULE 6 — System and Configuration
-- ============================================================
-- Table: membre_equipe
CREATE TABLE membre_equipe (
  id_membre INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  poste VARCHAR(150) NOT NULL,
  bio TEXT NULL,
  photo_url VARCHAR(255) NULL,
  ordre_affichage INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_membre),
  INDEX idx_membre_ordre (ordre_affichage)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Public team members';
-- Table: horaires_ouverture
CREATE TABLE horaires_ouverture (
  id_horaire INT NOT NULL AUTO_INCREMENT,
  jour_semaine ENUM(
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
    'sunday'
  ) NOT NULL,
  heure_ouverture TIME NULL,
  heure_fermeture TIME NULL,
  is_ferme TINYINT(1) NOT NULL DEFAULT 0,
  date_speciale DATE NULL COMMENT 'Override for specific dates',
  notes VARCHAR(255) NULL,
  PRIMARY KEY (id_horaire),
  UNIQUE KEY uq_horaire_jour (jour_semaine, date_speciale)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Weekly opening hours with overrides';
-- Table: contenu_rse
CREATE TABLE contenu_rse (
  id_contenu INT NOT NULL AUTO_INCREMENT,
  cle_section VARCHAR(50) NOT NULL,
  titre VARCHAR(200) NOT NULL,
  contenu TEXT NULL,
  image_url VARCHAR(255) NULL,
  ordre_affichage INT NOT NULL DEFAULT 0,
  id_utilisateur_maj INT NULL,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_contenu),
  UNIQUE KEY uq_contenu_cle (cle_section),
  CONSTRAINT fk_rse_utilisateur FOREIGN KEY (id_utilisateur_maj) REFERENCES utilisateur (id_utilisateur) ON DELETE
  SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Editable RSE page content';
-- ============================================================
-- MODULE 7 — Audit and Traceability
-- ============================================================
-- Table: historique_statut
CREATE TABLE historique_statut (
  id_historique INT NOT NULL AUTO_INCREMENT,
  id_commande INT NOT NULL,
  statut_precedent VARCHAR(50) NOT NULL,
  nouveau_statut VARCHAR(50) NOT NULL,
  id_utilisateur INT NOT NULL,
  motif TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_historique),
  INDEX idx_historique_commande (id_commande),
  CONSTRAINT fk_historique_commande FOREIGN KEY (id_commande) REFERENCES commande (id_commande) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_historique_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Order status change timeline';
-- Table: message_contact
CREATE TABLE message_contact (
  id_message INT NOT NULL AUTO_INCREMENT,
  nom_expediteur VARCHAR(150) NOT NULL,
  email_expediteur VARCHAR(255) NOT NULL,
  telephone_expediteur VARCHAR(20) NULL,
  sujet VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  statut ENUM('unread', 'read', 'replied', 'archived') NOT NULL DEFAULT 'unread',
  id_assigne INT NULL,
  repondu_a DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_message),
  INDEX idx_message_statut (statut),
  CONSTRAINT fk_message_assigne FOREIGN KEY (id_assigne) REFERENCES utilisateur (id_utilisateur) ON DELETE
  SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Contact form submissions';
-- Table: log_email
CREATE TABLE log_email (
  id_log INT NOT NULL AUTO_INCREMENT,
  destinataire VARCHAR(255) NOT NULL,
  sujet VARCHAR(255) NOT NULL,
  template VARCHAR(100) NOT NULL,
  statut ENUM('sent', 'failed', 'retrying') NOT NULL DEFAULT 'sent',
  message_erreur TEXT NULL,
  type_entite VARCHAR(50) NULL,
  id_entite INT NULL,
  envoye_a DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_log),
  INDEX idx_log_statut (statut),
  INDEX idx_log_destinataire (destinataire)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Email delivery log';
-- Table: notification
CREATE TABLE notification (
  id_notification INT NOT NULL AUTO_INCREMENT,
  id_utilisateur INT NOT NULL,
  type ENUM(
    'order_status',
    'review_response',
    'anti_waste',
    'system'
  ) NOT NULL,
  titre VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  lien_url VARCHAR(255) NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  read_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_notification),
  INDEX idx_notification_utilisateur (id_utilisateur),
  INDEX idx_notification_is_read (is_read),
  CONSTRAINT fk_notification_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'In-app user notifications';
-- Table: log_audit
CREATE TABLE log_audit (
  id_audit INT NOT NULL AUTO_INCREMENT,
  id_utilisateur INT NULL COMMENT 'NULL for anonymous actions',
  action ENUM(
    'create',
    'update',
    'delete',
    'login',
    'logout',
    'view'
  ) NOT NULL,
  type_entite VARCHAR(100) NOT NULL,
  id_entite INT NULL,
  anciennes_valeurs JSON NULL,
  nouvelles_valeurs JSON NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_audit),
  INDEX idx_audit_utilisateur (id_utilisateur),
  INDEX idx_audit_action (action),
  INDEX idx_audit_entite (type_entite, id_entite),
  INDEX idx_audit_created (created_at),
  CONSTRAINT fk_audit_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur) ON DELETE
  SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Audit trail for compliance';
-- ============================================================
-- POST-CREATION CONSTRAINTS
-- ============================================================
-- Validation rule: each ligne_commande must reference exactly ONE
-- item type (menu OR service OR offre). This is enforced in the
-- application layer (OrderController) due to MySQL 8.x limitation
-- with CHECK constraints + FK referential actions on the same column.
-- ============================================================
-- ARCHITECTURE NOTES
-- ============================================================
-- ENUM vs Reference tables:
-- Current implementation uses ENUM for simplicity (ECF scope).
-- For production at scale, consider replacing with reference tables
-- (ref_categorie_service, ref_type_alerte, etc.) to allow adding
-- new categories without ALTER TABLE.
-- log_audit purge strategy:
-- Recommended retention: 12 months active, then archive.
-- Options: monthly partitioning, archive table, or scheduled cleanup.
-- GDPR compliance: data must be deletable on user request.
-- Phone format:
-- Application layer must normalize to E.164 format (+33XXXXXXXXX).
-- Validation via regex before INSERT/UPDATE.
-- Slug strategy:
-- Menu slugs are generated from titles (lowercase, accents stripped,
-- spaces replaced with hyphens). Application layer generates slugs
-- via a helper function on INSERT/UPDATE. UNIQUE constraint prevents
-- duplicates. Used for SEO-friendly URLs (e.g., /catalogue/menu-elegance).
-- ============================================================
-- Finalize
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
