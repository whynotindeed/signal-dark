<?php
/**
 * Card module chrome for The AI Director template.
 * Wraps each module in a .moduletable with an h3 title header.
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '') {
    return;
}

$sfx   = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$tag   = htmlspecialchars($params->get('header_tag', 'h3'), ENT_QUOTES, 'UTF-8');

?>
<div class="moduletable <?php echo $sfx; ?>">
    <?php if ($module->showtitle) : ?>
        <<?php echo $tag; ?>><?php echo $module->title; ?></<?php echo $tag; ?>>
    <?php endif; ?>
    <?php echo $module->content; ?>
</div>
