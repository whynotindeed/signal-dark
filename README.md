# Signal Dark — Joomla Template

A dark editorial template for Joomla 5/6. Every component view styled for dark mode — articles, categories, contacts, tags, search, news feeds, user views, and more.

**[Live Demo](https://demo.theaidirector.win)** · **[Getting Started](https://demo.theaidirector.win/signal-dark/getting-started)**


## Features

- **Dark theme** — near-black `#0a0a0f` background, violet `#7c3aed` and cyan `#06b6d4` accents
- **36 module positions** — hero, featured, sidebar, footer, ad slots, newsletter, marketplace, and more
- **Typography** — Space Grotesk (headings), Inter (body), Space Mono (code) — all self-hosted, no external requests, GDPR-friendly
- **Every Joomla view styled** — articles, categories, contacts, tags, Smart Search, news feeds, user registration/login/profile
- **Reading progress bar** — animated top bar shows scroll position
- **Responsive** — mobile-first layout, hamburger nav, touch-friendly
- **CSS variables** — change colors, fonts, spacing from one place
- **Custom error page** — styled 404 with navigation back to home
- **Dark mode form controls** — inputs, selects, checkboxes all styled
- **Joomla 6 ready** — tested on Joomla 5.4+ and 6.0+

## Install

1. Download the [latest release](https://github.com/whynotindeed/signal-dark/releases) or clone this repo
2. Zip the contents (or use the release zip)
3. In Joomla admin: **Extensions → Install → Upload Package File**
4. Go to **System → Templates → Styles** and set Signal Dark as default

Or install via CLI:
```bash
# Copy files into your Joomla installation
cp -r templates/signal-dark /path/to/joomla/templates/
cp -r media/* /path/to/joomla/media/templates/site/signal-dark/

# Register and enable
php cli/joomla.php extension:discover
```

## Module Positions

| Position | Purpose |
|----------|---------|
| `menu` | Main navigation |
| `search` | Search bar in nav |
| `hero` | Hero content area |
| `featured` | Featured article cards |
| `top-a` / `top-b` | Two-column content section |
| `sidebar-right` | Article sidebar |
| `sidebar-left` | Left sidebar |
| `ad-banner` / `ad-sidebar` | Ad positions |
| `footer-a` / `footer-b` / `footer-c` | Three-column footer |

Full list: 36 positions declared in `templateDetails.xml`.

## CSS Customization

Edit `media/css/custom.css` for overrides that survive template updates. The main styles are in `media/css/template.css` — all colors use CSS custom properties defined in `:root`.

Key variables:
```css
:root {
  --bg:        #0a0a0f;     /* page background */
  --bg2:       #0f0f1a;     /* secondary background */
  --panel:     #141420;     /* card/panel background */
  --text:      #f0f0ff;     /* primary text */
  --text-soft: rgba(240, 240, 255, 0.76);
  --violet:    #7c3aed;     /* primary accent */
  --cyan:      #06b6d4;     /* secondary accent */
}
```

## Requirements

- Joomla 5.4+ or Joomla 6.0+
- PHP 8.1+

## License

GPL-2.0-or-later — same as Joomla itself.

## Credits

Built by **[The AI Director](https://theaidirector.win)** — tools for building Joomla sites with AI.

The entire template — CSS, PHP, JavaScript — was written conversationally using [Claude Code](https://claude.ai/claude-code) in VS Code. Want to build your own Joomla site the same way? Check out the [AI Joomla Starter Kit](https://theaidirector.win/shop).
