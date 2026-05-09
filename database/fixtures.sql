-- ============================================================
-- Vite & Gourmand — Test Data (Fixtures)
-- Version: 1.0.0
-- Purpose: Realistic test data for development and demonstration
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
-- Clear existing data (cascade will handle related records)
TRUNCATE TABLE log_audit;
TRUNCATE TABLE notification;
TRUNCATE TABLE log_email;
TRUNCATE TABLE message_contact;
TRUNCATE TABLE historique_statut;
TRUNCATE TABLE abonnement_alerte;
TRUNCATE TABLE avis;
TRUNCATE TABLE ligne_commande;
TRUNCATE TABLE commande;
TRUNCATE TABLE cache_distance;
TRUNCATE TABLE menu_certification;
TRUNCATE TABLE menu_badge_eco;
TRUNCATE TABLE menu_regime;
TRUNCATE TABLE composition_menu;
TRUNCATE TABLE plat_allergene;
TRUNCATE TABLE offre_anti_gaspi;
TRUNCATE TABLE service;
TRUNCATE TABLE plat;
TRUNCATE TABLE menu;
TRUNCATE TABLE badge_eco;
TRUNCATE TABLE certification;
TRUNCATE TABLE partenaire;
TRUNCATE TABLE regime;
TRUNCATE TABLE theme;
TRUNCATE TABLE allergene;
TRUNCATE TABLE contenu_rse;
TRUNCATE TABLE horaires_ouverture;
TRUNCATE TABLE membre_equipe;
TRUNCATE TABLE token_mot_de_passe;
TRUNCATE TABLE session;
TRUNCATE TABLE utilisateur;
-- ============================================================
-- MODULE 1 — USERS
-- ============================================================
-- Users (password: "password123" hashed with bcrypt cost 10)
INSERT INTO utilisateur (
        email,
        mot_de_passe,
        nom,
        prenom,
        telephone,
        adresse,
        code_postal,
        ville,
        role,
        is_active,
        email_verified_at
    )
VALUES (
        'admin@viteetgourmand.fr',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Dubois',
        'Marie',
        '0556123456',
        '12 Rue de la Victoire',
        '33000',
        'Bordeaux',
        'admin',
        1,
        NOW()
    ),
    (
        'julien.martin@viteetgourmand.fr',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Martin',
        'Julien',
        '0556234567',
        '8 Avenue des Chartrons',
        '33000',
        'Bordeaux',
        'employee',
        1,
        NOW()
    ),
    (
        'sophie.bernard@email.fr',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Bernard',
        'Sophie',
        '0612345678',
        '45 Cours de l\'Intendance',
        '33000',
        'Bordeaux',
        'client',
        1,
        NOW()
    ),
    (
        'thomas.petit@email.fr',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Petit',
        'Thomas',
        '0623456789',
        '23 Rue Sainte-Catherine',
        '33000',
        'Bordeaux',
        'client',
        1,
        NOW()
    ),
    (
        'claire.leroy@email.fr',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Leroy',
        'Claire',
        '0634567890',
        '10 Place de la Bourse',
        '33300',
        'Bordeaux',
        'client',
        1,
        NOW()
    );
-- ============================================================
-- MODULE 2 — CATALOG
-- ============================================================
-- Themes
INSERT INTO theme (nom, description, couleur)
VALUES (
        'Mariage',
        'Célébrations de mariage élégantes et raffinées',
        '#D4AF37'
    ),
    (
        'Entreprise',
        'Événements corporatifs et séminaires professionnels',
        '#063A1F'
    ),
    (
        'Anniversaire',
        'Fêtes d\'anniversaire conviviales et festives',
        '#800020'
    ),
    (
        'Cocktail',
        'Réceptions cocktail chic et décontractées',
        '#E8C547'
    ),
    (
        'Champêtre',
        'Événements rustiques et champêtres en plein air',
        '#8B7355'
    );
-- Allergens (EU standard 14)
INSERT INTO allergene (nom, icone)
VALUES ('Gluten', '🌾'),
    ('Crustacés', '🦐'),
    ('Œufs', '🥚'),
    ('Poissons', '🐟'),
    ('Arachides', '🥜'),
    ('Soja', '🫘'),
    ('Lait', '🥛'),
    ('Fruits à coque', '🌰'),
    ('Céleri', '🥬'),
    ('Moutarde', '🟡'),
    ('Graines de sésame', '⚪'),
    ('Sulfites', '🍷'),
    ('Lupin', '🫘'),
    ('Mollusques', '🦪');
-- Dietary regimes
INSERT INTO regime (nom, description)
VALUES (
        'Végétarien',
        'Sans viande ni poisson, mais avec produits laitiers et œufs'
    ),
    ('Vegan', 'Sans aucun produit d\'origine animale'),
    (
        'Sans gluten',
        'Sans blé, orge, seigle et leurs dérivés'
    ),
    (
        'Halal',
        'Conforme aux prescriptions alimentaires islamiques'
    ),
    (
        'Sans lactose',
        'Sans produits laitiers contenant du lactose'
    );
-- Eco badges
INSERT INTO badge_eco (nom, description, icone, couleur)
VALUES (
        'Circuit court',
        'Produits locaux à moins de 100km',
        '🌍',
        '#2E7D32'
    ),
    (
        'Agriculture biologique',
        'Produits certifiés bio',
        '🌱',
        '#66BB6A'
    ),
    (
        'Zéro déchet',
        'Emballages réutilisables et compostables',
        '♻️',
        '#1B5E20'
    );
-- Certifications
INSERT INTO certification (
        nom,
        description,
        logo_url,
        organisme,
        date_validite,
        is_active
    )
VALUES (
        'Agriculture Biologique',
        'Certification AB française',
        '/assets/certif/ab.png',
        'Agence Bio',
        '2026-12-31',
        1
    ),
    (
        'Ecocert',
        'Label environnemental Ecocert',
        '/assets/certif/ecocert.png',
        'Ecocert France',
        '2027-06-30',
        1
    ),
    (
        'Label Rouge',
        'Qualité supérieure des produits',
        '/assets/certif/label-rouge.png',
        'INAO',
        '2026-12-31',
        1
    );
-- Menus
INSERT INTO menu (
        id_theme,
        titre,
        description,
        prix_par_personne,
        nb_personnes_min,
        nb_personnes_max,
        image_url,
        is_active
    )
VALUES (
        1,
        'Menu Élégance',
        'Menu raffiné pour célébrations de mariage',
        89.00,
        50,
        150,
        '/assets/menus/elegance.jpg',
        1
    ),
    (
        1,
        'Menu Prestige',
        'Menu gastronomique haut de gamme',
        125.00,
        30,
        100,
        '/assets/menus/prestige.jpg',
        1
    ),
    (
        2,
        'Buffet Corporate',
        'Buffet varié pour événements d\'entreprise',
        45.00,
        20,
        200,
        '/assets/menus/corporate.jpg',
        1
    ),
    (
        3,
        'Menu Festif',
        'Menu convivial pour fêtes d\'anniversaire',
        38.00,
        15,
        80,
        '/assets/menus/festif.jpg',
        1
    ),
    (
        4,
        'Cocktail Chic',
        'Assortiment de bouchées raffinées',
        32.00,
        20,
        150,
        '/assets/menus/cocktail.jpg',
        1
    ),
    (
        5,
        'Menu Terroir',
        'Produits du terroir et recettes traditionnelles',
        52.00,
        25,
        100,
        '/assets/menus/terroir.jpg',
        1
    ),
    (
        4,
        'Apéritif Dinatoire',
        'Formule apéritive complète',
        42.00,
        15,
        100,
        '/assets/menus/aperitif.jpg',
        1
    ),
    (
        2,
        'Pause Café Premium',
        'Viennoiseries et boissons chaudes',
        12.00,
        10,
        100,
        '/assets/menus/pause-cafe.jpg',
        1
    );
-- Dishes
INSERT INTO plat (
        nom,
        description,
        categorie,
        image_url,
        is_active
    )
VALUES -- Starters
    (
        'Foie gras mi-cuit',
        'Foie gras maison, chutney de figues',
        'starter',
        '/assets/plats/foie-gras.jpg',
        1
    ),
    (
        'Velouté de cèpes',
        'Crème de champignons forestiers',
        'starter',
        '/assets/plats/veloute.jpg',
        1
    ),
    (
        'Tartare de saumon',
        'Saumon frais, avocat et citron vert',
        'starter',
        '/assets/plats/tartare.jpg',
        1
    ),
    (
        'Salade de chèvre chaud',
        'Mesclun, toasts de chèvre, miel',
        'starter',
        '/assets/plats/chevre.jpg',
        1
    ),
    -- Mains
    (
        'Magret de canard',
        'Magret rôti, sauce aux fruits rouges',
        'main',
        '/assets/plats/magret.jpg',
        1
    ),
    (
        'Pavé de bœuf',
        'Bœuf du Limousin, sauce au poivre',
        'main',
        '/assets/plats/boeuf.jpg',
        1
    ),
    (
        'Bar en croûte de sel',
        'Bar entier, légumes de saison',
        'main',
        '/assets/plats/bar.jpg',
        1
    ),
    (
        'Risotto aux champignons',
        'Risotto crémeux, cèpes et parmesan',
        'main',
        '/assets/plats/risotto.jpg',
        1
    ),
    (
        'Tajine végétarien',
        'Légumes du soleil, épices douces',
        'main',
        '/assets/plats/tajine.jpg',
        1
    ),
    -- Desserts
    (
        'Tiramisu maison',
        'Recette traditionnelle italienne',
        'dessert',
        '/assets/plats/tiramisu.jpg',
        1
    ),
    (
        'Tarte au citron meringuée',
        'Pâte sablée, crème citron, meringue',
        'dessert',
        '/assets/plats/tarte-citron.jpg',
        1
    ),
    (
        'Fondant au chocolat',
        'Cœur coulant, glace vanille',
        'dessert',
        '/assets/plats/fondant.jpg',
        1
    ),
    (
        'Crème brûlée',
        'Vanille Bourbon, sucre caramélisé',
        'dessert',
        '/assets/plats/creme-brulee.jpg',
        1
    ),
    -- Drinks
    (
        'Café gourmand',
        'Expresso, trio de mignardises',
        'drink',
        '/assets/plats/cafe.jpg',
        1
    ),
    (
        'Vin rouge Bordeaux',
        'Sélection du domaine',
        'drink',
        '/assets/plats/vin-rouge.jpg',
        1
    ),
    (
        'Champagne',
        'Cuvée prestige',
        'drink',
        '/assets/plats/champagne.jpg',
        1
    ),
    -- Others
    (
        'Assortiment de fromages',
        'Plateau de fromages régionaux',
        'other',
        '/assets/plats/fromages.jpg',
        1
    ),
    (
        'Mignardises',
        'Petits fours sucrés variés',
        'other',
        '/assets/plats/mignardises.jpg',
        1
    ),
    (
        'Pain artisanal',
        'Baguette tradition et pain de campagne',
        'other',
        '/assets/plats/pain.jpg',
        1
    ),
    (
        'Mini-burgers gourmets',
        'Burgers gastronomiques individuels',
        'other',
        '/assets/plats/burgers.jpg',
        1
    );
-- Services
INSERT INTO service (
        nom,
        description,
        categorie,
        prix_base,
        unite_tarification,
        is_active
    )
VALUES (
        'Serveur professionnel',
        'Service à table par personnel qualifié',
        'staff',
        180.00,
        'per_unit',
        1
    ),
    (
        'Chef à domicile',
        'Chef cuisinier sur place',
        'staff',
        350.00,
        'per_unit',
        1
    ),
    (
        'Location de vaisselle',
        'Assiettes, couverts, verres pour 10 personnes',
        'equipment',
        45.00,
        'per_unit',
        1
    ),
    (
        'Nappage lin',
        'Nappes et serviettes en lin',
        'equipment',
        8.00,
        'per_person',
        1
    ),
    (
        'Bar à cocktails',
        'Animation bartender et cocktails',
        'drinks',
        450.00,
        'flat_rate',
        1
    ),
    (
        'Vin au verre',
        'Sélection de vins régionaux',
        'drinks',
        6.50,
        'per_person',
        1
    ),
    (
        'Livraison et installation',
        'Transport et mise en place complète',
        'supplement',
        120.00,
        'flat_rate',
        1
    ),
    (
        'Débarrassage',
        'Nettoyage et débarrassage fin de soirée',
        'supplement',
        85.00,
        'flat_rate',
        1
    );
-- Menu compositions
INSERT INTO composition_menu (id_menu, id_plat, quantite, ordre_affichage)
VALUES -- Menu Élégance (id=1)
    (1, 1, 1, 1),
    -- Foie gras
    (1, 5, 1, 2),
    -- Magret
    (1, 17, 1, 3),
    -- Fromages
    (1, 11, 1, 4),
    -- Tarte citron
    (1, 14, 1, 5),
    -- Café gourmand
    -- Menu Prestige (id=2)
    (2, 3, 1, 1),
    -- Tartare saumon
    (2, 7, 1, 2),
    -- Bar
    (2, 17, 1, 3),
    -- Fromages
    (2, 12, 1, 4),
    -- Fondant chocolat
    (2, 16, 1, 5),
    -- Champagne
    -- Buffet Corporate (id=3)
    (3, 4, 1, 1),
    -- Salade chèvre
    (3, 6, 1, 2),
    -- Pavé bœuf
    (3, 8, 1, 3),
    -- Risotto
    (3, 10, 1, 4),
    -- Tiramisu
    -- Menu Festif (id=4)
    (4, 2, 1, 1),
    -- Velouté
    (4, 9, 1, 2),
    -- Tajine végé
    (4, 13, 1, 3),
    -- Crème brûlée
    -- Cocktail Chic (id=5)
    (5, 3, 2, 1),
    -- Tartare (double)
    (5, 20, 3, 2),
    -- Mini-burgers
    (5, 18, 1, 3),
    -- Mignardises
    -- Menu Terroir (id=6)
    (6, 1, 1, 1),
    -- Foie gras
    (6, 5, 1, 2),
    -- Magret
    (6, 17, 1, 3),
    -- Fromages
    (6, 11, 1, 4);
-- Tarte citron
-- Allergens mapping (sample)
INSERT INTO plat_allergene (id_plat, id_allergene)
VALUES (1, 3),
    -- Foie gras - Œufs
    (3, 4),
    -- Tartare saumon - Poissons
    (4, 1),
    -- Chèvre chaud - Gluten
    (4, 7),
    -- Chèvre chaud - Lait
    (8, 7),
    -- Risotto - Lait
    (10, 1),
    -- Tiramisu - Gluten
    (10, 3),
    -- Tiramisu - Œufs
    (10, 7),
    -- Tiramisu - Lait
    (11, 1),
    -- Tarte citron - Gluten
    (11, 3),
    -- Tarte citron - Œufs
    (12, 1),
    -- Fondant - Gluten
    (12, 3),
    -- Fondant - Œufs
    (12, 7);
-- Fondant - Lait
-- Menu regimes
INSERT INTO menu_regime (id_menu, id_regime)
VALUES (4, 1),
    -- Menu Festif - Végétarien
    (6, 4);
-- Menu Terroir - Halal
-- Menu eco badges
INSERT INTO menu_badge_eco (id_menu, id_badge)
VALUES (6, 1),
    -- Menu Terroir - Circuit court
    (6, 2),
    -- Menu Terroir - Bio
    (4, 3);
-- Menu Festif - Zéro déchet
-- Menu certifications
INSERT INTO menu_certification (id_menu, id_certification)
VALUES (6, 1),
    -- Menu Terroir - AB
    (6, 3),
    -- Menu Terroir - Label Rouge
    (2, 2);
-- Menu Prestige - Ecocert
-- ============================================================
-- MODULE 5 — ECO-RESPONSIBLE (before orders for FK)
-- ============================================================
-- Anti-waste offers
INSERT INTO offre_anti_gaspi (
        id_menu,
        titre,
        description,
        prix_original,
        prix_remise,
        quantite_disponible,
        disponible_jusqua,
        is_active
    )
VALUES (
        4,
        'Menu Festif invendu',
        'Menu complet pour 10 personnes, préparé hier',
        380.00,
        250.00,
        1,
        DATE_ADD(NOW(), INTERVAL 12 HOUR),
        1
    ),
    (
        5,
        'Cocktail Chic surplus',
        'Assortiment de bouchées pour 20 personnes',
        640.00,
        420.00,
        2,
        DATE_ADD(NOW(), INTERVAL 18 HOUR),
        1
    );
-- Partners
INSERT INTO partenaire (
        nom,
        description,
        logo_url,
        site_web,
        categorie,
        is_active
    )
VALUES (
        'Ferme Bio du Médoc',
        'Producteur de légumes biologiques',
        '/assets/partners/ferme-medoc.png',
        'https://ferme-medoc.fr',
        'supplier',
        1
    ),
    (
        'Fromagerie Girondine',
        'Fromages artisanaux locaux',
        '/assets/partners/fromagerie.png',
        'https://fromagerie-girondine.fr',
        'supplier',
        1
    ),
    (
        'Ecocert Aquitaine',
        'Organisme de certification bio',
        '/assets/partners/ecocert.png',
        'https://ecocert.fr',
        'certifier',
        1
    );
-- ============================================================
-- MODULE 3 — ORDERS
-- ============================================================
-- Distance cache
INSERT INTO cache_distance (ville, distance_km, cached_at, expires_at)
VALUES (
        'Mérignac',
        8.5,
        NOW(),
        DATE_ADD(NOW(), INTERVAL 90 DAY)
    ),
    (
        'Pessac',
        12.3,
        NOW(),
        DATE_ADD(NOW(), INTERVAL 90 DAY)
    ),
    (
        'Talence',
        6.7,
        NOW(),
        DATE_ADD(NOW(), INTERVAL 90 DAY)
    ),
    (
        'Bègles',
        5.2,
        NOW(),
        DATE_ADD(NOW(), INTERVAL 90 DAY)
    ),
    (
        'Cenon',
        7.8,
        NOW(),
        DATE_ADD(NOW(), INTERVAL 90 DAY)
    );
-- Orders
INSERT INTO commande (
        id_utilisateur,
        date_evenement,
        adresse_livraison,
        code_postal_livraison,
        ville_livraison,
        distance_km,
        nb_personnes,
        sous_total,
        montant_remise,
        frais_livraison,
        total,
        statut,
        created_at
    )
VALUES (
        3,
        '2026-06-15 19:00:00',
        '12 Avenue Thiers',
        '33100',
        'Bordeaux',
        5.2,
        50,
        4450.00,
        0.00,
        120.00,
        4570.00,
        'confirmed',
        DATE_SUB(NOW(), INTERVAL 5 DAY)
    ),
    (
        4,
        '2026-07-20 12:00:00',
        '45 Rue Judaïque',
        '33000',
        'Bordeaux',
        0.00,
        80,
        3600.00,
        0.00,
        0.00,
        3600.00,
        'pending',
        DATE_SUB(NOW(), INTERVAL 2 DAY)
    ),
    (
        5,
        '2026-05-01 20:00:00',
        '8 Cours de Verdun',
        '33000',
        'Bordeaux',
        0.00,
        30,
        3750.00,
        0.00,
        0.00,
        3750.00,
        'completed',
        DATE_SUB(NOW(), INTERVAL 10 DAY)
    );
-- Order lines
INSERT INTO ligne_commande (
        id_commande,
        id_menu,
        id_service,
        id_offre,
        quantite,
        duree_heures,
        prix_unitaire,
        sous_total,
        notes
    )
VALUES -- Order 1 (confirmed)
    (
        1,
        1,
        NULL,
        NULL,
        50,
        NULL,
        89.00,
        4450.00,
        'Menu élégance pour mariage'
    ),
    (
        1,
        NULL,
        7,
        NULL,
        1,
        NULL,
        120.00,
        120.00,
        'Livraison à Bègles'
    ),
    -- Order 2 (pending)
    (
        2,
        3,
        NULL,
        NULL,
        80,
        NULL,
        45.00,
        3600.00,
        'Buffet séminaire entreprise'
    ),
    -- Order 3 (completed)
    (
        3,
        2,
        NULL,
        NULL,
        30,
        NULL,
        125.00,
        3750.00,
        'Menu prestige anniversaire'
    );
-- ============================================================
-- MODULE 4 — REVIEWS
-- ============================================================
INSERT INTO avis (
        id_utilisateur,
        id_commande,
        note,
        commentaire,
        statut,
        id_moderateur,
        moderated_at,
        created_at
    )
VALUES (
        3,
        1,
        5,
        'Prestation parfaite pour notre mariage ! Les invités ont adoré le menu Élégance. Service impeccable.',
        'approved',
        2,
        DATE_SUB(NOW(), INTERVAL 3 DAY),
        DATE_SUB(NOW(), INTERVAL 4 DAY)
    ),
    (
        5,
        3,
        4,
        'Très bonne qualité, présentation soignée. Juste un petit retard dans la livraison mais rien de grave.',
        'approved',
        2,
        DATE_SUB(NOW(), INTERVAL 8 DAY),
        DATE_SUB(NOW(), INTERVAL 9 DAY)
    ),
    (
        4,
        2,
        5,
        'En attente de l\'événement mais la préparation semble au top !',
        'pending',
        NULL,
        NULL,
        DATE_SUB(NOW(), INTERVAL 1 DAY)
    );
-- ============================================================
-- MODULE 5 — ECO (continued)
-- ============================================================
-- Alert subscriptions
INSERT INTO abonnement_alerte (id_utilisateur, type_alerte, is_active)
VALUES (3, 'anti_waste', 1),
    (4, 'new_menu', 1),
    (5, 'anti_waste', 1);
-- ============================================================
-- MODULE 6 — SYSTEM
-- ============================================================
-- Team members
INSERT INTO membre_equipe (
        nom,
        prenom,
        poste,
        bio,
        photo_url,
        ordre_affichage,
        is_active
    )
VALUES (
        'Dubois',
        'Marie',
        'Chef de cuisine',
        'Passionnée de gastronomie depuis 15 ans, formée aux Beaux-Arts Culinaires de Bordeaux.',
        '/assets/team/marie.jpg',
        1,
        1
    ),
    (
        'Martin',
        'Julien',
        'Responsable événementiel',
        'Expert en organisation d\'événements d\'entreprise et célébrations privées.',
        '/assets/team/julien.jpg',
        2,
        1
    ),
    (
        'Rousseau',
        'Camille',
        'Pâtissière',
        'Spécialisée dans les desserts raffinés et wedding cakes sur mesure.',
        '/assets/team/camille.jpg',
        3,
        1
    ),
    (
        'Laurent',
        'Éric',
        'Sommelier',
        'Sélectionne les meilleurs vins de Bordeaux pour accompagner nos menus.',
        '/assets/team/eric.jpg',
        4,
        1
    );
-- Opening hours
INSERT INTO horaires_ouverture (
        jour_semaine,
        heure_ouverture,
        heure_fermeture,
        is_ferme,
        date_speciale,
        notes
    )
VALUES (
        'monday',
        '09:00:00',
        '18:00:00',
        0,
        NULL,
        'Consultations sur RDV'
    ),
    ('tuesday', '09:00:00', '18:00:00', 0, NULL, NULL),
    (
        'wednesday',
        '09:00:00',
        '18:00:00',
        0,
        NULL,
        NULL
    ),
    (
        'thursday',
        '09:00:00',
        '18:00:00',
        0,
        NULL,
        NULL
    ),
    ('friday', '09:00:00', '18:00:00', 0, NULL, NULL),
    (
        'saturday',
        '10:00:00',
        '16:00:00',
        0,
        NULL,
        'Uniquement rendez-vous'
    ),
    ('sunday', NULL, NULL, 1, NULL, 'Fermé');
-- RSE content
INSERT INTO contenu_rse (
        cle_section,
        titre,
        contenu,
        image_url,
        ordre_affichage,
        id_utilisateur_maj
    )
VALUES (
        'intro',
        'Notre engagement éco-responsable',
        'Chez Vite & Gourmand, nous croyons qu''il est possible de conjuguer gastronomie d''excellence et respect de l''environnement. Découvrez nos actions concrètes.',
        '/assets/rse/intro.jpg',
        1,
        1
    ),
    (
        'local',
        'Circuit court et producteurs locaux',
        'Plus de 80% de nos produits proviennent de producteurs situés à moins de 100km de Bordeaux. Nous privilégions les petits producteurs et l''agriculture raisonnée.',
        '/assets/rse/local.jpg',
        2,
        1
    ),
    (
        'waste',
        'Lutte contre le gaspillage',
        'Nos offres anti-gaspi permettent de valoriser les invendus à prix réduits. Nous compostons 100% de nos déchets organiques et privilégions les emballages réutilisables.',
        '/assets/rse/waste.jpg',
        3,
        1
    );
-- ============================================================
-- MODULE 7 — AUDIT
-- ============================================================
-- Status history
INSERT INTO historique_statut (
        id_commande,
        statut_precedent,
        nouveau_statut,
        id_utilisateur,
        motif,
        created_at
    )
VALUES (
        1,
        'pending',
        'confirmed',
        2,
        'Paiement reçu et disponibilités confirmées',
        DATE_SUB(NOW(), INTERVAL 5 DAY)
    ),
    (
        1,
        'confirmed',
        'in_preparation',
        2,
        'Début de la préparation des plats',
        DATE_SUB(NOW(), INTERVAL 2 DAY)
    ),
    (
        3,
        'pending',
        'confirmed',
        2,
        'Acompte versé',
        DATE_SUB(NOW(), INTERVAL 10 DAY)
    ),
    (
        3,
        'confirmed',
        'in_preparation',
        2,
        'Commande en cours de préparation',
        DATE_SUB(NOW(), INTERVAL 9 DAY)
    ),
    (
        3,
        'in_delivery',
        'completed',
        2,
        'Événement livré avec succès',
        DATE_SUB(NOW(), INTERVAL 8 DAY)
    );
-- Contact messages
INSERT INTO message_contact (
        nom_expediteur,
        email_expediteur,
        telephone_expediteur,
        sujet,
        message,
        statut,
        id_assigne,
        created_at
    )
VALUES (
        'Dupont',
        'alice.dupont@email.fr',
        '0645678901',
        'Demande de devis mariage',
        'Bonjour, nous organisons notre mariage le 15 août 2026 pour 120 personnes. Pouvez-vous nous faire un devis ?',
        'read',
        2,
        DATE_SUB(NOW(), INTERVAL 3 DAY)
    ),
    (
        'Moreau',
        'pierre.moreau@entreprise.fr',
        '0556789012',
        'Cocktail entreprise',
        'Nous recherchons un traiteur pour un cocktail de fin d''année (200 personnes). Disponibilités en décembre ?',
        'replied',
        2,
        DATE_SUB(NOW(), INTERVAL 1 DAY)
    );
-- Email logs
INSERT INTO log_email (
        destinataire,
        sujet,
        template,
        statut,
        type_entite,
        id_entite,
        envoye_a,
        created_at
    )
VALUES (
        'sophie.bernard@email.fr',
        'Confirmation de commande #1',
        'order_confirmation',
        'sent',
        'commande',
        1,
        DATE_SUB(NOW(), INTERVAL 5 DAY),
        DATE_SUB(NOW(), INTERVAL 5 DAY)
    ),
    (
        'claire.leroy@email.fr',
        'Votre commande #3 est livrée',
        'order_completed',
        'sent',
        'commande',
        3,
        DATE_SUB(NOW(), INTERVAL 8 DAY),
        DATE_SUB(NOW(), INTERVAL 8 DAY)
    ),
    (
        'thomas.petit@email.fr',
        'Votre commande #2 est en attente',
        'order_pending',
        'sent',
        'commande',
        2,
        DATE_SUB(NOW(), INTERVAL 2 DAY),
        DATE_SUB(NOW(), INTERVAL 2 DAY)
    );
-- Notifications
INSERT INTO notification (
        id_utilisateur,
        type,
        titre,
        message,
        lien_url,
        is_read,
        created_at
    )
VALUES (
        3,
        'order_status',
        'Commande confirmée',
        'Votre commande #1 a été confirmée et sera livrée le 15 juin.',
        '/orders/1',
        1,
        DATE_SUB(NOW(), INTERVAL 5 DAY)
    ),
    (
        5,
        'order_status',
        'Commande livrée',
        'Votre commande #3 a été livrée avec succès. N\'oubliez pas de laisser un avis !',
        '/orders/3',
        1,
        DATE_SUB(NOW(), INTERVAL 8 DAY)
    ),
    (
        4,
        'order_status',
        'Commande en attente',
        'Votre commande #2 est en attente de confirmation. Nous vous recontacterons sous 48h.',
        '/orders/2',
        0,
        DATE_SUB(NOW(), INTERVAL 2 DAY)
    );
-- Audit logs
INSERT INTO log_audit (
        id_utilisateur,
        action,
        type_entite,
        id_entite,
        anciennes_valeurs,
        nouvelles_valeurs,
        ip_address,
        user_agent,
        created_at
    )
VALUES (
        2,
        'update',
        'commande',
        1,
        '{"statut": "pending"}',
        '{"statut": "confirmed"}',
        '192.168.1.10',
        'Mozilla/5.0',
        DATE_SUB(NOW(), INTERVAL 5 DAY)
    ),
    (
        3,
        'create',
        'avis',
        1,
        NULL,
        '{"note": 5, "commentaire": "Prestation parfaite..."}',
        '192.168.1.25',
        'Mozilla/5.0',
        DATE_SUB(NOW(), INTERVAL 4 DAY)
    ),
    (
        2,
        'update',
        'avis',
        1,
        '{"statut": "pending"}',
        '{"statut": "approved"}',
        '192.168.1.10',
        'Mozilla/5.0',
        DATE_SUB(NOW(), INTERVAL 3 DAY)
    );
-- ============================================================
-- Re-enable foreign key checks
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================
-- Summary
-- ============================================================
-- Users: 5 (1 admin, 1 employee, 3 clients)
-- Themes: 5
-- Allergens: 14 (EU standard)
-- Regimes: 5
-- Eco badges: 3
-- Certifications: 3
-- Menus: 8
-- Dishes: 20
-- Services: 8
-- Orders: 3 (1 pending, 1 confirmed, 1 completed)
-- Reviews: 3 (2 approved, 1 pending)
-- Anti-waste offers: 2
-- Partners: 3
-- Team members: 4
-- Opening hours: 7 days
-- RSE sections: 3
-- ============================================================