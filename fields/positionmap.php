<?php

/**
 * Signal Dark — Visual Position Manager
 *
 * Custom Joomla form field that renders an interactive A4-style page blueprint
 * showing all template positions with per-device visibility toggles and mobile
 * stacking order controls. Data is stored as a JSON blob in template params.
 *
 * @package  signal-dark
 * @since    1.1.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Uri\Uri;

class JFormFieldPositionmap extends FormField
{
    protected $type = 'Positionmap';

    /**
     * Template layout definition.
     * Each row describes a horizontal section of the page with its positions.
     * Types: full (single full-width), cols-2, cols-3, content-area (3-col with sidebars).
     */
    private const LAYOUT = [
        ['label' => 'Search',           'type' => 'full',         'positions' => ['search']],
        ['label' => 'Navigation',      'type' => 'full',         'positions' => ['menu']],
        ['label' => 'Sub-navigation',  'type' => 'full',         'positions' => ['subnav']],
        ['label' => 'Announcement',    'type' => 'full',         'positions' => ['announcement']],
        ['label' => 'Hero',            'type' => 'full',         'positions' => ['hero']],
        ['label' => 'After Hero',      'type' => 'full',         'positions' => ['after-hero']],
        ['label' => 'Featured',        'type' => 'full',         'positions' => ['featured']],
        ['label' => 'Top Columns',     'type' => 'cols-2',       'positions' => ['top-a', 'top-b']],
        ['label' => 'Ad Banner',       'type' => 'full',         'positions' => ['ad-banner']],
        ['label' => 'Ad Banner Row',   'type' => 'full',         'positions' => ['ad-banner-row']],
        ['label' => 'Breadcrumbs',     'type' => 'full',         'positions' => ['breadcrumbs']],
        ['label' => 'Main Top',        'type' => 'full',         'positions' => ['main-top']],
        ['label' => 'Content Area',    'type' => 'content-area', 'positions' => [
            'sidebar-left', '_component', 'ad-article', 'ad-article-row', 'content-bottom', 'main-bottom',
            'sidebar-top', 'sidebar-right', 'ad-sidebar',
        ]],
        ['label' => 'Bottom Columns',  'type' => 'cols-2',       'positions' => ['bottom-a', 'bottom-b']],
        ['label' => 'Newsletter',      'type' => 'full',         'positions' => ['newsletter']],
        ['label' => 'Shop Intro',      'type' => 'full',         'positions' => ['shop-intro']],
        ['label' => 'Marketplace',     'type' => 'full',         'positions' => ['marketplace']],
        ['label' => 'Ad Footer',       'type' => 'full',         'positions' => ['ad-footer']],
        ['label' => 'Partner',         'type' => 'full',         'positions' => ['partner']],
        ['label' => 'Footer Columns',  'type' => 'cols-3',       'positions' => ['footer-a', 'footer-b', 'footer-c']],
        ['label' => 'Footer',          'type' => 'full',         'positions' => ['footer']],
        ['label' => 'Footer Bottom',   'type' => 'cols-3',       'positions' => ['footer-logo', 'footer-copy', 'footer-credit']],
        ['label' => 'Debug',           'type' => 'full',         'positions' => ['debug']],
    ];

    /**
     * All toggleable position names (excludes _component pseudo-position).
     */
    private const POSITIONS = [
        'search', 'menu', 'subnav', 'announcement', 'hero', 'after-hero', 'featured',
        'top-a', 'top-b', 'ad-banner', 'ad-banner-row', 'breadcrumbs', 'main-top',
        'sidebar-left', 'sidebar-top', 'sidebar-right', 'ad-sidebar', 'ad-article',
        'ad-article-row', 'content-bottom', 'main-bottom', 'bottom-a', 'bottom-b',
        'newsletter', 'shop-intro', 'marketplace', 'ad-footer', 'partner',
        'footer-a', 'footer-b', 'footer-c', 'footer', 'footer-logo', 'footer-copy',
        'footer-credit', 'debug',
    ];

    /**
     * Returns sensible defaults — all visible, order 0.
     * Sidebars default to desktop-only.
     */
    public static function getDefaults(): array
    {
        $defaults = [];
        foreach (self::POSITIONS as $pos) {
            $defaults[$pos] = ['desktop' => 1, 'tablet' => 1, 'phone' => 1, 'order' => 0];
        }
        // Sidebars: visible on all devices (stacks below content on tablet/phone)
        // Ad sidebar: hidden on tablet/phone (ads too large for small screens)
        $defaults['ad-sidebar']['tablet']    = 0;
        $defaults['ad-sidebar']['phone']     = 0;

        return $defaults;
    }

    protected function getInput(): string
    {
        $data = $this->parseValue();

        $this->injectAssets();

        $html = '<div class="taid-pm" id="taid-pm">'
              . '<p class="taid-pm-help">Each box is a module position. Click the device icons to toggle visibility:'
              . ' <span class="taid-pm-help-on">green</span> = visible,'
              . ' <span class="taid-pm-help-off">red</span> = hidden.'
              . ' The arrows control stacking order on mobile &mdash; higher numbers push a position further down the page'
              . ' when the layout collapses to a single column. Leave at &ldquo;&mdash;&rdquo; for default order.'
              . ' Changes take effect when you save.</p>'
              . '<div class="taid-pm-page">';

        foreach (self::LAYOUT as $row) {
            $html .= $this->renderRow($row, $data);
        }

        $html .= '</div></div>';

        // Hidden input stores the JSON — Joomla saves this as the param value
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $html .= '<input type="hidden" name="' . htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8')
               . '" id="' . htmlspecialchars($this->id, ENT_QUOTES, 'UTF-8')
               . '" value="' . htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . '">';

        return $html;
    }

    protected function getLabel(): string
    {
        return '';
    }

    // ── Private helpers ──────────────────────────────────────────

    private function parseValue(): array
    {
        $data = [];
        if (!empty($this->value)) {
            $decoded = json_decode($this->value, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        // Merge with defaults so every position exists, enforce types, strip unknowns
        $defaults = self::getDefaults();
        foreach ($defaults as $pos => $def) {
            if (!isset($data[$pos]) || !is_array($data[$pos])) {
                $data[$pos] = $def;
            } else {
                $merged = array_merge($def, $data[$pos]);
                $data[$pos] = [
                    'desktop' => (int) ($merged['desktop'] ?? 0),
                    'tablet'  => (int) ($merged['tablet'] ?? 0),
                    'phone'   => (int) ($merged['phone'] ?? 0),
                    'order'   => max(-10, min(10, (int) ($merged['order'] ?? 0))),
                ];
            }
        }

        // Strip unknown position keys
        $data = array_intersect_key($data, $defaults);

        return $data;
    }

    private function injectAssets(): void
    {
        $base    = Uri::root(true) . '/media/templates/site/signal-dark';
        $absBase = JPATH_ROOT . '/media/templates/site/signal-dark';
        $v       = '?v=' . (is_file($absBase . '/css/positionmap.css') ? filemtime($absBase . '/css/positionmap.css') : time());

        /** @var \Joomla\CMS\Document\HtmlDocument $doc */
        $doc = Factory::getDocument();
        $doc->addStyleSheet($base . '/css/positionmap.css' . $v);
        $doc->addScript($base . '/js/positionmap.js' . $v, [], ['defer' => true]);
    }

    private function renderRow(array $row, array $data): string
    {
        $type = $row['type'];
        $html = '<div class="taid-pm-row taid-pm-row--' . $type . '">'
              . '<span class="taid-pm-row-label">' . htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8') . '</span>'
              . '<div class="taid-pm-cells">';

        if ($type === 'content-area') {
            $html .= $this->renderContentArea($row['positions'], $data);
        } else {
            foreach ($row['positions'] as $pos) {
                $html .= $this->renderBox($pos, $data);
            }
        }

        $html .= '</div></div>';
        return $html;
    }

    /**
     * Renders the 3-column content area: sidebar-left | center stack | sidebar-right stack.
     */
    private function renderContentArea(array $positions, array $data): string
    {
        $left   = ['sidebar-left'];
        $center = ['_component', 'ad-article', 'ad-article-row', 'content-bottom', 'main-bottom'];
        $right  = ['sidebar-top', 'sidebar-right', 'ad-sidebar'];

        $html = '<div class="taid-pm-col taid-pm-col--side">';
        foreach ($left as $p) {
            $html .= $this->renderBox($p, $data);
        }
        $html .= '</div>';

        $html .= '<div class="taid-pm-col taid-pm-col--center">';
        foreach ($center as $p) {
            $html .= $this->renderBox($p, $data);
        }
        $html .= '</div>';

        $html .= '<div class="taid-pm-col taid-pm-col--side">';
        foreach ($right as $p) {
            $html .= $this->renderBox($p, $data);
        }
        $html .= '</div>';

        return $html;
    }

    private function renderBox(string $pos, array $data): string
    {
        // _component is a non-interactive marker
        if ($pos === '_component') {
            return '<div class="taid-pm-box taid-pm-box--component">'
                 . '<span class="taid-pm-name">component</span>'
                 . '</div>';
        }

        $cfg = $data[$pos] ?? ['desktop' => 1, 'tablet' => 1, 'phone' => 1, 'order' => 0];

        $html = '<div class="taid-pm-box" data-position="' . htmlspecialchars($pos, ENT_QUOTES, 'UTF-8') . '">';
        $html .= '<span class="taid-pm-name">' . htmlspecialchars($pos, ENT_QUOTES, 'UTF-8') . '</span>';

        // Device toggles
        $html .= '<div class="taid-pm-toggles">';
        foreach (['desktop', 'tablet', 'phone'] as $device) {
            $on = !empty($cfg[$device]);
            $html .= '<button type="button" class="taid-pm-toggle' . ($on ? ' is-on' : '')
                    . '" data-device="' . $device
                    . '" title="' . ucfirst($device) . '">'
                    . self::deviceSvg($device)
                    . '</button>';
        }
        $html .= '</div>';

        // Mobile order
        $order = (int)($cfg['order'] ?? 0);
        $html .= '<div class="taid-pm-order">'
                . '<button type="button" class="taid-pm-order-btn" data-dir="-1" title="Move up">&#9650;</button>'
                . '<span class="taid-pm-order-val" data-order="' . $order . '">'
                . ($order === 0 ? '&mdash;' : $order)
                . '</span>'
                . '<button type="button" class="taid-pm-order-btn" data-dir="1" title="Move down">&#9660;</button>'
                . '</div>';

        $html .= '</div>';
        return $html;
    }

    /**
     * Minimal inline SVG icons — no external icon dependencies.
     */
    private static function deviceSvg(string $device): string
    {
        $attr = 'width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

        return match ($device) {
            'desktop' => '<svg ' . $attr . '><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
            'tablet'  => '<svg ' . $attr . '><rect x="4" y="2" width="16" height="20" rx="2"/><circle cx="12" cy="18" r=".5"/></svg>',
            'phone'   => '<svg ' . $attr . '><rect x="6" y="2" width="12" height="20" rx="2"/><circle cx="12" cy="18" r=".5"/></svg>',
            default   => '',
        };
    }
}
