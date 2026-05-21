<?php
/**
 * Section: Nos Menus Signatures
 * Path: src/views/components/menus-home.php
 * 3 cartes menus — données statiques pour l'instant, BDD plus tard
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';

$menus = [
  [
    'image'       => '/assets/img/menus/menu-festif.webp',
    'alt_fr'      => 'Buffet de mariage avec plats du terroir bordelais',
    'alt_en'      => 'Wedding buffet with local Bordeaux dishes',
    'badge'       => 'MARIAGE',
    'badge_color' => 'bordeaux',
    'price'       => 39,
    'title_fr'    => 'Menu Festif',
    'title_en'    => 'Festive Menu',
    'desc_fr'     => 'Une explosion de saveurs pour célébrer vos moments joyeux avec gourmandise et convivialité.',
    'desc_en'     => 'An explosion of flavors to celebrate your joyful moments with indulgence and conviviality.',
    'slug'        => 'menu-festif',
  ],
  [
    'image'       => '/assets/img/menus/menu-terroir.webp',
    'alt_fr'      => 'Menu terroir bordelais avec produits locaux et bio',
    'alt_en'      => 'Bordeaux terroir menu with local organic products',
    'badge'       => 'BIO · LOCAL',
    'badge_color' => 'forest',
    'price'       => 52,
    'title_fr'    => 'Menu Terroir',
    'title_en'    => 'Terroir Menu',
    'desc_fr'     => 'Le meilleur des producteurs locaux et bio, pour une expérience authentique et responsable.',
    'desc_en'     => 'The best of local and organic producers for an authentic and responsible experience.',
    'slug'        => 'menu-terroir',
  ],
  [
    'image'       => '/assets/img/menus/menu-prestige.webp',
    'alt_fr'      => 'Menu prestige gastronomique avec présentation raffinée',
    'alt_en'      => 'Gastronomic prestige menu with refined presentation',
    'badge'       => 'PRESTIGE',
    'badge_color' => 'bordeaux',
    'price'       => 125,
    'title_fr'    => 'Menu Prestige',
    'title_en'    => 'Prestige Menu',
    'desc_fr'     => 'L\'excellence gastronomique pour sublimer vos événements d\'exception avec raffinement.',
    'desc_en'     => 'Gastronomic excellence to elevate your exceptional events with refinement.',
    'slug'        => 'menu-prestige',
  ],
];
?>

<section class="menus-home" aria-labelledby="menus-home-title" data-animate="fade-up">
  <div class="container">

    <div class="section-header">
      <h2 id="menus-home-title" class="section-header__title">
        <?= $currentLang === 'en' ? 'Our Signature Menus' : 'Nos Menus Signatures' ?>
      </h2>
      <a href="/catalogue/" class="section-header__link">
        <?= $currentLang === 'en' ? 'View all menus →' : 'Voir tous nos menus →' ?>
      </a>
    </div>

    <div class="menus-home__grid">
      <?php foreach ($menus as $menu) : ?>
        <article class="menu-card" data-animate="card-up">
          <a href="/catalogue/detail.php?slug=<?= $menu['slug'] ?>" class="menu-card__img-wrapper" tabindex="-1" aria-hidden="true">
            <img
              src="<?= $menu['image'] ?>"
              alt="<?= $currentLang === 'en' ? $menu['alt_en'] : $menu['alt_fr'] ?>"
              class="menu-card__img"
              width="350" height="240"
              loading="lazy"
              decoding="async"
            >
            <span class="menu-card__badge menu-card__badge--<?= $menu['badge_color'] ?>" aria-label="<?= $currentLang === 'en' ? 'Category: ' : 'Catégorie : ' ?><?= $menu['badge'] ?>">
              <?= $menu['badge'] ?>
            </span>
            <span class="menu-card__price" aria-label="<?= $menu['price'] ?>€ <?= $currentLang === 'en' ? 'per person' : 'par personne' ?>">
              <?= $menu['price'] ?>€<small>/pers.</small>
            </span>
          </a>

          <div class="menu-card__body">
            <h3 class="menu-card__title">
              <a href="/catalogue/detail.php?slug=<?= $menu['slug'] ?>" class="menu-card__title-link">
                <?= $currentLang === 'en' ? $menu['title_en'] : $menu['title_fr'] ?>
              </a>
            </h3>
            <p class="menu-card__desc">
              <?= $currentLang === 'en' ? $menu['desc_en'] : $menu['desc_fr'] ?>
            </p>
            <a href="/catalogue/detail.php?slug=<?= $menu['slug'] ?>" class="btn btn--forest menu-card__cta">
              <?= $currentLang === 'en' ? 'Discover the menu' : 'Découvrir le menu' ?>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <!-- Lien mobile en bas (visible uniquement sur petit écran) -->
    <div class="menus-home__mobile-link">
      <a href="/catalogue/">
        <?= $currentLang === 'en' ? 'See all our menus →' : 'Voir tous nos menus →' ?>
      </a>
    </div>

  </div>
</section>
