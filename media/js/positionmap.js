/**
 * Signal Dark — Visual Position Manager
 *
 * Handles toggle and order interactions in the admin field.
 * Single delegated listener, zero dependencies.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', init);

  function init() {
    var container = document.getElementById('taid-pm');
    if (!container) return;

    // The hidden input is the next sibling of the container
    var hidden = container.nextElementSibling;
    if (!hidden || hidden.type !== 'hidden') {
      // Fallback: search by name pattern
      hidden = document.querySelector('input[type="hidden"][name*="positionMap"]');
    }
    if (!hidden) return;

    var state = parseState(hidden.value);

    container.addEventListener('click', function (e) {
      var toggle = e.target.closest('.taid-pm-toggle');
      if (toggle) {
        handleToggle(toggle, state, hidden);
        return;
      }

      var orderBtn = e.target.closest('.taid-pm-order-btn');
      if (orderBtn) {
        handleOrder(orderBtn, state, hidden);
        return;
      }
    });
  }

  /**
   * Parse JSON from the hidden input, return an object.
   */
  function parseState(raw) {
    try {
      var parsed = JSON.parse(raw);
      if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
        return parsed;
      }
    } catch (e) {
      // Ignore parse errors — defaults will be used
    }
    return {};
  }

  /**
   * Toggle a device visibility on/off.
   */
  function handleToggle(btn, state, hidden) {
    var box = btn.closest('.taid-pm-box');
    if (!box) return;

    var pos    = box.getAttribute('data-position');
    var device = btn.getAttribute('data-device');
    var DEVICES = { desktop: 1, tablet: 1, phone: 1 };
    if (!pos || !device || !DEVICES[device] || !state[pos]) return;

    // Flip the value
    var current = state[pos][device];
    state[pos][device] = current ? 0 : 1;

    // Update visual state
    if (state[pos][device]) {
      btn.classList.add('is-on');
    } else {
      btn.classList.remove('is-on');
    }

    syncHidden(state, hidden);
  }

  /**
   * Increment or decrement the mobile order value.
   */
  function handleOrder(btn, state, hidden) {
    var box = btn.closest('.taid-pm-box');
    if (!box) return;

    var pos = box.getAttribute('data-position');
    var dir = parseInt(btn.getAttribute('data-dir'), 10);
    if (!pos || isNaN(dir) || !state[pos]) return;

    var val = (state[pos].order || 0) + dir;

    // Clamp to -10..10
    if (val < -10) val = -10;
    if (val > 10) val = 10;

    state[pos].order = val;

    // Update display
    var span = box.querySelector('.taid-pm-order-val');
    if (span) {
      span.textContent = val === 0 ? '\u2014' : String(val);
      span.setAttribute('data-order', String(val));
    }

    syncHidden(state, hidden);
  }

  /**
   * Serialize state back to the hidden input.
   */
  function syncHidden(state, hidden) {
    hidden.value = JSON.stringify(state);
  }
})();
