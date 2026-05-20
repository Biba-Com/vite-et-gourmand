-- ============================================================
-- VITE & GOURMAND — FIXTURES v3.0 DÉFINITIF
-- ============================================================
-- Aligné exactement sur TOUTES les structures BDD réelles
-- Mot de passe universel test : "password"
-- Hash bcrypt : $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE log_audit;
TRUNCATE TABLE notification;
TRUNCATE TABLE log_email;
TRUNCATE TABLE message_contact;
TRUNCATE TABLE historique_statut;
TRUNCATE TABLE contenu_rse;
TRUNCATE TABLE horaires_ouverture;
TRUNCATE TABLE membre_equipe;
TRUNCATE TABLE partenaire;
TRUNCATE TABLE abonnement_alerte;
TRUNCATE TABLE offre_anti_gaspi;
TRUNCATE TABLE avis;
TRUNCATE TABLE ligne_commande;
TRUNCATE TABLE commande;
TRUNCATE TABLE menu_certification;
TRUNCATE TABLE menu_badge_eco;
TRUNCATE TABLE menu_regime;
TRUNCATE TABLE plat_allergene;
TRUNCATE TABLE composition_menu;
TRUNCATE TABLE service;
TRUNCATE TABLE plat;
TRUNCATE TABLE menu;
TRUNCATE TABLE certification;
TRUNCATE TABLE badge_eco;
TRUNCATE TABLE regime;
TRUNCATE TABLE allergene;
TRUNCATE TABLE theme;
TRUNCATE TABLE utilisateur;

SET FOREIGN_KEY_CHECKS = 1;

-- ════════════════════════════════════════════════════════════
-- UTILISATEURS
-- Colonnes : id_utilisateur, email, mot_de_passe, nom, prenom,
-- telephone, adresse, code_postal, ville, role, is_active, created_at
-- ════════════════════════════════════════════════════════════
INSERT INTO utilisateur
    (id_utilisateur, email, mot_de_passe, nom, prenom, telephone,
     adresse, code_postal, ville, role, is_active, created_at)
VALUES
    (1, 'jose.viteetgourmand@gmail.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Carrère', 'José', '0556000001',
     '12 Rue de la Gastronomie', '33000', 'Bordeaux',
     'admin', 1, '2025-01-15 09:00:00'),

    (2, 'julie.viteetgourmand@gmail.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Lartigue', 'Julie', '0556000002',
     '12 Rue de la Gastronomie', '33000', 'Bordeaux',
     'employee', 1, '2025-01-15 09:15:00'),

    (3, 'marc.dubreuilh@viteetgourmand.fr',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Dubreuilh', 'Marc', '0556000003',
     '45 Rue Saint-Rémi', '33000', 'Bordeaux',
     'employee', 1, '2025-02-01 10:00:00'),

    (4, 'sophie.lacaze@gmail.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Lacaze', 'Sophie', '0612345678',
     'Château du Taillan', '33320', 'Le Taillan-Médoc',
     'client', 1, '2025-12-10 14:30:00'),

    (5, 'antoine.darrieussecq@gmail.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Darrieussecq', 'Antoine', '0623456789',
     '8 Quai de Bacalan', '33300', 'Bordeaux',
     'client', 1, '2026-01-20 16:45:00'),

    (6, 'camille.duboscq@gmail.com',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Duboscq', 'Camille', '0634567890',
     '22 Avenue Louis Barthou', '33200', 'Bordeaux',
     'client', 1, '2026-03-05 11:20:00');

-- ════════════════════════════════════════════════════════════
-- THÈMES
-- ════════════════════════════════════════════════════════════
INSERT INTO theme (id_theme, nom, description, couleur)
VALUES
    (1, 'Noël',       'Menus festifs pour les fêtes de fin d''année',      '#C62828'),
    (2, 'Pâques',     'Menus de printemps autour de l''agneau et légumes', '#9C27B0'),
    (3, 'Classique',  'Grands classiques de la gastronomie bordelaise',    '#063A1F'),
    (4, 'Événement',  'Mariages, anniversaires, fêtes privées',            '#D4AF37'),
    (5, 'Entreprise', 'Buffets, cocktails et séminaires professionnels',   '#1565C0');

-- ════════════════════════════════════════════════════════════
-- RÉGIMES
-- ════════════════════════════════════════════════════════════
INSERT INTO regime (id_regime, nom, description)
VALUES
    (1, 'Classique',   'Menu standard avec toutes catégories d''aliments'),
    (2, 'Végétarien',  'Sans viande ni poisson'),
    (3, 'Végan',       'Aucun produit d''origine animale'),
    (4, 'Sans gluten', 'Conforme aux régimes coeliaques'),
    (5, 'Halal',       'Viandes issues de filières certifiées halal');

-- ════════════════════════════════════════════════════════════
-- ALLERGÈNES (14 — Règlement UE n°1169/2011)
-- ════════════════════════════════════════════════════════════
INSERT INTO allergene (id_allergene, nom, icone)
VALUES
    (1,  'Gluten',            '🌾'),
    (2,  'Crustacés',         '🦐'),
    (3,  'Œufs',              '🥚'),
    (4,  'Poissons',          '🐟'),
    (5,  'Arachides',         '🥜'),
    (6,  'Soja',              '🌱'),
    (7,  'Lait',              '🥛'),
    (8,  'Fruits à coque',    '🌰'),
    (9,  'Céleri',            '🥬'),
    (10, 'Moutarde',          '🟡'),
    (11, 'Graines de sésame', '🌻'),
    (12, 'Sulfites',          '🍇'),
    (13, 'Lupin',             '🌼'),
    (14, 'Mollusques',        '🐚');

-- ════════════════════════════════════════════════════════════
-- BADGES ÉCO
-- ════════════════════════════════════════════════════════════
INSERT INTO badge_eco (id_badge, nom, description, icone)
VALUES
    (1, 'Local',       'Produits issus de producteurs girondins',         '🌍'),
    (2, 'Zéro déchet', 'Emballages compostables, vaisselle réutilisable', '♻️'),
    (3, 'Végétal',     'Menu majoritairement végétal',                    '🌿');

-- ════════════════════════════════════════════════════════════
-- CERTIFICATIONS
-- ════════════════════════════════════════════════════════════
INSERT INTO certification (id_certification, nom, description, logo_url)
VALUES
    (1, 'Bio',                'Label Agriculture Biologique européen', '/assets/img/certif/bio.svg'),
    (2, 'Label Rouge',        'Qualité supérieure certifiée',          '/assets/img/certif/label-rouge.svg'),
    (3, 'Bordeaux Métropole', 'Producteur partenaire local',           '/assets/img/certif/metropole.svg');

-- ════════════════════════════════════════════════════════════
-- PLATS (20)
-- ════════════════════════════════════════════════════════════
INSERT INTO plat (id_plat, nom, description, categorie, image_url, is_active)
VALUES
    (1,  'Huîtres du Bassin n°3 et crépinette',
         'Trois huîtres fraîches du Cap Ferret, crépinette chaude',
         'starter', '/assets/img/plats/huitres.jpg', 1),
    (2,  'Velouté d''asperges blanches du Blayais',
         'Asperges de saison en circuit court, éclat de noisettes',
         'starter', '/assets/img/plats/veloute-asperges.jpg', 1),
    (3,  'Foie gras mi-cuit et chutney de figues',
         'Foie gras maison mariné, chutney de figues du Sud-Ouest',
         'starter', '/assets/img/plats/foie-gras.jpg', 1),
    (4,  'Saumon fumé à la ficelle et blinis maison',
         'Saumon fumé artisanalement, blinis tièdes crème ciboulette',
         'starter', '/assets/img/plats/saumon-fume.jpg', 1),
    (5,  'Salade landaise traditionnelle',
         'Gésiers confits, magret séché, pignons, mesclun bordelais',
         'starter', '/assets/img/plats/salade-landaise.jpg', 1),
    (6,  'Velouté de potimarron à la châtaigne',
         'Potimarron de saison, châtaignes, crème fouettée',
         'starter', '/assets/img/plats/veloute-potimarron.jpg', 1),
    (7,  'Tartare de légumes croquants',
         'Concombre, radis, avocat, herbes fraîches, vinaigrette balsamique',
         'starter', '/assets/img/plats/tartare-legumes.jpg', 1),
    (8,  'Entrecôte à la bordelaise',
         'Bœuf de Bazas, sauce vin rouge de Bordeaux et moelle',
         'main', '/assets/img/plats/entrecote-bordelaise.jpg', 1),
    (9,  'Magret de canard rôti, sauce aux fruits',
         'Magret du Sud-Ouest, sauce orange-cassis, légumes glacés',
         'main', '/assets/img/plats/magret-canard.jpg', 1),
    (10, 'Filet de bœuf Rossini, sauce périgueux',
         'Filet de bœuf, escalope de foie gras poêlée, sauce truffée',
         'main', '/assets/img/plats/filet-rossini.jpg', 1),
    (11, 'Gigot d''agneau rôti aux herbes',
         'Agneau de Pauillac, herbes de Provence, ail confit',
         'main', '/assets/img/plats/gigot-agneau.jpg', 1),
    (12, 'Esturgeon de l''Estuaire d''Aquitaine',
         'Filet d''esturgeon poêlé, mousseline de poireaux',
         'main', '/assets/img/plats/esturgeon.jpg', 1),
    (13, 'Wok de légumes croquants au tofu',
         'Brocolis, carottes, poivrons, tofu mariné sauce soja',
         'main', '/assets/img/plats/wok-legumes.jpg', 1),
    (14, 'Risotto crémeux aux cèpes du Médoc',
         'Riz arborio, cèpes du Médoc, parmesan affiné 24 mois',
         'main', '/assets/img/plats/risotto-cepes.jpg', 1),
    (15, 'Véritables canelés bordelais',
         'Cœur tendre et robe caramélisée, rhum et vanille bourbon',
         'dessert', '/assets/img/plats/caneles.jpg', 1),
    (16, 'Dunes blanches du Cap Ferret',
         'Chouquettes garnies d''une crème mousseline secrète',
         'dessert', '/assets/img/plats/dunes-blanches.jpg', 1),
    (17, 'Tarte au citron meringuée',
         'Pâte sablée, crème citron, meringue italienne',
         'dessert', '/assets/img/plats/tarte-citron.jpg', 1),
    (18, 'Café gourmand bordelais',
         'Trio mignardises : canelé, dune blanche, macaron, café',
         'dessert', '/assets/img/plats/cafe-gourmand.jpg', 1),
    (19, 'Assortiment de fromages affinés',
         'Plateau fromages régionaux, confiture cerises, pain levain',
         'dessert', '/assets/img/plats/fromages.jpg', 1),
    (20, 'Sélection de vins de Bordeaux',
         'Verre dégustation : Médoc, Saint-Émilion ou Pessac-Léognan',
         'drink', '/assets/img/plats/vins-bordeaux.jpg', 1);

-- ════════════════════════════════════════════════════════════
-- MENUS (8)
-- Colonnes exactes : id_menu, id_theme, titre, slug, description,
-- prix_par_personne, nb_personnes_min, nb_personnes_max,
-- image_url, is_active
-- ════════════════════════════════════════════════════════════
INSERT INTO menu
    (id_menu, id_theme, titre, slug, description,
     prix_par_personne, nb_personnes_min, nb_personnes_max,
     image_url, is_active)
VALUES
    (1, 4, 'Menu Élégance', 'menu-elegance',
     'Notre menu signature pour mariages et grandes célébrations. Foie gras, entrecôte bordelaise et canelés revisités.',
     89.00, 50, 200, '/assets/img/menus/menu-elegance.jpg', 1),

    (2, 4, 'Menu Prestige', 'menu-prestige',
     'Le summum de la gastronomie bordelaise : filet Rossini, sauce périgueux et accord mets-vins d''exception.',
     125.00, 30, 100, '/assets/img/menus/menu-prestige.jpg', 1),

    (3, 5, 'Buffet Corporate', 'buffet-corporate',
     'Buffet professionnel raffiné pour vos séminaires et événements d''entreprise.',
     45.00, 20, 200, '/assets/img/menus/buffet-corporate.jpg', 1),

    (4, 3, 'Menu Festif', 'menu-festif',
     'Menu convivial pour anniversaires et fêtes familiales. Magret de canard, fromages affinés, dunes blanches.',
     38.00, 15, 80, '/assets/img/menus/menu-festif.jpg', 1),

    (5, 5, 'Cocktail Chic', 'cocktail-chic',
     'Assortiment de bouchées raffinées pour cocktails dînatoires : huîtres, foie gras, mignardises.',
     32.00, 20, 150, '/assets/img/menus/cocktail-chic.jpg', 1),

    (6, 3, 'Menu Terroir', 'menu-terroir',
     'Produits du terroir aquitain : salade landaise, gigot d''agneau de Pauillac, fromages régionaux.',
     52.00, 25, 100, '/assets/img/menus/menu-terroir.jpg', 1),

    (7, 5, 'Apéritif Dînatoire', 'aperitif-dinatoire',
     'Formule apéritif complète : 8 bouchées par personne, idéal pour lancements produits et inaugurations.',
     42.00, 15, 100, '/assets/img/menus/aperitif-dinatoire.jpg', 1),

    (8, 5, 'Pause Café Premium', 'pause-cafe-premium',
     'Viennoiseries artisanales et boissons chaudes pour vos pauses de séminaire.',
     12.00, 10, 100, '/assets/img/menus/pause-cafe.jpg', 1);

-- ════════════════════════════════════════════════════════════
-- COMPOSITION DES MENUS
-- ════════════════════════════════════════════════════════════
INSERT INTO composition_menu (id_menu, id_plat, quantite, ordre_affichage)
VALUES
    (1, 3,  1, 1), (1, 8,  1, 2), (1, 19, 1, 3), (1, 15, 2, 4), (1, 20, 1, 5),
    (2, 3,  1, 1), (2, 10, 1, 2), (2, 19, 1, 3), (2, 16, 1, 4), (2, 20, 1, 5),
    (3, 4,  1, 1), (3, 9,  1, 2), (3, 18, 1, 3),
    (4, 5,  1, 1), (4, 9,  1, 2), (4, 16, 1, 3),
    (5, 1,  3, 1), (5, 3,  1, 2), (5, 15, 2, 3),
    (6, 5,  1, 1), (6, 11, 1, 2), (6, 19, 1, 3), (6, 15, 1, 4),
    (7, 1,  2, 1), (7, 7,  1, 2), (7, 18, 1, 3),
    (8, 15, 2, 1), (8, 16, 1, 2);

-- ════════════════════════════════════════════════════════════
-- LIAISONS PLAT ↔ ALLERGÈNE
-- ════════════════════════════════════════════════════════════
INSERT INTO plat_allergene (id_plat, id_allergene)
VALUES
    (1, 14), (1, 12),
    (2, 7),  (2, 8),
    (3, 1),  (3, 12),
    (4, 4),  (4, 7),  (4, 1),  (4, 3),
    (5, 3),  (5, 10), (5, 12),
    (6, 7),  (6, 8),
    (8, 12), (8, 7),
    (9, 10), (9, 12),
    (10, 1), (10, 7), (10, 12),
    (11, 10),(11, 12),
    (12, 4), (12, 7),
    (13, 6), (13, 11),(13, 1),
    (14, 7),
    (15, 1), (15, 7), (15, 3),
    (16, 1), (16, 7), (16, 3),
    (17, 1), (17, 7), (17, 3),
    (18, 1), (18, 7), (18, 3), (18, 8),
    (19, 7),
    (20, 12);

-- ════════════════════════════════════════════════════════════
-- LIAISONS MENU ↔ RÉGIME
-- ════════════════════════════════════════════════════════════
INSERT INTO menu_regime (id_menu, id_regime)
VALUES
    (1, 1), (1, 5),
    (2, 1),
    (3, 1), (3, 2),
    (4, 1),
    (5, 1), (5, 2),
    (6, 1),
    (7, 1), (7, 2), (7, 3),
    (8, 1), (8, 2);

-- ════════════════════════════════════════════════════════════
-- LIAISONS MENU ↔ BADGE ÉCO
-- ════════════════════════════════════════════════════════════
INSERT INTO menu_badge_eco (id_menu, id_badge)
VALUES
    (1, 1), (2, 1),
    (3, 1), (3, 2),
    (4, 1), (5, 1), (6, 1),
    (7, 1), (7, 2), (7, 3),
    (8, 1), (8, 2);

-- ════════════════════════════════════════════════════════════
-- LIAISONS MENU ↔ CERTIFICATION
-- ════════════════════════════════════════════════════════════
INSERT INTO menu_certification (id_menu, id_certification)
VALUES
    (1, 2), (1, 3),
    (2, 2), (2, 3),
    (3, 1), (3, 3),
    (6, 2), (6, 3);

-- ════════════════════════════════════════════════════════════
-- SERVICES (8)
-- Colonnes exactes : id_service, nom, description, categorie,
-- prix_base, unite_tarification, is_active
-- ════════════════════════════════════════════════════════════
INSERT INTO service
    (id_service, nom, description, categorie, prix_base, unite_tarification, is_active)
VALUES
    (1, 'Service à table',
     'Serveurs professionnels (1 pour 25 convives)',
     'staff', 150.00, 'per_unit', 1),

    (2, 'Location vaisselle',
     'Vaisselle complète : assiettes, verres, couverts',
     'equipment', 8.00, 'per_person', 1),

    (3, 'Location mobilier',
     'Tables rondes, chaises, nappes',
     'equipment', 12.00, 'per_person', 1),

    (4, 'Décoration florale',
     'Compositions florales selon thème',
     'supplement', 25.00, 'per_unit', 1),

    (5, 'Accord mets-vins',
     'Sélection de vins de Bordeaux par sommelier',
     'drinks', 15.00, 'per_person', 1),

    (6, 'Animation musicale',
     'DJ ou musicien live',
     'supplement', 400.00, 'flat_rate', 1),

    (7, 'Livraison emballages éco',
     'Vaisselle compostable et emballages biodégradables',
     'supplement', 3.00, 'per_person', 1),

    (8, 'Brigade cuisine sur place',
     'Chef et commis pour finition sur site',
     'staff', 250.00, 'per_unit', 1);

-- ════════════════════════════════════════════════════════════
-- COMMANDES (3)
-- Colonnes exactes : id_commande, id_utilisateur, date_evenement,
-- adresse_livraison, code_postal_livraison, ville_livraison,
-- distance_km, nb_personnes, sous_total, montant_remise,
-- frais_livraison, total, statut, created_at
-- ════════════════════════════════════════════════════════════
INSERT INTO commande
    (id_commande, id_utilisateur, date_evenement, adresse_livraison,
     code_postal_livraison, ville_livraison, distance_km, nb_personnes,
     sous_total, montant_remise, frais_livraison, total, statut, created_at)
VALUES
    (1, 6, '2026-05-10 19:30:00',
     '22 Avenue Louis Barthou', '33200', 'Bordeaux', 3.5, 20,
     760.00, 76.00, 5.00, 689.00, 'completed', '2026-04-20 14:30:00'),

    (2, 4, '2026-06-15 12:00:00',
     'Château du Taillan', '33320', 'Le Taillan-Médoc', 14.2, 60,
     5340.00, 534.00, 13.36, 4819.36, 'confirmed', '2026-05-02 09:15:00'),

    (3, 5, '2026-05-22 12:00:00',
     '8 Quai de Bacalan', '33300', 'Bordeaux', 0.0, 50,
     2250.00, 225.00, 0.00, 2025.00, 'in_preparation', '2026-05-08 11:05:00');

-- ════════════════════════════════════════════════════════════
-- LIGNES DE COMMANDE
-- Colonnes exactes : id_commande, id_menu, quantite,
-- prix_unitaire, sous_total, notes
-- ════════════════════════════════════════════════════════════
INSERT INTO ligne_commande
    (id_commande, id_menu, quantite, prix_unitaire, sous_total, notes)
VALUES
    (1, 4, 20, 38.00,  760.00,  'Anniversaire — décoration thème jardin'),
    (2, 1, 60, 89.00,  5340.00, 'Mariage — accord mets-vins, vaisselle haut de gamme'),
    (3, 3, 50, 45.00,  2250.00, 'Séminaire — emballages compostables (charte RSE)');

-- ════════════════════════════════════════════════════════════
-- AVIS (1 seul — commande terminée uniquement)
-- Colonnes exactes : id_utilisateur, id_commande, note,
-- commentaire, statut, id_moderateur, moderated_at, created_at
-- ════════════════════════════════════════════════════════════
INSERT INTO avis
    (id_utilisateur, id_commande, note, commentaire,
     statut, id_moderateur, moderated_at, created_at)
VALUES
    (6, 1, 5,
     'Anniversaire parfait ! Les dunes blanches ont fait fureur. Service impeccable. Je recommande !',
     'approved', 2, '2026-05-12 10:00:00', '2026-05-11 16:30:00');

-- ════════════════════════════════════════════════════════════
-- OFFRES ANTI-GASPI
-- Colonnes exactes : id_offre, id_menu, titre, description,
-- prix_original, prix_remise, quantite_disponible,
-- disponible_jusqua, is_active
-- ════════════════════════════════════════════════════════════
INSERT INTO offre_anti_gaspi
    (id_offre, id_menu, titre, description,
     prix_original, prix_remise, quantite_disponible,
     disponible_jusqua, is_active)
VALUES
    (1, 8, 'Pause Café -50%',
     'Surplus de viennoiseries. À récupérer en boutique avant 18h.',
     12.00, 6.00, 15, '2026-05-25 18:00:00', 1),

    (2, 5, 'Cocktail Chic -40%',
     'Préparations excédentaires d''un séminaire annulé.',
     32.00, 19.20, 8, '2026-05-26 20:00:00', 1);

-- ════════════════════════════════════════════════════════════
-- ABONNEMENTS ALERTES
-- Colonnes exactes : id_utilisateur, type_alerte, is_active
-- ════════════════════════════════════════════════════════════
INSERT INTO abonnement_alerte (id_utilisateur, type_alerte, is_active)
VALUES
    (4, 'anti_waste', 1),
    (5, 'anti_waste', 1),
    (6, 'new_menu',   1);

-- ════════════════════════════════════════════════════════════
-- PARTENAIRES
-- Colonnes exactes : id_partenaire, nom, description,
-- logo_url, site_web, categorie, is_active
-- ════════════════════════════════════════════════════════════
INSERT INTO partenaire
    (id_partenaire, nom, description, logo_url, site_web, categorie, is_active)
VALUES
    (1, 'Ferme du Médoc',
     'Producteur de viandes de Bazas et agneau de Pauillac en circuit court',
     '/assets/img/partenaires/ferme-medoc.svg', 'https://fermedumedoc.fr',
     'supplier', 1),

    (2, 'Ostréiculteurs du Cap Ferret',
     'Huîtres fraîches du Bassin d''Arcachon, livraison quotidienne',
     '/assets/img/partenaires/cap-ferret.svg', 'https://huitres-cap-ferret.fr',
     'supplier', 1),

    (3, 'Maraîchers du Blayais',
     'Légumes bio de saison, asperges blanches et carottes de Macau',
     '/assets/img/partenaires/blayais.svg', 'https://maraichers-blayais.fr',
     'supplier', 1);

-- ════════════════════════════════════════════════════════════
-- MEMBRES ÉQUIPE
-- Colonnes exactes : id_membre, nom, prenom, poste, bio,
-- photo_url, ordre_affichage, is_active
-- ════════════════════════════════════════════════════════════
INSERT INTO membre_equipe
    (id_membre, nom, prenom, poste, bio, photo_url, ordre_affichage, is_active)
VALUES
    (1, 'Carrère',   'José',  'Chef cuisinier & cofondateur',
     '25 ans d''expérience en gastronomie bordelaise. Passionné de produits locaux.',
     '/assets/img/equipe/jose.jpg', 1, 1),

    (2, 'Lartigue',  'Julie', 'Directrice & cofondatrice',
     'Œnologue de formation, Julie sélectionne avec passion les accords mets-vins.',
     '/assets/img/equipe/julie.jpg', 2, 1),

    (3, 'Dubreuilh', 'Marc',  'Second de cuisine',
     'Chef pâtissier formé chez les Compagnons du Tour de France.',
     '/assets/img/equipe/marc.jpg', 3, 1),

    (4, 'Bordeau',   'Léa',   'Responsable événementiel',
     'Coordonne tous vos événements de A à Z. Spécialiste mariages et réceptions.',
     '/assets/img/equipe/lea.jpg', 4, 1);

-- ════════════════════════════════════════════════════════════
-- HORAIRES
-- Colonnes exactes : id_horaire, jour_semaine, heure_ouverture,
-- heure_fermeture, is_ferme, date_speciale, notes
-- ════════════════════════════════════════════════════════════
INSERT INTO horaires_ouverture
    (id_horaire, jour_semaine, heure_ouverture, heure_fermeture,
     is_ferme, date_speciale, notes)
VALUES
    (1, 'monday',    '09:00:00', '18:00:00', 0, NULL, NULL),
    (2, 'tuesday',   '09:00:00', '18:00:00', 0, NULL, NULL),
    (3, 'wednesday', '09:00:00', '18:00:00', 0, NULL, NULL),
    (4, 'thursday',  '09:00:00', '18:00:00', 0, NULL, NULL),
    (5, 'friday',    '09:00:00', '19:00:00', 0, NULL, 'Fermeture tardive le vendredi'),
    (6, 'saturday',  '10:00:00', '16:00:00', 0, NULL, NULL),
    (7, 'sunday',    NULL,        NULL,       1, NULL, 'Fermé — Repos hebdomadaire');

-- ════════════════════════════════════════════════════════════
-- CONTENU RSE
-- Colonnes exactes : id_contenu, cle_section, titre, contenu,
-- image_url, ordre_affichage, id_utilisateur_maj
-- ════════════════════════════════════════════════════════════
INSERT INTO contenu_rse
    (id_contenu, cle_section, titre, contenu,
     image_url, ordre_affichage, id_utilisateur_maj)
VALUES
    (1, 'intro', 'Notre engagement éco-responsable',
     'Depuis 1999, Vite & Gourmand s''engage pour une gastronomie durable. Producteurs girondins en circuit court.',
     '/assets/img/rse/engagement.jpg', 1, 1),

    (2, 'zero_dechet', 'Démarche zéro déchet',
     'Vaisselle compostable certifiée, emballages biodégradables, livraisons en vélos-cargos.',
     '/assets/img/rse/zero-dechet.jpg', 2, 1),

    (3, 'partenaires', 'Nos producteurs partenaires',
     'Ferme du Médoc, ostréiculteurs du Cap Ferret, maraîchers du Blayais.',
     '/assets/img/rse/partenaires.jpg', 3, 1);

-- ════════════════════════════════════════════════════════════
-- HISTORIQUE STATUTS
-- Colonnes exactes : id_commande, statut_precedent,
-- nouveau_statut, id_utilisateur, motif, created_at
-- ════════════════════════════════════════════════════════════
INSERT INTO historique_statut
    (id_commande, statut_precedent, nouveau_statut,
     id_utilisateur, motif, created_at)
VALUES
    (1, 'pending',        'confirmed',      2, 'Acompte de 30% validé',                           '2026-04-20 15:00:00'),
    (1, 'confirmed',      'in_preparation', 3, 'Lancement préparation cuisine',                   '2026-05-09 08:00:00'),
    (1, 'in_preparation', 'in_delivery',    2, 'Chargement caissons isothermes, départ livraison','2026-05-10 17:30:00'),
    (1, 'in_delivery',    'completed',      2, 'Livraison effectuée en vélo-cargo, client ravi',  '2026-05-10 19:00:00'),
    (2, 'pending',        'confirmed',      2, 'Date bloquée dans l''agenda, devis accepté',      '2026-05-02 10:00:00'),
    (3, 'pending',        'confirmed',      2, 'Validation charte zéro déchet pour séminaire',    '2026-05-08 14:00:00'),
    (3, 'confirmed',      'in_preparation', 3, 'Réception légumes Blayais et viandes Bazas',      '2026-05-17 16:30:00');

-- ════════════════════════════════════════════════════════════
-- MESSAGES CONTACT
-- Colonnes exactes : id_message, nom_expediteur, email_expediteur,
-- telephone_expediteur, sujet, message, statut, id_assigne
-- ════════════════════════════════════════════════════════════
INSERT INTO message_contact
    (id_message, nom_expediteur, email_expediteur, telephone_expediteur,
     sujet, message, statut, id_assigne)
VALUES
    (1, 'Mathilde Lasserre', 'mathilde.lasserre@gmail.com', '0698765432',
     'Demande de devis mariage',
     'Bonjour, je souhaite organiser mon mariage pour 80 personnes le 12 septembre 2026.',
     'unread', 2),

    (2, 'Restaurant Le Quatrième', 'contact@le-quatrieme.fr', '0556789012',
     'Partenariat traiteur événementiel',
     'Nous recherchons un partenaire traiteur pour nos événements privés.',
     'read', 1);

-- ════════════════════════════════════════════════════════════
-- LOGS EMAIL
-- Colonnes exactes : id_log, destinataire, sujet, template,
-- statut, type_entite, id_entite, envoye_a
-- ════════════════════════════════════════════════════════════
INSERT INTO log_email
    (id_log, destinataire, sujet, template, statut,
     type_entite, id_entite, envoye_a)
VALUES
    (1, 'camille.duboscq@gmail.com', 'Confirmation commande #1',
     'order_confirmation', 'sent', 'commande', 1, '2026-04-20 15:01:00'),

    (2, 'sophie.lacaze@gmail.com', 'Confirmation commande #2',
     'order_confirmation', 'sent', 'commande', 2, '2026-05-02 10:01:00'),

    (3, 'camille.duboscq@gmail.com', 'Donnez votre avis',
     'review_request', 'sent', 'commande', 1, '2026-05-11 09:00:00');

-- ════════════════════════════════════════════════════════════
-- NOTIFICATIONS
-- Colonnes exactes : id_notification, id_utilisateur, type,
-- titre, message, lien_url, is_read
-- ════════════════════════════════════════════════════════════
INSERT INTO notification
    (id_notification, id_utilisateur, type, titre, message, lien_url, is_read)
VALUES
    (1, 6, 'order_status', 'Commande terminée',
     'Votre commande du 10 mai a été livrée. Donnez-nous votre avis !',
     '/mes-commandes/1', 1),

    (2, 4, 'order_status', 'Commande confirmée',
     'Votre mariage du 15 juin est confirmé.',
     '/mes-commandes/2', 0),

    (3, 5, 'order_status', 'Préparation en cours',
     'Votre séminaire du 22 mai est en préparation.',
     '/mes-commandes/3', 0);

-- ════════════════════════════════════════════════════════════
-- LOG AUDIT
-- Colonnes exactes : id_audit, id_utilisateur, action,
-- type_entite, id_entite, anciennes_valeurs, nouvelles_valeurs,
-- ip_address, user_agent
-- ════════════════════════════════════════════════════════════
INSERT INTO log_audit
    (id_audit, id_utilisateur, action, type_entite, id_entite,
     anciennes_valeurs, nouvelles_valeurs, ip_address, user_agent)
VALUES
    (1, 1, 'create', 'utilisateur', 3,
     NULL, '{"prenom":"Marc","role":"employee"}',
     '127.0.0.1', 'Mozilla/5.0'),

    (2, 2, 'update', 'commande', 1,
     '{"statut":"pending"}', '{"statut":"confirmed"}',
     '127.0.0.1', 'Mozilla/5.0'),

    (3, 2, 'update', 'avis', 1,
     '{"statut":"pending"}', '{"statut":"approved"}',
     '127.0.0.1', 'Mozilla/5.0');
