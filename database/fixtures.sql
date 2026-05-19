-- ============================================================
-- VITE & GOURMAND — FIXTURES v2.0
-- ============================================================
-- Alignement Énoncé Studi ECF DWWM
-- Identité bordelaise + cohérence temporelle (pivot mai 2026)
-- Mot de passe universel test : "password"
-- Hash bcrypt : $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── Nettoyage complet (ordre inverse des dépendances) ───────
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
TRUNCATE TABLE cache_distance;
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
TRUNCATE TABLE token_mot_de_passe;
TRUNCATE TABLE session;
TRUNCATE TABLE utilisateur;

SET FOREIGN_KEY_CHECKS = 1;

-- ════════════════════════════════════════════════════════════
-- MODULE 1 — UTILISATEURS (6 comptes)
-- ════════════════════════════════════════════════════════════
-- Conformité énoncé page 3 (Julie & José cofondateurs)
-- Patronymes typiquement girondins/gascons
INSERT INTO utilisateur (id_utilisateur, email, mot_de_passe, prenom, nom, telephone, role, adresse, code_postal, ville, pays, is_active, created_at)
VALUES
    -- ADMIN : José (cofondateur — énoncé page 9)
    (1, 'jose.viteetgourmand@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'José', 'Carrère', '0556000001', 'admin',
     '12 Rue de la Gastronomie', '33000', 'Bordeaux', 'France', 1, '2025-01-15 09:00:00'),

    -- EMPLOYÉ 1 : Julie (cofondatrice — opérationnelle)
    (2, 'julie.viteetgourmand@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Julie', 'Lartigue', '0556000002', 'employee',
     '12 Rue de la Gastronomie', '33000', 'Bordeaux', 'France', 1, '2025-01-15 09:15:00'),

    -- EMPLOYÉ 2 : Chef cuisine
    (3, 'marc.dubreuilh@viteetgourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Marc', 'Dubreuilh', '0556000003', 'employee',
     '45 Rue Saint-Rémi', '33000', 'Bordeaux', 'France', 1, '2025-02-01 10:00:00'),

    -- CLIENT 1 : Mariage au Médoc
    (4, 'sophie.lacaze@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Sophie', 'Lacaze', '0612345678', 'client',
     'Château du Taillan', '33320', 'Le Taillan-Médoc', 'France', 1, '2025-12-10 14:30:00'),

    -- CLIENT 2 : Entreprise Bassins à flot
    (5, 'antoine.darrieussecq@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Antoine', 'Darrieussecq', '0623456789', 'client',
     '8 Quai de Bacalan', '33300', 'Bordeaux', 'France', 1, '2026-01-20 16:45:00'),

    -- CLIENT 3 : Anniversaire à Caudéran
    (6, 'camille.duboscq@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     'Camille', 'Duboscq', '0634567890', 'client',
     '22 Avenue Louis Barthou', '33200', 'Bordeaux-Caudéran', 'France', 1, '2026-03-05 11:20:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — THÈMES (5 — alignés énoncé page 4)
-- ════════════════════════════════════════════════════════════
INSERT INTO theme (id_theme, nom, description, couleur)
VALUES
    (1, 'Noël',         'Menus festifs traditionnels pour les fêtes de fin d''année',         '#C62828'),
    (2, 'Pâques',       'Menus de printemps autour de l''agneau et des légumes nouveaux',     '#9C27B0'),
    (3, 'Classique',    'Grands classiques de la gastronomie française et bordelaise',        '#063A1F'),
    (4, 'Événement',    'Mariages, anniversaires, fêtes privées',                             '#D4AF37'),
    (5, 'Entreprise',   'Buffets, cocktails et séminaires professionnels',                    '#1565C0');

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — RÉGIMES (5 — alignés énoncé page 4)
-- ════════════════════════════════════════════════════════════
INSERT INTO regime (id_regime, nom, description)
VALUES
    (1, 'Classique',    'Menu standard avec toutes catégories d''aliments'),
    (2, 'Végétarien',   'Sans viande ni poisson'),
    (3, 'Végan',        'Aucun produit d''origine animale'),
    (4, 'Sans gluten',  'Conforme aux régimes coeliaques'),
    (5, 'Halal',        'Viandes issues de filières certifiées halal');

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — ALLERGÈNES (14 — Règlement UE n°1169/2011)
-- ════════════════════════════════════════════════════════════
INSERT INTO allergene (id_allergene, nom, icone)
VALUES
    (1,  'Gluten',           '🌾'),
    (2,  'Crustacés',        '🦐'),
    (3,  'Œufs',             '🥚'),
    (4,  'Poissons',         '🐟'),
    (5,  'Arachides',        '🥜'),
    (6,  'Soja',             '🌱'),
    (7,  'Lait',             '🥛'),
    (8,  'Fruits à coque',   '🌰'),
    (9,  'Céleri',           '🥬'),
    (10, 'Moutarde',         '🟡'),
    (11, 'Graines de sésame','🌻'),
    (12, 'Sulfites',         '🍇'),
    (13, 'Lupin',            '🌼'),
    (14, 'Mollusques',       '🐚');

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — BADGES ÉCO (3)
-- ════════════════════════════════════════════════════════════
INSERT INTO badge_eco (id_badge, nom, description, icone)
VALUES
    (1, 'Local',         'Produits issus de producteurs girondins',     '🌍'),
    (2, 'Zéro déchet',   'Emballages compostables, vaisselle réutilisable', '♻️'),
    (3, 'Végétal',       'Menu majoritairement végétal',                '🌿');

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — CERTIFICATIONS (3)
-- ════════════════════════════════════════════════════════════
INSERT INTO certification (id_certification, nom, description, logo_url)
VALUES
    (1, 'Bio',                 'Label Agriculture Biologique européen',   '/assets/img/certif/bio.svg'),
    (2, 'Label Rouge',          'Qualité supérieure certifiée',            '/assets/img/certif/label-rouge.svg'),
    (3, 'Bordeaux Métropole',  'Producteur partenaire local',             '/assets/img/certif/metropole.svg');

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — PLATS (20 — ancrage bordelais)
-- ════════════════════════════════════════════════════════════
INSERT INTO plat (id_plat, nom, description, categorie, image_url, is_active)
VALUES
    -- Entrées (1-7)
    (1,  'Huîtres du Bassin n°3 et crépinette',  'Trois huîtres fraîches du Cap Ferret accompagnées d''une crépinette chaude au porc',  'starter', '/assets/img/plats/huitres-cap-ferret.jpg', 1),
    (2,  'Velouté d''asperges blanches du Blayais', 'Asperges de saison en circuit court, éclat de noisettes torréfiées',                'starter', '/assets/img/plats/veloute-asperges.jpg',   1),
    (3,  'Foie gras mi-cuit et chutney de figues', 'Foie gras maison mariné, accompagné d''un chutney de figues du Sud-Ouest',          'starter', '/assets/img/plats/foie-gras.jpg',          1),
    (4,  'Saumon fumé à la ficelle, blinis maison', 'Saumon fumé artisanalement et blinis tièdes à la crème ciboulette',                 'starter', '/assets/img/plats/saumon-fume.jpg',        1),
    (5,  'Salade landaise traditionnelle',         'Gésiers confits, magret séché, pignons, mesclun bordelais',                          'starter', '/assets/img/plats/salade-landaise.jpg',    1),
    (6,  'Velouté de potimarron à la châtaigne',   'Potimarron de saison, châtaignes de l''Ardèche, crème fouettée',                     'starter', '/assets/img/plats/veloute-potimarron.jpg', 1),
    (7,  'Tartare de légumes croquants',            'Concombre, radis, avocat, herbes fraîches, vinaigrette balsamique',                  'starter', '/assets/img/plats/tartare-legumes.jpg',    1),

    -- Plats (8-14)
    (8,  'Entrecôte à la bordelaise',              'Bœuf de Bazas, sauce au vin rouge de Bordeaux et moelle, échalotes confites',        'main',    '/assets/img/plats/entrecote-bordelaise.jpg', 1),
    (9,  'Magret de canard rôti, sauce aux fruits', 'Magret du Sud-Ouest, sauce orange-cassis, légumes glacés',                          'main',    '/assets/img/plats/magret-canard.jpg',        1),
    (10, 'Filet de bœuf Rossini, sauce périgueux', 'Filet de bœuf, escalope de foie gras poêlée, sauce truffée du Périgord',             'main',    '/assets/img/plats/filet-rossini.jpg',        1),
    (11, 'Gigot d''agneau rôti aux herbes',         'Agneau de Pauillac, herbes de Provence, ail confit, jus de cuisson',                'main',    '/assets/img/plats/gigot-agneau.jpg',         1),
    (12, 'Esturgeon de l''Estuaire d''Aquitaine',   'Filet d''esturgeon poêlé, mousseline de poireaux, brunoise de légumes',             'main',    '/assets/img/plats/esturgeon.jpg',            1),
    (13, 'Wok de légumes croquants au tofu',       'Brocolis, carottes, poivrons, tofu mariné sauce soja et gingembre',                  'main',    '/assets/img/plats/wok-legumes.jpg',          1),
    (14, 'Risotto crémeux aux cèpes',              'Riz arborio, cèpes du Médoc, parmesan affiné 24 mois',                              'main',    '/assets/img/plats/risotto-cepes.jpg',        1),

    -- Desserts (15-19)
    (15, 'Véritables canelés bordelais',           'Cœur tendre et robe caramélisée, parfumés au rhum et vanille bourbon',               'dessert', '/assets/img/plats/caneles.jpg',              1),
    (16, 'Dunes blanches du Cap Ferret',           'Chouquettes garnies d''une crème mousseline secrète et aérienne',                   'dessert', '/assets/img/plats/dunes-blanches.jpg',       1),
    (17, 'Tarte au citron meringuée',              'Pâte sablée, crème citron, meringue italienne',                                     'dessert', '/assets/img/plats/tarte-citron.jpg',         1),
    (18, 'Café gourmand bordelais',                'Trio mignardises : canelé, dune blanche, macaron, café expresso',                    'dessert', '/assets/img/plats/cafe-gourmand.jpg',        1),
    (19, 'Assortiment de fromages affinés',        'Plateau de fromages régionaux, confiture de cerises noires, pain au levain',         'dessert', '/assets/img/plats/fromages.jpg',             1),

    -- Boisson (20)
    (20, 'Sélection de vins de Bordeaux',          'Verre dégustation : Médoc, Saint-Émilion ou Pessac-Léognan selon menu',              'drink',   '/assets/img/plats/vins-bordeaux.jpg',        1);

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — MENUS (8 — couverture large des cas d'usage)
-- ════════════════════════════════════════════════════════════
INSERT INTO menu (id_menu, id_theme, titre, slug, description, prix_par_personne, nb_personnes_min, nb_personnes_max, stock_disponible, image_url, is_active)
VALUES
    (1, 4, 'Menu Élégance',
        'menu-elegance',
        'Notre menu signature pour mariages et grandes célébrations. Foie gras, entrecôte bordelaise et canelés revisités.',
        89.00, 50, 200, 10, '/assets/img/menus/menu-elegance.jpg', 1),

    (2, 4, 'Menu Prestige',
        'menu-prestige',
        'Le summum de la gastronomie bordelaise : filet Rossini, sauce périgueux et accord mets-vins d''exception.',
        125.00, 30, 100, 8, '/assets/img/menus/menu-prestige.jpg', 1),

    (3, 5, 'Buffet Corporate',
        'buffet-corporate',
        'Buffet professionnel raffiné pour vos séminaires et événements d''entreprise dans la métropole bordelaise.',
        45.00, 20, 200, 15, '/assets/img/menus/buffet-corporate.jpg', 1),

    (4, 3, 'Menu Festif',
        'menu-festif',
        'Menu convivial pour anniversaires et fêtes familiales. Magret de canard, fromages affinés, dunes blanches.',
        38.00, 15, 80, 20, '/assets/img/menus/menu-festif.jpg', 1),

    (5, 5, 'Cocktail Chic',
        'cocktail-chic',
        'Assortiment de bouchées raffinées pour vos cocktails dînatoires : huîtres, foie gras, mignardises.',
        32.00, 20, 150, 12, '/assets/img/menus/cocktail-chic.jpg', 1),

    (6, 3, 'Menu Terroir',
        'menu-terroir',
        'Produits du terroir aquitain : salade landaise, gigot d''agneau de Pauillac, plateau de fromages régionaux.',
        52.00, 25, 100, 10, '/assets/img/menus/menu-terroir.jpg', 1),

    (7, 5, 'Apéritif Dînatoire',
        'aperitif-dinatoire',
        'Formule apéritif complète : 8 bouchées par personne, idéal pour vos lancements produits et inaugurations.',
        42.00, 15, 100, 25, '/assets/img/menus/aperitif-dinatoire.jpg', 1),

    (8, 5, 'Pause Café Premium',
        'pause-cafe-premium',
        'Viennoiseries artisanales et boissons chaudes pour vos pauses entre deux sessions de séminaire.',
        12.00, 10, 100, 30, '/assets/img/menus/pause-cafe.jpg', 1);

-- ════════════════════════════════════════════════════════════
-- COMPOSITION DES MENUS (plats par menu)
-- ════════════════════════════════════════════════════════════
INSERT INTO composition_menu (id_menu, id_plat, quantite, ordre_affichage)
VALUES
    -- Menu 1 : Élégance
    (1, 3,  1, 1),  -- Foie gras
    (1, 8,  1, 2),  -- Entrecôte bordelaise
    (1, 19, 1, 3),  -- Fromages
    (1, 15, 2, 4),  -- Canelés
    (1, 20, 1, 5),  -- Vins de Bordeaux

    -- Menu 2 : Prestige
    (2, 3,  1, 1),  -- Foie gras
    (2, 10, 1, 2),  -- Filet Rossini
    (2, 19, 1, 3),  -- Fromages
    (2, 16, 1, 4),  -- Dunes blanches
    (2, 20, 1, 5),  -- Vins

    -- Menu 3 : Buffet Corporate
    (3, 4,  1, 1),  -- Saumon fumé
    (3, 9,  1, 2),  -- Magret canard
    (3, 18, 1, 3),  -- Café gourmand

    -- Menu 4 : Festif
    (4, 5,  1, 1),  -- Salade landaise
    (4, 9,  1, 2),  -- Magret canard
    (4, 16, 1, 3),  -- Dunes blanches

    -- Menu 5 : Cocktail Chic
    (5, 1,  3, 1),  -- Huîtres
    (5, 3,  1, 2),  -- Foie gras
    (5, 15, 2, 3),  -- Canelés

    -- Menu 6 : Terroir
    (6, 5,  1, 1),  -- Salade landaise
    (6, 11, 1, 2),  -- Gigot agneau
    (6, 19, 1, 3),  -- Fromages
    (6, 15, 1, 4),  -- Canelés

    -- Menu 7 : Apéritif Dînatoire
    (7, 1,  2, 1),  -- Huîtres
    (7, 7,  1, 2),  -- Tartare légumes
    (7, 18, 1, 3),  -- Café gourmand

    -- Menu 8 : Pause Café (mignardises)
    (8, 15, 2, 1),  -- Canelés
    (8, 16, 1, 2);  -- Dunes blanches

-- ════════════════════════════════════════════════════════════
-- LIAISONS PLAT ↔ ALLERGÈNE (Règlement UE n°1169/2011)
-- ════════════════════════════════════════════════════════════
INSERT INTO plat_allergene (id_plat, id_allergene)
VALUES
    -- Huîtres : mollusques + sulfites
    (1, 14), (1, 12),
    -- Velouté asperges : lait, fruits à coque
    (2, 7), (2, 8),
    -- Foie gras : gluten (toasts), sulfites
    (3, 1), (3, 12),
    -- Saumon fumé : poissons, lait (blinis), gluten, œufs
    (4, 4), (4, 7), (4, 1), (4, 3),
    -- Salade landaise : œufs, moutarde, sulfites
    (5, 3), (5, 10), (5, 12),
    -- Velouté potimarron : lait, fruits à coque
    (6, 7), (6, 8),
    -- Entrecôte bordelaise : sulfites (vin), lait (beurre)
    (8, 12), (8, 7),
    -- Magret : moutarde, sulfites
    (9, 10), (9, 12),
    -- Filet Rossini : gluten, lait, sulfites
    (10, 1), (10, 7), (10, 12),
    -- Gigot agneau : moutarde, sulfites
    (11, 10), (11, 12),
    -- Esturgeon : poissons, lait
    (12, 4), (12, 7),
    -- Wok tofu : soja, sésame, gluten (sauce)
    (13, 6), (13, 11), (13, 1),
    -- Risotto cèpes : lait
    (14, 7),
    -- Canelés : gluten, lait, œufs
    (15, 1), (15, 7), (15, 3),
    -- Dunes blanches : gluten, lait, œufs
    (16, 1), (16, 7), (16, 3),
    -- Tarte citron : gluten, lait, œufs
    (17, 1), (17, 7), (17, 3),
    -- Café gourmand : gluten, lait, œufs, fruits à coque
    (18, 1), (18, 7), (18, 3), (18, 8),
    -- Fromages : lait
    (19, 7),
    -- Vins : sulfites
    (20, 12);

-- ════════════════════════════════════════════════════════════
-- LIAISONS MENU ↔ RÉGIME
-- ════════════════════════════════════════════════════════════
INSERT INTO menu_regime (id_menu, id_regime)
VALUES
    (1, 1), (1, 5),                  -- Élégance : Classique, Halal
    (2, 1),                          -- Prestige : Classique
    (3, 1), (3, 2),                  -- Corporate : Classique, Végétarien (sur option)
    (4, 1),                          -- Festif : Classique
    (5, 1), (5, 2),                  -- Cocktail : Classique, Végé
    (6, 1),                          -- Terroir : Classique
    (7, 1), (7, 2), (7, 3),          -- Apéritif : 3 régimes
    (8, 1), (8, 2);                  -- Pause Café : Classique, Végé

-- ════════════════════════════════════════════════════════════
-- LIAISONS MENU ↔ BADGE ÉCO
-- ════════════════════════════════════════════════════════════
INSERT INTO menu_badge_eco (id_menu, id_badge)
VALUES
    (1, 1),                          -- Élégance : Local
    (2, 1),                          -- Prestige : Local
    (3, 1), (3, 2),                  -- Corporate : Local + Zéro déchet
    (4, 1),                          -- Festif : Local
    (5, 1),                          -- Cocktail : Local
    (6, 1),                          -- Terroir : Local
    (7, 1), (7, 2), (7, 3),          -- Apéritif : tout
    (8, 1), (8, 2);                  -- Pause Café : Local + Zéro déchet

-- ════════════════════════════════════════════════════════════
-- LIAISONS MENU ↔ CERTIFICATION
-- ════════════════════════════════════════════════════════════
INSERT INTO menu_certification (id_menu, id_certification)
VALUES
    (1, 2), (1, 3),                  -- Élégance : Label Rouge, BX Métropole
    (2, 2), (2, 3),                  -- Prestige : Label Rouge, BX Métropole
    (3, 1), (3, 3),                  -- Corporate : Bio, BX Métropole
    (6, 2), (6, 3);                  -- Terroir : Label Rouge, BX Métropole

-- ════════════════════════════════════════════════════════════
-- MODULE 2 — SERVICES (8)
-- ════════════════════════════════════════════════════════════
INSERT INTO service (id_service, nom, description, prix_unitaire, unite, is_active)
VALUES
    (1, 'Service à table',           'Serveurs professionnels (1 pour 25 convives)',         150.00, 'par serveur',  1),
    (2, 'Location vaisselle',         'Vaisselle complète : assiettes, verres, couverts',     8.00,   'par personne',  1),
    (3, 'Location mobilier',          'Tables rondes, chaises, nappes',                       12.00,  'par personne',  1),
    (4, 'Décoration florale',          'Compositions florales selon thème',                    25.00,  'par table',     1),
    (5, 'Accord mets-vins',           'Sélection de vins de Bordeaux par sommelier',          15.00,  'par personne',  1),
    (6, 'Animation musicale',         'DJ ou musicien live',                                  400.00, 'forfait',       1),
    (7, 'Livraison emballages éco',    'Vaisselle compostable et emballages biodégradables',   3.00,   'par personne',  1),
    (8, 'Brigade cuisine sur place',  'Chef et commis pour finition sur site',                250.00, 'par chef',      1);

-- ════════════════════════════════════════════════════════════
-- MODULE 3 — COMMANDES (3 — cohérence temporelle mai 2026)
-- ════════════════════════════════════════════════════════════
-- Règle énoncé page 7 : réduction 10% si nb_personnes >= (min+5)
--
-- C1 : PASSÉE — 10 mai 2026 — completed → AVIS POSSIBLE
-- C2 : FUTURE — 15 juin 2026 — confirmed → PAS d'avis
-- C3 : FUTURE PROCHE — 22 mai 2026 — in_preparation → PAS d'avis
-- ════════════════════════════════════════════════════════════
INSERT INTO commande (id_commande, id_utilisateur, date_evenement, adresse_livraison, code_postal_livraison, ville_livraison, distance_km, nb_personnes, sous_total, montant_remise, frais_livraison, total, statut, created_at)
VALUES
    -- C1 : Camille (anniversaire) → COMPLETED
    -- 20 personnes (min=15), réduction 10% car >= 15+5
    (1, 6, '2026-05-10 19:30:00',
       '22 Avenue Louis Barthou', '33200', 'Bordeaux-Caudéran', 3.5, 20,
       760.00, 76.00, 5.00, 689.00, 'completed', '2026-04-20 14:30:00'),

    -- C2 : Sophie (mariage Médoc) → CONFIRMED (future)
    -- 60 personnes (min=50), réduction 10% car >= 50+5+5
    (2, 4, '2026-06-15 12:00:00',
       'Château du Taillan', '33320', 'Le Taillan-Médoc', 14.2, 60,
       5340.00, 534.00, 13.36, 4819.36, 'confirmed', '2026-05-02 09:15:00'),

    -- C3 : Antoine (séminaire entreprise) → IN_PREPARATION (future proche)
    -- 50 personnes (min=20), réduction 10% car >= 20+5
    (3, 5, '2026-05-22 12:00:00',
       '8 Quai de Bacalan', '33300', 'Bordeaux', 0.0, 50,
       2250.00, 225.00, 0.00, 2025.00, 'in_preparation', '2026-05-08 11:05:00');

-- ════════════════════════════════════════════════════════════
-- LIGNES DE COMMANDE
-- ════════════════════════════════════════════════════════════
INSERT INTO ligne_commande (id_commande, id_menu, quantite, prix_unitaire, sous_total, notes)
VALUES
    (1, 4, 20, 38.00, 760.00,  'Anniversaire 40 ans — décoration thème jardin'),
    (2, 1, 60, 89.00, 5340.00, 'Mariage — accord mets-vins compris, vaisselle haut de gamme'),
    (3, 3, 50, 45.00, 2250.00, 'Séminaire — emballages compostables (charte RSE)');

-- ════════════════════════════════════════════════════════════
-- MODULE 4 — AVIS (1 seul — uniquement commande terminée)
-- ════════════════════════════════════════════════════════════
-- Énoncé page 7 : "Quand la commande est terminée, l'utilisateur
-- est notifié par mail qu'il peut donner son avis"
INSERT INTO avis (id_utilisateur, id_commande, note, commentaire, statut, id_moderateur, moderated_at, created_at)
VALUES
    (6, 1, 5,
     'Anniversaire parfait ! Les dunes blanches ont fait fureur et le magret était délicieux. Service impeccable, équipe à l''écoute. Je recommande sans hésiter pour vos événements !',
     'approved', 2, '2026-05-12 10:00:00', '2026-05-11 16:30:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 5 — OFFRES ANTI-GASPI (2)
-- ════════════════════════════════════════════════════════════
INSERT INTO offre_anti_gaspi (id_offre, id_menu, titre, description, prix_original, prix_anti_gaspi, quantite_disponible, date_limite, is_active, created_at)
VALUES
    (1, 8, 'Pause Café -50%',
     'Surplus de viennoiseries de notre dernier événement. À récupérer en boutique avant 18h.',
     12.00, 6.00, 15, '2026-05-19 18:00:00', 1, '2026-05-17 09:00:00'),

    (2, 5, 'Cocktail Chic -40%',
     'Préparations excédentaires d''un séminaire annulé. Idéal pour apéro dînatoire improvisé.',
     32.00, 19.20, 8, '2026-05-20 20:00:00', 1, '2026-05-17 11:30:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 5 — ABONNEMENTS ALERTES (3)
-- ════════════════════════════════════════════════════════════
INSERT INTO abonnement_alerte (id_utilisateur, type_alerte, is_active, created_at)
VALUES
    (4, 'anti_gaspi', 1, '2026-01-15 10:00:00'),
    (5, 'anti_gaspi', 1, '2026-02-20 14:30:00'),
    (6, 'new_menu',   1, '2026-03-10 09:15:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 5 — PARTENAIRES LOCAUX (3)
-- ════════════════════════════════════════════════════════════
INSERT INTO partenaire (id_partenaire, nom, description, type_partenariat, ville, distance_km, logo_url, site_web, is_active)
VALUES
    (1, 'Ferme du Médoc',
     'Producteur de viandes de Bazas et agneau de Pauillac en circuit court',
     'fournisseur', 'Pauillac', 50.0, '/assets/img/partenaires/ferme-medoc.svg', 'https://fermedumedoc.fr', 1),

    (2, 'Ostréiculteurs du Cap Ferret',
     'Huîtres fraîches du Bassin d''Arcachon, livraison quotidienne',
     'fournisseur', 'Cap Ferret', 65.0, '/assets/img/partenaires/cap-ferret.svg', 'https://huitres-cap-ferret.fr', 1),

    (3, 'Maraîchers du Blayais',
     'Légumes bio de saison, asperges blanches et carottes de Macau',
     'fournisseur', 'Blaye', 45.0, '/assets/img/partenaires/blayais.svg', 'https://maraichers-blayais.fr', 1);

-- ════════════════════════════════════════════════════════════
-- MODULE 6 — MEMBRES ÉQUIPE (4)
-- ════════════════════════════════════════════════════════════
INSERT INTO membre_equipe (id_membre, nom, prenom, poste, bio, photo_url, ordre_affichage, is_active)
VALUES
    (1, 'Carrère',    'José',    'Chef cuisinier & cofondateur',
     '25 ans d''expérience en gastronomie bordelaise. Passionné de produits locaux et de saison.',
     '/assets/img/equipe/jose.jpg', 1, 1),

    (2, 'Lartigue',   'Julie',   'Directrice & cofondatrice',
     'Œnologue de formation, Julie sélectionne avec passion les accords mets-vins pour chaque événement.',
     '/assets/img/equipe/julie.jpg', 2, 1),

    (3, 'Dubreuilh',  'Marc',    'Second de cuisine',
     'Chef pâtissier formé chez les Compagnons du Tour de France, expert des classiques bordelais.',
     '/assets/img/equipe/marc.jpg', 3, 1),

    (4, 'Bordeau',    'Léa',     'Responsable événementiel',
     'Coordonne tous vos événements de A à Z. Spécialiste des mariages et grandes réceptions.',
     '/assets/img/equipe/lea.jpg', 4, 1);

-- ════════════════════════════════════════════════════════════
-- MODULE 6 — HORAIRES (7 jours — énoncé page 4 "pied de page")
-- ════════════════════════════════════════════════════════════
INSERT INTO horaires_ouverture (id_horaire, jour_semaine, heure_ouverture, heure_fermeture, is_ferme, date_speciale, notes)
VALUES
    (1, 'monday',    '09:00:00', '18:00:00', 0, NULL, NULL),
    (2, 'tuesday',   '09:00:00', '18:00:00', 0, NULL, NULL),
    (3, 'wednesday', '09:00:00', '18:00:00', 0, NULL, NULL),
    (4, 'thursday',  '09:00:00', '18:00:00', 0, NULL, NULL),
    (5, 'friday',    '09:00:00', '19:00:00', 0, NULL, 'Fermeture tardive le vendredi'),
    (6, 'saturday',  '10:00:00', '16:00:00', 0, NULL, NULL),
    (7, 'sunday',    NULL,        NULL,       1, NULL, 'Fermé — Repos hebdomadaire');

-- ════════════════════════════════════════════════════════════
-- MODULE 6 — CONTENU RSE (3 sections)
-- ════════════════════════════════════════════════════════════
INSERT INTO contenu_rse (id_contenu, cle_section, titre, contenu, image_url, ordre_affichage, id_utilisateur_maj)
VALUES
    (1, 'intro', 'Notre engagement éco-responsable',
     'Depuis 1999, Vite & Gourmand s''engage pour une gastronomie durable. Nous travaillons exclusivement avec des producteurs girondins en circuit court, à moins de 100 km de notre cuisine bordelaise.',
     '/assets/img/rse/engagement.jpg', 1, 1),

    (2, 'zero_dechet', 'Démarche zéro déchet',
     'Vaisselle compostable certifiée, emballages biodégradables, livraisons en vélos-cargos pour le centre de Bordeaux. Nos invendus sont redistribués via notre offre anti-gaspi.',
     '/assets/img/rse/zero-dechet.jpg', 2, 1),

    (3, 'partenaires', 'Nos producteurs partenaires',
     'Ferme du Médoc, ostréiculteurs du Cap Ferret, maraîchers du Blayais : nous soutenons l''économie locale girondine au quotidien.',
     '/assets/img/rse/partenaires.jpg', 3, 1);

-- ════════════════════════════════════════════════════════════
-- MODULE 7 — HISTORIQUE STATUTS (cycle complet sans saut)
-- ════════════════════════════════════════════════════════════
INSERT INTO historique_statut (id_commande, statut_precedent, nouveau_statut, id_utilisateur, motif, created_at)
VALUES
    -- C1 : Cycle COMPLET (pending → confirmed → in_preparation → in_delivery → completed)
    (1, 'pending',        'confirmed',      2, 'Acompte de 30% validé',                            '2026-04-20 15:00:00'),
    (1, 'confirmed',      'in_preparation', 3, 'Lancement préparation cuisine',                    '2026-05-09 08:00:00'),
    (1, 'in_preparation', 'in_delivery',    2, 'Chargement caissons isothermes, départ livraison', '2026-05-10 17:30:00'),
    (1, 'in_delivery',    'completed',      2, 'Livraison effectuée en vélo-cargo, client ravi',   '2026-05-10 19:00:00'),

    -- C2 : pending → confirmed (future)
    (2, 'pending',        'confirmed',      2, 'Date bloquée dans l''agenda, devis accepté',       '2026-05-02 10:00:00'),

    -- C3 : pending → confirmed → in_preparation (future proche)
    (3, 'pending',        'confirmed',      2, 'Validation charte zéro déchet pour séminaire',    '2026-05-08 14:00:00'),
    (3, 'confirmed',      'in_preparation', 3, 'Réception légumes Blayais et viandes Bazas',       '2026-05-17 16:30:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 7 — MESSAGES CONTACT (2 exemples)
-- ════════════════════════════════════════════════════════════
INSERT INTO message_contact (id_message, nom_expediteur, email_expediteur, telephone_expediteur, sujet, message, statut, id_assigne, created_at)
VALUES
    (1, 'Mathilde Lasserre', 'mathilde.lasserre@gmail.com', '0698765432',
     'Demande de devis mariage',
     'Bonjour, je souhaite organiser mon mariage pour 80 personnes le 12 septembre 2026 au Château Pape-Clément. Pouvez-vous me faire un devis pour votre menu Élégance ?',
     'new', 2, '2026-05-15 14:22:00'),

    (2, 'Restaurant Le Quatrième', 'contact@le-quatrieme.fr', '0556789012',
     'Partenariat traiteur événementiel',
     'Nous recherchons un partenaire traiteur pour nos événements privés. Possibilité de rendez-vous la semaine prochaine ?',
     'in_progress', 1, '2026-05-12 09:45:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 7 — LOGS EMAIL (3 traces)
-- ════════════════════════════════════════════════════════════
INSERT INTO log_email (id_log, destinataire, sujet, template, statut, type_entite, id_entite, envoye_a, created_at)
VALUES
    (1, 'camille.duboscq@gmail.com', 'Confirmation commande #1', 'order_confirmation',  'sent', 'commande', 1, '2026-04-20 15:01:00', '2026-04-20 15:01:00'),
    (2, 'sophie.lacaze@gmail.com',   'Confirmation commande #2', 'order_confirmation',  'sent', 'commande', 2, '2026-05-02 10:01:00', '2026-05-02 10:01:00'),
    (3, 'camille.duboscq@gmail.com', 'Donnez votre avis',        'review_request',      'sent', 'commande', 1, '2026-05-11 09:00:00', '2026-05-11 09:00:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 7 — NOTIFICATIONS (3)
-- ════════════════════════════════════════════════════════════
INSERT INTO notification (id_notification, id_utilisateur, type, titre, message, lien_url, is_read, created_at)
VALUES
    (1, 6, 'order_status', 'Commande terminée',     'Votre commande du 10 mai a été livrée avec succès. Donnez-nous votre avis !', '/mes-commandes/1', 1, '2026-05-10 19:05:00'),
    (2, 4, 'order_status', 'Commande confirmée',    'Votre mariage du 15 juin est confirmé. Préparation à venir.',                  '/mes-commandes/2', 0, '2026-05-02 10:02:00'),
    (3, 5, 'order_status', 'Préparation en cours',  'Votre séminaire du 22 mai est en préparation par notre équipe.',               '/mes-commandes/3', 0, '2026-05-17 16:35:00');

-- ════════════════════════════════════════════════════════════
-- MODULE 7 — LOG AUDIT (3 traces sensibles)
-- ════════════════════════════════════════════════════════════
INSERT INTO log_audit (id_audit, id_utilisateur, action, type_entite, id_entite, anciennes_valeurs, nouvelles_valeurs, ip_address, user_agent, created_at)
VALUES
    (1, 1, 'create', 'utilisateur', 3, NULL, '{"prenom":"Marc","role":"employee"}',          '127.0.0.1', 'Mozilla/5.0', '2025-02-01 10:00:00'),
    (2, 2, 'update', 'commande',    1, '{"statut":"pending"}', '{"statut":"confirmed"}',     '127.0.0.1', 'Mozilla/5.0', '2026-04-20 15:00:00'),
    (3, 2, 'update', 'avis',        1, '{"statut":"pending"}', '{"statut":"approved"}',      '127.0.0.1', 'Mozilla/5.0', '2026-05-12 10:00:00');

-- ════════════════════════════════════════════════════════════
-- RÉSUMÉ INSERTIONS
-- ════════════════════════════════════════════════════════════
-- 6 utilisateurs (1 admin, 2 employés, 3 clients)
-- 5 thèmes / 5 régimes / 14 allergènes (UE) / 3 badges éco / 3 certifications
-- 20 plats / 8 menus / 8 services
-- 30 compositions menu↔plat / 40 liaisons plat↔allergène
-- 14 menus_regime / 13 menus_badge_eco / 7 menus_certification
-- 3 commandes (1 completed, 1 confirmed, 1 in_preparation) avec réduction 10%
-- 4 lignes commande / 1 avis (sur commande complétée uniquement)
-- 2 offres anti-gaspi / 3 abonnements alerte / 3 partenaires
-- 4 membres équipe (Julie, José, Marc, Léa) / 7 horaires / 3 contenus RSE
-- 7 historiques statuts / 2 messages contact / 3 logs email / 3 notifications / 3 logs audit
-- ════════════════════════════════════════════════════════════
