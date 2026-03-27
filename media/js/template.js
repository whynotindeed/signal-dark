/* The AI Director — template.js */

/* ── Bootstrap accordion collapse — full manual override ─────── *
 * window.bootstrap is not exposed as a global in Joomla 5's
 * frontend (Bootstrap loads as an ES module), so we can't call
 * Collapse.hide(). We take over BOTH open and close in capture
 * phase, implementing Bootstrap's own height-transition sequence
 * so the animation works without needing the Bootstrap JS API.
 *
 * Bootstrap collapse classes:
 *   .collapse          – hidden (display:none unless .show present)
 *   .collapse.show     – visible
 *   .collapsing        – animating (overflow:hidden, height transitions)
 */
document.addEventListener('DOMContentLoaded', () => {
  const DURATION = 350; // matches Bootstrap's default transition

  const collapsePanel = (btn, panel, open) => {
    if (panel.classList.contains('collapsing')) return; // already animating

    btn.setAttribute('aria-expanded', String(open));
    btn.classList.toggle('collapsed', !open);

    if (open) {
      // ── OPEN ─────────────────────────────────
      panel.classList.remove('collapse');
      panel.classList.add('collapsing');
      panel.style.height = '0px';
      const target = panel.scrollHeight;
      requestAnimationFrame(() => requestAnimationFrame(() => {
        panel.style.height = target + 'px';
      }));
      panel.addEventListener('transitionend', () => {
        panel.classList.remove('collapsing');
        panel.classList.add('collapse', 'show');
        panel.style.height = '';
      }, { once: true });

    } else {
      // ── CLOSE ────────────────────────────────
      panel.style.height = panel.scrollHeight + 'px';
      panel.classList.add('collapsing');
      panel.classList.remove('collapse', 'show');
      requestAnimationFrame(() => requestAnimationFrame(() => {
        panel.style.height = '0px';
      }));
      panel.addEventListener('transitionend', () => {
        panel.classList.remove('collapsing');
        panel.classList.add('collapse');
        panel.style.height = '';
      }, { once: true });
    }
  };

  document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
    const targetSel = btn.getAttribute('data-bs-target');
    const panel = targetSel ? document.querySelector(targetSel) : null;
    if (!panel) return;

    btn.addEventListener('click', (e) => {
      e.stopImmediatePropagation();
      e.preventDefault();
      const isExpanded = btn.getAttribute('aria-expanded') === 'true';
      collapsePanel(btn, panel, !isExpanded);
    }, true);
  });
});

/* ── Switcher instant colour feedback ───────────────────────── *
 * Joomla runs server-side validation before adding has-success,
 * causing a multi-second delay. We toggle it immediately on
 * change so the pill colour updates at click speed. */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.switcher').forEach(sw => {
    const positiveRadio = sw.querySelector('input[type="radio"][value="1"]');
    if (!positiveRadio) return;

    const applyState = () => sw.classList.toggle('has-success', positiveRadio.checked);
    applyState(); // sync on load
    sw.querySelectorAll('input[type="radio"]').forEach(r =>
      r.addEventListener('change', applyState)
    );
  });
});

document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  /* ── Sticky nav ─────────────────────────────────────── */
  const nav = document.querySelector('.taid-nav-wrap');
  if (nav) {
    const tick = () => nav.classList.toggle('is-scrolled', window.scrollY > 20);
    tick();
    window.addEventListener('scroll', tick, { passive: true });
  }

  /* ── Reading progress bar ───────────────────────────── */
  const bar = document.getElementById('taid-progress');
  if (bar) {
    const updateBar = () => {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const pct = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;
      bar.style.width = pct + '%';
      bar.setAttribute('aria-valuenow', Math.round(pct));
    };
    window.addEventListener('scroll', updateBar, { passive: true });
    updateBar();
  }

  /* ── Mobile hamburger ───────────────────────────────── */
  const hamburger = document.getElementById('taid-hamburger');
  const menu      = document.getElementById('taid-menu');

  if (hamburger && menu) {
    const toggle = (open) => {
      menu.classList.toggle('is-open', open);
      hamburger.setAttribute('aria-expanded', String(open));
      document.body.style.overflow = open ? 'hidden' : '';
    };

    hamburger.addEventListener('click', (e) => {
      e.stopPropagation(); // prevent document click handler from immediately closing
      const isOpen = menu.classList.contains('is-open');
      toggle(!isOpen);
    });

    // Close on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menu.classList.contains('is-open')) toggle(false);
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      if (menu.classList.contains('is-open') &&
          !menu.contains(e.target) &&
          !hamburger.contains(e.target)) {
        toggle(false);
      }
    });

    // Close when a nav link is clicked
    menu.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => toggle(false));
    });

  }

  /* ── Mobile search toggle ──────────────────────────── */
  const searchToggle = document.getElementById('taid-search-toggle');
  const navSearch    = document.querySelector('.taid-nav-search');

  if (searchToggle && navSearch) {
    searchToggle.addEventListener('click', () => {
      const open = navSearch.classList.toggle('is-open');
      searchToggle.setAttribute('aria-expanded', String(open));
      if (open) {
        const input = navSearch.querySelector('input[type="search"], input[type="text"]');
        if (input) input.focus();
      }
    });

    // Close on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && navSearch.classList.contains('is-open')) {
        navSearch.classList.remove('is-open');
        searchToggle.setAttribute('aria-expanded', 'false');
      }
    });

    // Close when clicking anywhere outside the search form
    document.addEventListener('click', (e) => {
      if (!navSearch.classList.contains('is-open')) return;
      const form = navSearch.querySelector('form');
      if (!searchToggle.contains(e.target) && (!form || !form.contains(e.target))) {
        navSearch.classList.remove('is-open');
        searchToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ── Block-linked article cards + category list rows ── */
  if (document.body.classList.contains('taid-block-links')) {
    document.querySelectorAll('.mod-articles-items li, .mod-tagssimilar li, .latestlist li, .mostread li, .mod-relateditems li').forEach(li => {
      const link = li.querySelector('a');
      if (!link) return;
      li.addEventListener('click', (e) => {
        if (e.target.closest('a')) return;
        link.click();
      });
    });
    document.querySelectorAll('.com-content-category__table tbody tr').forEach(tr => {
      const link = tr.querySelector('.list-title a');
      if (!link) return;
      tr.addEventListener('click', (e) => {
        if (e.target.closest('a')) return;
        link.click();
      });
    });
  }
  /* ── Featured cards — full-card click ── */
  document.querySelectorAll('.taid-feat-card').forEach(card => {
    const link = card.querySelector('.taid-feat-title a');
    if (!link) return;
    card.addEventListener('click', (e) => {
      if (e.target.closest('a')) return;
      link.click();
    });
  });

  /* ── Scroll fade-up ─────────────────────────────────── */
  const targets = document.querySelectorAll(
    '.taid-section, .taid-newsletter, .taid-footer, .taid-hero-stats, .taid-marketplace'
  );

  if ('IntersectionObserver' in window && targets.length) {
    targets.forEach(el => el.classList.add('taid-fade-up'));

    const io = new IntersectionObserver(
      entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.06, rootMargin: '0px 0px -50px 0px' }
    );

    targets.forEach(el => io.observe(el));
  } else {
    targets.forEach(el => el.classList.add('taid-fade-up', 'is-visible'));
  }

  /* ── Code block copy button ────────────────────────── */
  document.querySelectorAll('pre > code').forEach(code => {
    const pre = code.parentElement;
    if (pre.classList.contains('no-copy')) return;
    pre.style.position = 'relative';
    const btn = document.createElement('button');
    btn.className = 'taid-copy-btn';
    btn.textContent = 'Copy';
    btn.addEventListener('click', () => {
      navigator.clipboard.writeText(code.textContent).then(() => {
        btn.textContent = 'Copied';
        setTimeout(() => { btn.textContent = 'Copy'; }, 2000);
      }).catch(() => {
        // fallback
        const ta = document.createElement('textarea');
        ta.value = code.textContent;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        btn.textContent = 'Copied';
        setTimeout(() => { btn.textContent = 'Copy'; }, 2000);
      });
    });
    pre.appendChild(btn);
  });

  /* ── Kunena BBCode code block copy button ─────────────── */
  document.querySelectorAll('#kunena .bbcode_code_head').forEach(head => {
    const body = head.nextElementSibling;
    if (!body || !body.classList.contains('bbcode_code_body')) return;
    const btn = document.createElement('button');
    btn.className = 'taid-copy-btn';
    btn.textContent = 'Copy';
    btn.addEventListener('click', () => {
      navigator.clipboard.writeText(body.textContent).then(() => {
        btn.textContent = 'Copied';
        btn.classList.add('copied');
        setTimeout(() => { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 2000);
      }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = body.textContent;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        btn.textContent = 'Copied';
        btn.classList.add('copied');
        setTimeout(() => { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 2000);
      });
    });
    head.appendChild(btn);
  });

  /* ── Responsive tables — add data-label from headers ── */
  document.querySelectorAll('.taid-component-panel table').forEach(table => {
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    if (!headers.length) return;
    table.querySelectorAll('tbody tr').forEach(tr => {
      tr.querySelectorAll('td').forEach((td, i) => {
        if (headers[i]) td.setAttribute('data-label', headers[i]);
      });
    });
  });

  /* ── Permission-denied hint — "perhaps you are logged out" ── */
  document.querySelectorAll('joomla-alert .alert-message').forEach(msg => {
    if (msg.textContent.includes('permission')) {
      const loginLink = document.querySelector('a[href*="login"]');
      if (loginLink) {
        const hint = document.createElement('span');
        hint.innerHTML = ' Perhaps you are <a href="' + loginLink.href + '" style="color:var(--violet-lt);text-decoration:underline">logged out</a>.';
        msg.appendChild(hint);
      }
    }
  });

  /* ── Back to top ────────────────────────────────────── */
  const backTop = document.getElementById('taid-back-top');
  if (backTop) {
    window.addEventListener('scroll', () => {
      backTop.classList.toggle('is-visible', window.scrollY > 400);
    }, { passive: true });

    backTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

});
