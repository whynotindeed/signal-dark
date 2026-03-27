<?php
defined('_JEXEC') or die;
$app = \Joomla\CMS\Factory::getApplication();
$errorCode = $this->error->getCode();
$siteName = $app->get('sitename', 'My Joomla Site');
?><!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" data-bs-theme="dark">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="color-scheme" content="dark">
<title><?php echo $errorCode; ?> — <?php echo htmlspecialchars($siteName); ?></title>
<link href="/media/templates/site/signal-dark/css/fonts.css" rel="stylesheet">
<link href="/media/system/css/joomla-fontawesome.min.css" rel="stylesheet">
<link href="/media/templates/site/signal-dark/css/template.css" rel="stylesheet">
<style>
.taid-error{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:3rem 1.5rem}
.taid-error-code{font-family:var(--font-display);font-size:clamp(4rem,12vw,8rem);font-weight:700;background:var(--grad-signal);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1;margin-bottom:1rem}
.taid-error-msg{font-size:1.25rem;color:var(--text-soft);max-width:500px;margin-bottom:2rem;line-height:1.6}
.taid-error-home{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 2rem;border:1px solid var(--violet);color:var(--violet-lt);border-radius:var(--radius);text-decoration:none;font-weight:500;transition:all .2s ease}
.taid-error-home:hover{background:var(--violet);color:#fff}
</style>
</head>
<body class="taid-site">
<div class="taid-signal-bar" aria-hidden="true"></div>
<header class="taid-nav-wrap" id="top"><nav class="taid-nav" role="navigation"><a class="taid-logo" href="/"><span class="taid-logo-text"><?php $p=explode(" ",$siteName,2);echo htmlspecialchars($p[0]);if(isset($p[1]))echo " <span>".htmlspecialchars($p[1])."</span>"; ?></span></a></nav></header>
<main class="taid-main"><div class="taid-error">
<div class="taid-error-code"><?php echo (int)$errorCode; ?></div>
<p class="taid-error-msg"><?php echo $errorCode==404?"The page you're looking for doesn't exist or has been moved.":htmlspecialchars($this->error->getMessage()?:"Something went wrong."); ?></p>
<a href="/" class="taid-error-home"><span class="icon-home" aria-hidden="true"></span> Back to Home</a>
</div></main>
<footer class="taid-footer"><div class="taid-container"><div class="taid-footer-bottom"><p class="taid-footer-copy">&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($siteName); ?> — All rights reserved.</p></div></div></footer>
</body></html>