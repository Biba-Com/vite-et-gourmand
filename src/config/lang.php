<?php
/**
 * Language Engine — i18n
 * Path: src/config/lang.php
 *
 * Detection priority: URL param → Session → Browser preference → Default (fr)
 * Usage in any view: <?= __('nav.menus') ?>
 */

/* ---------------------------------------------------------------
   1. Start session if not already active
   --------------------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ---------------------------------------------------------------
   2. Supported languages
   --------------------------------------------------------------- */
define('LANG_SUPPORTED', ['fr', 'en']);
define('LANG_DEFAULT', 'fr');

/* ---------------------------------------------------------------
   3. Detect language — URL param > Session > Browser > Default
   --------------------------------------------------------------- */
function detectLanguage(): string
{
    /* Priority 1 — URL parameter ?lang=xx */
    if (isset($_GET['lang']) && in_array($_GET['lang'], LANG_SUPPORTED, true)) {
        $lang = $_GET['lang'];
        $_SESSION['lang'] = $lang;
        return $lang;
    }

    /* Priority 2 — Session (user already chose) */
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], LANG_SUPPORTED, true)) {
        return $_SESSION['lang'];
    }

    /* Priority 3 — Browser Accept-Language header */
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
    if (in_array($browserLang, LANG_SUPPORTED, true)) {
        return $browserLang;
    }

    /* Priority 4 — Default */
    return LANG_DEFAULT;
}

/* ---------------------------------------------------------------
   4. Load translations array
   --------------------------------------------------------------- */
$GLOBALS['lang']         = detectLanguage();
$GLOBALS['translations'] = [];

$langFile = __DIR__ . '/locales/' . $GLOBALS['lang'] . '.php';

if (file_exists($langFile)) {
    $GLOBALS['translations'] = require $langFile;
} else {
    /* Fallback to French if file missing */
    $GLOBALS['translations'] = require __DIR__ . '/locales/fr.php';
}

/* ---------------------------------------------------------------
   5. Translation helper — use everywhere in views
   --------------------------------------------------------------- */

/**
 * Translate a key using dot notation
 *
 * @param string $key     Dot notation key, e.g. 'nav.menus'
 * @param array  $replace Placeholders, e.g. ['name' => 'Marie']
 * @return string         Translated string or key if not found
 *
 * Examples:
 *   <?= __('nav.menus') ?>            → "Nos Menus" / "Our Menus"
 *   <?= __('home.hero.cta') ?>        → "Découvrir" / "Discover"
 *   <?= __('greeting', ['name'=>'Marie']) ?>  → "Bonjour Marie"
 */
function __(string $key, array $replace = []): string
{
    $keys  = explode('.', $key);
    $value = $GLOBALS['translations'];

    foreach ($keys as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            /* Key not found — return key itself (never blank) */
            return $key;
        }
        $value = $value[$segment];
    }

    $text = is_string($value) ? $value : $key;

    /* Replace placeholders: :name, :count, etc. */
    foreach ($replace as $placeholder => $replacement) {
        $text = str_replace(':' . $placeholder, htmlspecialchars((string)$replacement, ENT_QUOTES, 'UTF-8'), $text);
    }

    return $text;
}

/**
 * Get current language code
 * @return string 'fr' | 'en'
 */
function currentLang(): string
{
    return $GLOBALS['lang'];
}

/**
 * Build a URL preserving current query params but switching the lang
 * @param string $lang Target language code
 * @return string URL with ?lang=xx
 */
function langSwitchUrl(string $lang): string
{
    $params = $_GET;
    $params['lang'] = $lang;
    $path = strtok($_SERVER['REQUEST_URI'], '?');
    return $path . '?' . http_build_query($params);
}
