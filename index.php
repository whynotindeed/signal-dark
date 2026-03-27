<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app   = Factory::getApplication();
$input = $app->getInput();

$option = $input->getCmd('option', '');
$view   = $input->getCmd('view', '');
$layout = $input->getCmd('layout', '');
$task   = $input->getCmd('task', '');
$itemid = $input->getCmd('Itemid', '');

// Detect page context
$menu         = $app->getMenu();
$isHomePage   = ($menu->getActive() === $menu->getDefault());
$isArticle    = ($view === 'article');
$isCategory   = ($view === 'category');

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

// Open Graph / Twitter meta
$currentUrl = Uri::getInstance()->toString();
$ogTitle    = $this->title ?: $app->get('sitename');
$ogDesc     = $this->getMetaData('description') ?: 'Deep analysis, practical prompts, and working scripts for people who actually use AI.';
$ogImage    = Uri::root() . 'media/templates/site/' . $this->template . '/img/og-default.jpg';

$this->setMetaData('og:type',        $isArticle ? 'article' : 'website');
$this->setMetaData('og:title',       $ogTitle);
$this->setMetaData('og:description', $ogDesc);
$this->setMetaData('og:url',         $currentUrl);
$this->setMetaData('og:image',       $ogImage);
$this->setMetaData('og:site_name',   $app->get('sitename'));
$this->setMetaData('twitter:card',        'summary_large_image');
$this->setMetaData('twitter:title',       $ogTitle);
$this->setMetaData('twitter:description', $ogDesc);
$this->setMetaData('twitter:image',       $ogImage);

$siteName      = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$logoText      = (string) $this->params->get('logoText', 'My <span>Joomla</span> Site');
$siteTagline   = htmlspecialchars((string) $this->params->get('siteTagline',   'AI · Prompts · Automation'), ENT_QUOTES, 'UTF-8');
$heroTitle     = htmlspecialchars((string) $this->params->get('heroTitle',     'Think Clearly About AI.'), ENT_QUOTES, 'UTF-8');
$heroSubtitle  = htmlspecialchars((string) $this->params->get('heroSubtitle',  'Deep analysis, practical prompts, and working scripts for people who want to actually use AI — not just talk about it.'), ENT_QUOTES, 'UTF-8');
$ctaText       = htmlspecialchars((string) $this->params->get('ctaText',       'Read Articles'), ENT_QUOTES, 'UTF-8');
$ctaLink       = htmlspecialchars((string) $this->params->get('ctaLink',       '#articles'), ENT_QUOTES, 'UTF-8');
$secCtaText    = htmlspecialchars((string) $this->params->get('secondaryCtaText', 'Browse Scripts'), ENT_QUOTES, 'UTF-8');
$secCtaLink    = htmlspecialchars((string) $this->params->get('secondaryCtaLink', '#marketplace'), ENT_QUOTES, 'UTF-8');
$showHero        = (int) $this->params->get('showHero', 1) === 1;
$showTagline     = (int) $this->params->get('showHeroTagline', 1) === 1;
$showTitle       = (int) $this->params->get('showHeroTitle', 1) === 1;
$showSubtitle    = (int) $this->params->get('showHeroSubtitle', 1) === 1;
$showCta         = (int) $this->params->get('showHeroCta', 1) === 1;
$showSecCta      = (int) $this->params->get('showHeroSecondaryCta', 0) === 1;
$showStats       = (int) $this->params->get('showHeroStats', 1) === 1;
$heroImage       = (string) $this->params->get('heroImage', '');
$heroHeight      = (string) $this->params->get('heroHeight', 'auto');
$heroLayout      = (string) $this->params->get('heroLayout', 'overlay');
$heroImageFit    = (string) $this->params->get('heroImageFit', 'cover');
$heroImagePos    = (string) $this->params->get('heroImagePosition', 'center');
$heroOverlay     = (string) $this->params->get('heroOverlayOpacity', '70');
$showProgress    = (int) $this->params->get('showProgressBar', 1) === 1;
$stickyHeader    = (int) $this->params->get('stickyHeader', 1) === 1;
$stickySubnav    = (int) $this->params->get('stickySubnav', 1) === 1;
$showNavCta      = (int) $this->params->get('showNavCta', 0) === 1;
$showNavSearch   = (int) $this->params->get('showNavSearch', 1) === 1;
$kunenaCompact   = (int) $this->params->get('kunenaShowLastPost', 0) === 0;
$kunenaHidePost  = (int) $this->params->get('kunenaShowPostHeader', 1) === 0;
$kunenaLockOnce  = (int) $this->params->get('kunenaLockOnce', 1) === 1;
$linkHoverBg     = (int) $this->params->get('linkHoverBg', 1) === 1;
$kunenaFont      = (int) $this->params->get('kunenaTemplateFont', 1) === 1;
$kunenaGlow      = (int) $this->params->get('kunenaPostGlow', 1) === 1;
$jceDarkPreview  = (int) $this->params->get('jceDarkPreview', 1) === 1;
$roundedImages   = (int) $this->params->get('roundedImages', 1) === 1;
$blockLinks      = (int) $this->params->get('blockLinks', 1) === 1;
$showNewsletter  = (int) $this->params->get('showNewsletter', 1) === 1;
$nlTitle         = htmlspecialchars((string) $this->params->get('newsletterTitle',    'Stay Sharp on AI'), ENT_QUOTES, 'UTF-8');
$nlSubtitle      = htmlspecialchars((string) $this->params->get('newsletterSubtitle', 'New articles, prompt packs, and scripts — delivered when they\'re ready. No filler.'), ENT_QUOTES, 'UTF-8');

// ── Position Manager ─────────────────────────────────────────────
// Read the positionMap JSON param and merge with defaults.
$posMapRaw = $this->params->get('positionMap', '');
$posMap    = [];
if (is_string($posMapRaw) && $posMapRaw !== '') {
    $decoded = json_decode($posMapRaw, true);
    if (is_array($decoded)) {
        $posMap = $decoded;
    }
}
// Merge with defaults so every position is guaranteed to exist, enforce types
require_once __DIR__ . '/fields/positionmap.php';
$posDefaults = JFormFieldPositionmap::getDefaults();
foreach ($posDefaults as $pn => $pd) {
    if (!isset($posMap[$pn]) || !is_array($posMap[$pn])) {
        $posMap[$pn] = $pd;
    } else {
        $merged = array_merge($pd, $posMap[$pn]);
        $posMap[$pn] = [
            'desktop' => (int) ($merged['desktop'] ?? 0),
            'tablet'  => (int) ($merged['tablet'] ?? 0),
            'phone'   => (int) ($merged['phone'] ?? 0),
            'order'   => max(-10, min(10, (int) ($merged['order'] ?? 0))),
        ];
    }
}

/**
 * Build responsive visibility CSS classes for a template position.
 * Returns a string like "taid-hide-tablet taid-hide-phone" or empty.
 */
function taidPosClasses(array $posMap, string $position): string
{
    if (!isset($posMap[$position])) {
        return '';
    }
    $cfg = $posMap[$position];
    $cls = [];
    if (empty($cfg['desktop'])) { $cls[] = 'taid-hide-desktop'; }
    if (empty($cfg['tablet']))  { $cls[] = 'taid-hide-tablet'; }
    if (empty($cfg['phone']))   { $cls[] = 'taid-hide-phone'; }
    return implode(' ', $cls);
}

/**
 * Build an inline order style for mobile stacking.
 * Returns style="order:N" or empty string.
 */
function taidPosOrder(array $posMap, string $position): string
{
    if (!isset($posMap[$position])) {
        return '';
    }
    $order = (int) ($posMap[$position]['order'] ?? 0);
    if ($order === 0) {
        return '';
    }
    return ' style="order:' . $order . '"';
}

// Live stats
$db = Factory::getDbo();
$statArticles = (int) $db->setQuery('SELECT COUNT(*) FROM #__content WHERE state=1 AND catid > 1')->loadResult();
$statCats     = (int) $db->setQuery('SELECT COUNT(*) FROM #__categories WHERE extension="com_content" AND published=1 AND level=1 AND id > 1')->loadResult();

// Module checks
$hasMenu        = $this->countModules('menu', true);
$hasSearch      = $this->countModules('search', true);
$hasAnnounce    = $this->countModules('announcement', true);
$hasHero        = $this->countModules('hero', true);
$hasFeatured    = $this->countModules('featured', true);
$hasTopA        = $this->countModules('top-a', true);
$hasTopB        = $this->countModules('top-b', true);
$hasAdBanner    = $this->countModules('ad-banner', true);
$hasAdBannerRow = $this->countModules('ad-banner-row', true);
$hasSidebar     = $this->countModules('sidebar-right', true);
$hasSidebarLeft = $this->countModules('sidebar-left', true);
$hasAdSidebar   = $this->countModules('ad-sidebar', true);
$hasAdArticle   = $this->countModules('ad-article', true);
$hasAdArticleRow = $this->countModules('ad-article-row', true);
$hasAdFooter    = $this->countModules('ad-footer', true);
$hasBreadcrumbs = $this->countModules('breadcrumbs', true);
$hasContentBot  = $this->countModules('content-bottom', true);
$hasSidebarTop  = $this->countModules('sidebar-top', true);
$hasBottomA     = $this->countModules('bottom-a', true);
$hasBottomB     = $this->countModules('bottom-b', true);
$hasNewsletter  = $this->countModules('newsletter', true);
$hasMarketplace = $this->countModules('marketplace', true);
$hasFooterA     = $this->countModules('footer-a', true);
$hasFooterB     = $this->countModules('footer-b', true);
$hasFooterC     = $this->countModules('footer-c', true);
$hasFooterLogo  = $this->countModules('footer-logo', true);
$hasFooterCopy  = $this->countModules('footer-copy', true);
$hasFooterCredit = $this->countModules('footer-credit', true);

$topColClass = ($hasTopA && $hasTopB) ? 'taid-two-col--2' : 'taid-two-col--1';
$hasSidebarAny = $hasSidebar || $hasAdSidebar || $hasSidebarLeft;

// Assets
$fontQuery = 'family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500&family=Space+Mono:wght@400;700&display=swap';
switch ($this->params->get('fontService', 'google')) {
    case 'google':
        $this->addHeadLink('https://fonts.googleapis.com', 'preconnect', 'rel');
        $this->addHeadLink('https://fonts.gstatic.com', 'preconnect', 'rel', ['crossorigin' => 'anonymous']);
        $this->addStyleSheet('https://fonts.googleapis.com/css2?' . $fontQuery);
        break;
    case 'bunny':
        // Bunny Fonts: GDPR-friendly, /css2 endpoint is Google Fonts API v2 compatible
        $this->addHeadLink('https://fonts.bunny.net', 'preconnect', 'rel');
        $this->addStyleSheet('https://fonts.bunny.net/css2?' . $fontQuery);
        break;
    case 'adobe':
        // Adobe Fonts: strip to alphanumeric only — never trust user input in a URL
        $kitId = preg_replace('/[^a-zA-Z0-9]/', '', $this->params->get('adobeFontsKitId', ''));
        if ($kitId !== '') {
            $this->addStyleSheet('https://use.typekit.net/' . $kitId . '.css');
        }
        break;
    case 'local':
    default:
        // Local: hardcoded path, load fonts.css if the file exists
        if (is_file(JPATH_ROOT . '/media/templates/site/signal-dark/css/fonts.css')) {
            $this->addStyleSheet(Uri::root(true) . '/media/templates/site/signal-dark/css/fonts.css');
        }
        break;
}
$this->addStyleSheet(Uri::root(true) . '/media/system/css/joomla-fontawesome.min.css');
$this->addStyleSheet(Uri::root(true) . '/media/templates/site/signal-dark/css/template.css?v=20260306au');
$this->addStyleSheet(Uri::root(true) . '/media/templates/site/signal-dark/css/custom.css?v=20260302w');
$this->addScript(Uri::root(true) . '/media/templates/site/signal-dark/js/template.js?v=20260306j', [], ['defer' => true]);
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" data-bs-theme="dark">
<head>
    
    <link rel="icon" type="image/x-icon" href="<?php echo Uri::root(true); ?>/media/templates/site/signal-dark/img/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo Uri::root(true); ?>/media/templates/site/signal-dark/img/favicon-32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo Uri::root(true); ?>/media/templates/site/signal-dark/img/apple-touch-icon.png">
    <meta name="theme-color" content="#0a0a0f">
    <meta name="color-scheme" content="dark">
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": <?php echo json_encode($siteName ?: 'The AI Director', JSON_UNESCAPED_UNICODE); ?>,
      "url": "<?php echo Uri::root(); ?>",
      "description": "Deep analysis, practical prompts, and working scripts for people who actually use AI.",
      "publisher": {
        "@type": "Organization",
        "name": "The AI Director",
        "url": "<?php echo Uri::root(); ?>"
      },
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo Uri::root(); ?>index.php?option=com_search&searchword={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
<?php if (!$stickyHeader || !$stickySubnav) : ?>
<style>
    <?php if (!$stickyHeader) : ?>
    .taid-nav-wrap { position: relative; }
    <?php endif; ?>
    <?php if (!$stickySubnav) : ?>
    .taid-subnav { position: relative; top: auto; }
    <?php endif; ?>
    <?php if ($stickyHeader && !$stickySubnav) : ?>
    :root { --sticky-offset: var(--nav-h); }
    <?php elseif (!$stickyHeader && $stickySubnav) : ?>
    :root { --sticky-offset: var(--subnav-h); }
    .taid-subnav { top: 0; }
    <?php elseif (!$stickyHeader && !$stickySubnav) : ?>
    :root { --sticky-offset: 0px; }
    <?php endif; ?>
</style>
<?php endif; ?>
</head>
<body class="taid-site <?php echo $option
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout : '')
    . ($task   ? ' task-'   . $task   : '')
    . ($itemid ? ' itemid-' . $itemid : '')
    . ($kunenaCompact ? ' taid-kunena-compact' : '')
    . ($kunenaHidePost ? ' taid-kunena-no-post-header' : '')
    . ($kunenaLockOnce ? ' taid-kunena-lock-once' : '')
    . ($linkHoverBg    ? ' taid-link-hover-bg' : '')
    . ($kunenaFont     ? ' taid-kunena-template-font' : '')
    . ($kunenaGlow     ? ' taid-kunena-post-glow' : '')
    . ($jceDarkPreview ? ' taid-jce-dark-preview' : '')
    . ($roundedImages  ? ' taid-rounded-images' : '')
    . ($blockLinks     ? ' taid-block-links' : '');
?>">

    <?php if ($showProgress) : ?>
        <div class="taid-progress" id="taid-progress" role="progressbar" aria-label="Reading progress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    <?php endif; ?>

    <a href="#taid-main" class="taid-skip-link">Skip to content</a>

    <!-- ─── Signal bar ─────────────────────────────────────── -->
    <div class="taid-signal-bar" aria-hidden="true"></div>

    <!-- ─── Navigation ────────────────────────────────────── -->
    <header class="taid-nav-wrap <?php echo taidPosClasses($posMap, 'menu'); ?>" id="top"<?php echo taidPosOrder($posMap, 'menu'); ?>>
        <nav class="taid-nav" role="navigation" aria-label="Main navigation">
            <a class="taid-logo" href="<?php echo $this->baseurl; ?>/">
                <span class="taid-logo-text"><?php echo $logoText; ?></span>
            </a>

            <div class="taid-menu" id="taid-menu">
                <jdoc:include type="modules" name="menu" style="none" />
            </div>

            <?php if ($showNavSearch && $hasSearch) : ?>
            <div class="taid-nav-search">
                <jdoc:include type="modules" name="search" style="none" />
            </div>
            <?php endif; ?>

            <div class="taid-nav-actions">
                <?php if ($showNavSearch && $hasSearch) : ?>
                <button class="taid-search-toggle" id="taid-search-toggle" aria-label="Search" aria-expanded="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
                <?php endif; ?>
                <?php if ($showNavCta) : ?>
                <a href="<?php echo $ctaLink; ?>" class="taid-btn taid-btn-primary"><?php echo $ctaText; ?></a>
                <?php endif; ?>
                <button class="taid-hamburger" id="taid-hamburger" aria-controls="taid-menu" aria-expanded="false" aria-label="Open menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </nav>
    </header>

    <!-- ─── Secondary nav (optional) ────────────────────── -->
    <?php if ($this->countModules('subnav')) : ?>
    <div class="taid-subnav <?php echo taidPosClasses($posMap, 'subnav'); ?>"<?php echo taidPosOrder($posMap, 'subnav'); ?>>
        <div class="taid-container">
            <jdoc:include type="modules" name="subnav" style="none" />
        </div>
    </div>
    <?php endif; ?>

    <!-- ─── Announcement ──────────────────────────────────── -->
    <?php if ($hasAnnounce) : ?>
        <div class="taid-announce <?php echo taidPosClasses($posMap, 'announcement'); ?>"<?php echo taidPosOrder($posMap, 'announcement'); ?>>
            <div class="taid-container taid-container--tight">
                <jdoc:include type="modules" name="announcement" style="none" />
            </div>
        </div>
    <?php endif; ?>

    <!-- ─── Hero (homepage only, toggleable) ────────────────── -->
    <?php if ($isHomePage && $showHero) : ?>
    <section class="taid-hero taid-hero--<?php echo $heroLayout; ?><?php if ($heroImage) echo ' taid-hero--has-image'; ?> <?php echo taidPosClasses($posMap, 'hero'); ?>" aria-label="Hero"<?php
        $heroStyles = [];
        if ($heroImage) {
            $heroStyles[] = 'background-image:url(' . Uri::root(true) . '/' . $heroImage . ')';
            $fit = ($heroImageFit === 'stretch') ? '100% 100%' : $heroImageFit;
            $heroStyles[] = 'background-size:' . $fit;
            $heroStyles[] = 'background-position:' . $heroImagePos;
            $heroStyles[] = 'background-repeat:no-repeat';
        }
        if ($heroHeight !== 'auto') $heroStyles[] = 'min-height:' . $heroHeight;
        if ($heroStyles) echo ' style="' . implode(';', $heroStyles) . '"';
        echo taidPosOrder($posMap, 'hero');
    ?>>
        <?php if ($heroImage && $heroOverlay > 0) : ?>
            <div class="taid-hero-overlay" style="background:rgba(10,10,25,<?php echo $heroOverlay / 100; ?>)"></div>
        <?php elseif (!$heroImage) : ?>
            <div class="taid-hero-grid" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="taid-container taid-hero-inner">
            <?php if ($showTagline) : ?>
                <p class="taid-eyebrow"><?php echo $siteTagline; ?></p>
            <?php endif; ?>
            <?php if ($showTitle) : ?>
                <h1 class="taid-hero-title"><?php echo $heroTitle; ?></h1>
            <?php endif; ?>
            <?php if ($showSubtitle) : ?>
                <p class="taid-hero-subtitle"><?php echo $heroSubtitle; ?></p>
            <?php endif; ?>
            <?php if ($showCta || $showSecCta) : ?>
                <div class="taid-hero-actions">
                    <?php if ($showCta) : ?>
                        <a href="<?php echo $ctaLink; ?>" class="taid-btn taid-btn-primary taid-btn--lg"><?php echo $ctaText; ?></a>
                    <?php endif; ?>
                    <?php if ($showSecCta) : ?>
                        <a href="<?php echo $secCtaLink; ?>" class="taid-btn taid-btn-ghost taid-btn--lg"><?php echo $secCtaText; ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($showStats) : ?>
                <div class="taid-hero-stats">
                    <div class="taid-stat">
                        <span class="taid-stat-value"><?php echo $statArticles; ?></span>
                        <span class="taid-stat-label">In-depth articles</span>
                    </div>
                    <div class="taid-stat">
                        <span class="taid-stat-value"><?php echo $statCats; ?></span>
                        <span class="taid-stat-label">Topic categories</span>
                    </div>
                    <div class="taid-stat">
                        <span class="taid-stat-value">Free</span>
                        <span class="taid-stat-label">No paywall, ever</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($hasHero) : ?>
            <div class="taid-container taid-hero-modules">
                <jdoc:include type="modules" name="hero" style="none" />
            </div>
        <?php endif; ?>
    </section>
    <?php endif; /* isHomePage — hero */ ?>

    <!-- ─── After-hero slot ───────────────────────────────── -->
    <?php if ($this->countModules('after-hero', true)) : ?>
        <div class="taid-after-hero <?php echo taidPosClasses($posMap, 'after-hero'); ?>"<?php echo taidPosOrder($posMap, 'after-hero'); ?>>
            <div class="taid-container">
                <jdoc:include type="modules" name="after-hero" style="none" />
            </div>
        </div>
    <?php endif; ?>

    <!-- ─── Featured (homepage only) ─────────────────────── -->
    <?php if ($isHomePage && $hasFeatured) : ?>
        <section class="taid-section <?php echo taidPosClasses($posMap, 'featured'); ?>" id="featured"<?php echo taidPosOrder($posMap, 'featured'); ?>>
            <div class="taid-container">
                <h2 class="taid-section-title">Featured</h2>
                <div class="taid-featured-grid">
                    <jdoc:include type="modules" name="featured" style="none" />
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Latest / Trending (homepage only) ────────────── -->
    <?php if ($isHomePage && ($hasTopA || $hasTopB)) : ?>
        <section class="taid-section" id="latest">
            <div class="taid-container">
                <div class="taid-two-col <?php echo $topColClass; ?>">
                    <?php if ($hasTopA) : ?>
                        <div class="taid-col-panel <?php echo taidPosClasses($posMap, 'top-a'); ?>"<?php echo taidPosOrder($posMap, 'top-a'); ?>>
                            <h2 class="taid-section-title">Latest Insights</h2>
                            <jdoc:include type="modules" name="top-a" style="none" />
                        </div>
                    <?php endif; ?>
                    <?php if ($hasTopB) : ?>
                        <div class="taid-col-panel <?php echo taidPosClasses($posMap, 'top-b'); ?>"<?php echo taidPosOrder($posMap, 'top-b'); ?>>
                            <h2 class="taid-section-title">Trending</h2>
                            <jdoc:include type="modules" name="top-b" style="none" />
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Ad banner ─────────────────────────────────────── -->
    <?php if ($hasAdBanner) : ?>
        <div class="taid-ad-strip <?php echo taidPosClasses($posMap, 'ad-banner'); ?>"<?php echo taidPosOrder($posMap, 'ad-banner'); ?>>
            <div class="taid-container taid-container--tight">
                <div class="taid-ad-slot taid-ad-slot--banner">
                    <jdoc:include type="modules" name="ad-banner" style="none" />
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ─── Ad banner row (3-up) ──────────────────────────── -->
    <?php if ($hasAdBannerRow) : ?>
        <div class="taid-ad-strip taid-ad-strip--row <?php echo taidPosClasses($posMap, 'ad-banner-row'); ?>"<?php echo taidPosOrder($posMap, 'ad-banner-row'); ?>>
            <div class="taid-container taid-container--tight">
                <div class="taid-ad-row">
                    <jdoc:include type="modules" name="ad-banner-row" style="none" />
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ─── Main content ──────────────────────────────────── -->
    <main id="taid-main" class="taid-main">
        <div class="taid-container">
            <?php if ($isHomePage) : ?>
            <h2 class="taid-section-title" id="articles">Articles</h2>
            <?php endif; ?>

            <?php if ($hasBreadcrumbs) : ?>
            <div class="taid-breadcrumbs <?php echo taidPosClasses($posMap, 'breadcrumbs'); ?>"<?php echo taidPosOrder($posMap, 'breadcrumbs'); ?>>
                <jdoc:include type="modules" name="breadcrumbs" style="none" />
            </div>
            <?php endif; ?>

            <?php if ($this->countModules('main-top')) : ?>
            <div class="taid-ft-bar-wrap <?php echo taidPosClasses($posMap, 'main-top'); ?>"<?php echo taidPosOrder($posMap, 'main-top'); ?>>
                <jdoc:include type="modules" name="main-top" style="none" />
            </div>
            <?php endif; ?>
            <jdoc:include type="message" />

            <div class="taid-content-wrap<?php echo $hasSidebarAny ? ' taid-content-wrap--sidebar' : ''; ?><?php echo $hasSidebarLeft ? ' taid-content-wrap--sidebar-left' : ''; ?>">
                <?php if ($hasSidebarLeft) : ?>
                    <aside class="taid-sidebar taid-sidebar--left <?php echo taidPosClasses($posMap, 'sidebar-left'); ?>" aria-label="Left sidebar"<?php echo taidPosOrder($posMap, 'sidebar-left'); ?>>
                        <jdoc:include type="modules" name="sidebar-left" style="card" />
                    </aside>
                <?php endif; ?>

                <div class="taid-content-primary">
                    <div class="taid-component-panel">
                        <jdoc:include type="component" />
                    </div>

                    <?php if ($hasAdArticle) : ?>
                        <div class="taid-ad-slot taid-ad-slot--article <?php echo taidPosClasses($posMap, 'ad-article'); ?>"<?php echo taidPosOrder($posMap, 'ad-article'); ?>>
                            <jdoc:include type="modules" name="ad-article" style="none" />
                        </div>
                    <?php endif; ?>

                    <?php if ($hasAdArticleRow) : ?>
                        <div class="taid-ad-row taid-ad-row--article <?php echo taidPosClasses($posMap, 'ad-article-row'); ?>"<?php echo taidPosOrder($posMap, 'ad-article-row'); ?>>
                            <jdoc:include type="modules" name="ad-article-row" style="none" />
                        </div>
                    <?php endif; ?>

                    <?php if ($hasContentBot) : ?>
                    <div class="taid-content-bottom <?php echo taidPosClasses($posMap, 'content-bottom'); ?>"<?php echo taidPosOrder($posMap, 'content-bottom'); ?>>
                        <jdoc:include type="modules" name="content-bottom" style="html5" />
                    </div>
                    <?php endif; ?>

                    <?php if ($this->countModules('main-bottom')) : ?>
                    <div class="taid-main-bottom-grid <?php echo taidPosClasses($posMap, 'main-bottom'); ?>"<?php echo taidPosOrder($posMap, 'main-bottom'); ?>>
                        <jdoc:include type="modules" name="main-bottom" style="html5" />
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($hasSidebar || $hasAdSidebar) : ?>
                    <?php
                    // Right sidebar aside: hide on a device only when ALL its positions are hidden there
                    $rSidebarCls = [];
                    $rCfg   = $posMap['sidebar-right'] ?? [];
                    $adsCfg = $posMap['ad-sidebar'] ?? [];
                    if (empty($rCfg['desktop']) && empty($adsCfg['desktop'])) { $rSidebarCls[] = 'taid-hide-desktop'; }
                    if (empty($rCfg['tablet'])  && empty($adsCfg['tablet']))  { $rSidebarCls[] = 'taid-hide-tablet'; }
                    if (empty($rCfg['phone'])   && empty($adsCfg['phone']))   { $rSidebarCls[] = 'taid-hide-phone'; }
                    $rSidebarClasses = implode(' ', $rSidebarCls);
                    ?>
                    <aside class="taid-sidebar <?php echo $rSidebarClasses; ?>" aria-label="Sidebar"<?php echo taidPosOrder($posMap, 'sidebar-right'); ?>>
                        <?php if ($hasSidebarTop) : ?>
                            <div class="taid-pos-group <?php echo taidPosClasses($posMap, 'sidebar-top'); ?>">
                                <jdoc:include type="modules" name="sidebar-top" style="card" />
                            </div>
                        <?php endif; ?>
                        <?php if ($hasSidebar) : ?>
                            <div class="taid-pos-group <?php echo taidPosClasses($posMap, 'sidebar-right'); ?>">
                                <jdoc:include type="modules" name="sidebar-right" style="card" />
                            </div>
                        <?php endif; ?>
                        <?php if ($hasAdSidebar) : ?>
                            <div class="taid-ad-slot taid-ad-slot--sidebar <?php echo taidPosClasses($posMap, 'ad-sidebar'); ?>"<?php echo taidPosOrder($posMap, 'ad-sidebar'); ?>>
                                <jdoc:include type="modules" name="ad-sidebar" style="none" />
                            </div>
                        <?php endif; ?>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- ─── Bottom rows ───────────────────────────────────────── -->
    <?php if ($hasBottomA || $hasBottomB) : ?>
    <section class="taid-section taid-bottom-rows">
        <div class="taid-container">
            <div class="taid-two-col <?php echo ($hasBottomA && $hasBottomB) ? 'taid-two-col--2' : 'taid-two-col--1'; ?>">
                <?php if ($hasBottomA) : ?>
                <div class="taid-col-panel <?php echo taidPosClasses($posMap, 'bottom-a'); ?>"<?php echo taidPosOrder($posMap, 'bottom-a'); ?>>
                    <jdoc:include type="modules" name="bottom-a" style="html5" />
                </div>
                <?php endif; ?>
                <?php if ($hasBottomB) : ?>
                <div class="taid-col-panel <?php echo taidPosClasses($posMap, 'bottom-b'); ?>"<?php echo taidPosOrder($posMap, 'bottom-b'); ?>>
                    <jdoc:include type="modules" name="bottom-b" style="html5" />
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ─── Shop Intro ──────────────────────────────────────── -->
    <?php if ($this->countModules('shop-intro')) : ?>
        <section class="taid-section taid-shop-intro <?php echo taidPosClasses($posMap, 'shop-intro'); ?>" id="marketplace"<?php echo taidPosOrder($posMap, 'shop-intro'); ?>>
            <div class="taid-container">
                <jdoc:include type="modules" name="shop-intro" style="none" />
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Marketplace ───────────────────────────────────── -->
    <?php if ($hasMarketplace) : ?>
        <section class="taid-section taid-marketplace <?php echo taidPosClasses($posMap, 'marketplace'); ?>"<?php echo taidPosOrder($posMap, 'marketplace'); ?>>
            <div class="taid-container">
                <jdoc:include type="modules" name="marketplace" style="none" />
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Newsletter ────────────────────────────────────── -->
    <?php if ($hasNewsletter && $showNewsletter) : ?>
        <section class="taid-newsletter <?php echo taidPosClasses($posMap, 'newsletter'); ?>" id="newsletter"<?php echo taidPosOrder($posMap, 'newsletter'); ?>>
            <div class="taid-newsletter-glow" aria-hidden="true"></div>
            <div class="taid-container taid-newsletter-inner">
                <h2 class="taid-newsletter-title"><?php echo $nlTitle; ?></h2>
                <p class="taid-newsletter-sub"><?php echo $nlSubtitle; ?></p>
                <div class="taid-newsletter-form">
                    <jdoc:include type="modules" name="newsletter" style="none" />
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Partner ────────────────────────────────────────── -->
    <?php if ($this->countModules('partner')) : ?>
    <div class="taid-partner-strip <?php echo taidPosClasses($posMap, 'partner'); ?>"<?php echo taidPosOrder($posMap, 'partner'); ?>>
        <div class="taid-container">
            <jdoc:include type="modules" name="partner" style="none" />
        </div>
    </div>
    <?php endif; ?>

    <!-- ─── Ad footer ───────────────────────────────────────── -->
    <?php if ($hasAdFooter) : ?>
        <div class="taid-ad-strip taid-ad-strip--footer <?php echo taidPosClasses($posMap, 'ad-footer'); ?>"<?php echo taidPosOrder($posMap, 'ad-footer'); ?>>
            <div class="taid-container taid-container--tight">
                <div class="taid-ad-slot taid-ad-slot--banner">
                    <jdoc:include type="modules" name="ad-footer" style="none" />
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ─── Footer ────────────────────────────────────────── -->
    <footer class="taid-footer">
        <div class="taid-container">
            <?php if ($hasFooterA || $hasFooterB || $hasFooterC) : ?>
                <div class="taid-footer-grid">
                    <?php if ($hasFooterA) : ?>
                        <div class="taid-pos-group <?php echo taidPosClasses($posMap, 'footer-a'); ?>"<?php echo taidPosOrder($posMap, 'footer-a'); ?>><jdoc:include type="modules" name="footer-a" style="none" /></div>
                    <?php endif; ?>
                    <?php if ($hasFooterB) : ?>
                        <div class="taid-pos-group <?php echo taidPosClasses($posMap, 'footer-b'); ?>"<?php echo taidPosOrder($posMap, 'footer-b'); ?>><jdoc:include type="modules" name="footer-b" style="none" /></div>
                    <?php endif; ?>
                    <?php if ($hasFooterC) : ?>
                        <div class="taid-pos-group <?php echo taidPosClasses($posMap, 'footer-c'); ?>"<?php echo taidPosOrder($posMap, 'footer-c'); ?>><jdoc:include type="modules" name="footer-c" style="none" /></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="<?php echo taidPosClasses($posMap, 'footer'); ?>"<?php echo taidPosOrder($posMap, 'footer'); ?>>
                <jdoc:include type="modules" name="footer" style="none" />
            </div>

            <div class="taid-footer-bottom">
                <?php if ($hasFooterLogo) : ?>
                    <jdoc:include type="modules" name="footer-logo" style="none" />
                <?php else : ?>
                    <a class="taid-footer-logo" href="<?php echo $this->baseurl; ?>/"><?php echo $logoText; ?></a>
                <?php endif; ?>
                <?php if ($hasFooterCopy) : ?>
                    <jdoc:include type="modules" name="footer-copy" style="none" />
                <?php else : ?>
                    <p class="taid-footer-copy">&copy; <?php echo date('Y'); ?> <?php echo $siteName; ?> — All rights reserved.</p>
                <?php endif; ?>
                <?php if ($hasFooterCredit) : ?>
                    <jdoc:include type="modules" name="footer-credit" style="none" />
                <?php else : ?>
                    <p class="taid-footer-powered">Powered by <a href="https://www.joomla.org" target="_blank" rel="noopener noreferrer">Joomla</a> &amp; <a href="https://demo.theaidirector.win/signal-dark/getting-started" target="_blank" rel="noopener noreferrer">Signal Dark</a></p>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <!-- ─── Back to top ───────────────────────────────────── -->
    <button class="taid-back-top" id="taid-back-top" aria-label="Back to top" title="Back to top">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M8 12V4M4 8l4-4 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div class="<?php echo taidPosClasses($posMap, 'debug'); ?>"<?php echo taidPosOrder($posMap, 'debug'); ?>>
        <jdoc:include type="modules" name="debug" style="none" />
    </div>
</body>
</html>
