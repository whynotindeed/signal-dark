<?php
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
$tplMedia = Uri::root(true) . '/media/templates/site/' . $this->template;
$this->addStyleSheet($tplMedia . '/css/fonts.css');
$this->addStyleSheet(Uri::root(true) . '/media/system/css/joomla-fontawesome.min.css');
$this->addStyleSheet($tplMedia . '/css/template.css');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="dark">
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="taid-component-only">
    <main class="taid-main">
        <div class="taid-container">
            <jdoc:include type="message" />
            <div class="taid-component-panel">
                <jdoc:include type="component" />
            </div>
        </div>
    </main>
</body>
</html>
