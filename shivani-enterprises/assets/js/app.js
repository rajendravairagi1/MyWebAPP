(function () {
  // ---- PWA: register service worker so the browser offers "Add to Home
  // Screen" / "Install app" (Android Chrome needs one registered for this;
  // iOS Safari doesn't need it but it's harmless there). ----
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      var swUrl = document.body.getAttribute('data-sw-url');
      if (swUrl) navigator.serviceWorker.register(swUrl).catch(function () {});
    });
  }

  // ---- Mobile sidebar (hamburger) ----
  var sidebar = document.querySelector('.sidebar');
  var overlay = document.querySelector('.sidebar-overlay');
  var openBtn = document.querySelector('.menu-toggle');
  var closeBtn = document.querySelector('.sidebar-close');

  function openMenu() {
    if (sidebar) sidebar.classList.add('open');
    if (overlay) overlay.classList.add('open');
  }
  function closeMenu() {
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
  }
  if (openBtn) openBtn.addEventListener('click', openMenu);
  if (closeBtn) closeBtn.addEventListener('click', closeMenu);
  if (overlay) overlay.addEventListener('click', closeMenu);
  document.querySelectorAll('.sidebar nav a').forEach(function (a) {
    a.addEventListener('click', closeMenu);
  });

  // ---- Theme switcher ----
  function applyTheme(name) {
    document.documentElement.setAttribute('data-theme', name);
    try { localStorage.setItem('shivani_theme', name); } catch (e) {}
    document.querySelectorAll('.theme-swatch').forEach(function (s) {
      s.classList.toggle('active', s.dataset.theme === name);
    });
    // .theme-dot's background is `var(--brand-grad)` in CSS, so it updates
    // automatically the moment data-theme changes - no JS needed for it.
  }

  document.querySelectorAll('.theme-swatch').forEach(function (btn) {
    btn.addEventListener('click', function () {
      applyTheme(btn.dataset.theme);
      var panel = document.querySelector('.theme-switcher');
      if (panel) panel.removeAttribute('open');
    });
  });

  // Mark the currently active swatch on load (theme itself is already set
  // by the inline script in <head> to avoid a flash of the wrong colors).
  var current = document.documentElement.getAttribute('data-theme') || 'teal';
  applyTheme(current);

  // Close the theme panel when clicking outside it.
  document.addEventListener('click', function (e) {
    var panel = document.querySelector('.theme-switcher');
    if (panel && panel.open && !panel.contains(e.target)) {
      panel.removeAttribute('open');
    }
  });

  // ---- Live validation for mobile / username fields ----
  // Mark a field with data-validate="mobile" or data-validate="username" and
  // it gets: real-time feedback as the user types, and a submit-blocking
  // check (so bad data never reaches the server in the first place).
  // Mobile accepts any format - with spaces/dashes, a leading 0, +91, 91,
  // pasted straight off a contact card - as long as the last 10 digits
  // form a real Indian mobile number (starts 6-9). Most data entry here
  // happens on a phone, so this has to tolerate copy-paste formatting,
  // not just a clean 10-digit string.
  function digitsOnly(v) { return v.replace(/\D+/g, ''); }
  function isValidMobileLoose(v) {
    var digits = digitsOnly(v);
    if (digits.length < 10) return false;
    return /^[6-9]\d{9}$/.test(digits.slice(-10));
  }
  function isValidUsernameLoose(v) {
    return /^[a-zA-Z0-9_.]{3,30}$/.test(v);
  }
  var VALIDATORS = {
    mobile: { test: isValidMobileLoose, msg: 'Sahi 10-digit mobile number dalein (jaise 9876543210) - country code (+91) ke saath bhi chalega.' },
    username: { test: isValidUsernameLoose, msg: 'Username me space allowed nahi - sirf letters, numbers, dot (.) ya underscore (_), 3-30 characters.' },
  };
  function fieldMessageEl(input) {
    var el = input.nextElementSibling;
    if (el && el.classList && el.classList.contains('field-error')) return el;
    el = document.createElement('div');
    el.className = 'field-error';
    input.insertAdjacentElement('afterend', el);
    return el;
  }
  function validateField(input) {
    var rule = VALIDATORS[input.dataset.validate];
    if (!rule) return true;
    var val = input.value.trim();
    var msgEl = fieldMessageEl(input);
    if (val === '') {
      input.classList.remove('invalid');
      msgEl.textContent = '';
      return !input.required;
    }
    if (rule.test(val)) {
      input.classList.remove('invalid');
      msgEl.textContent = '';
      return true;
    }
    input.classList.add('invalid');
    msgEl.textContent = rule.msg;
    return false;
  }
  document.querySelectorAll('[data-validate]').forEach(function (input) {
    input.addEventListener('input', function () { validateField(input); });
    input.addEventListener('blur', function () { validateField(input); });
  });
  // Capture phase so this runs before the double-submit-guard below and
  // before any onsubmit="return confirm(...)" on the form itself - a bad
  // field blocks submission before the user even sees a confirm dialog.
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    var invalid = null;
    form.querySelectorAll('[data-validate]').forEach(function (input) {
      if (!validateField(input) && !invalid) invalid = input;
    });
    if (form.id) {
      document.querySelectorAll('[data-validate][form="' + form.id + '"]').forEach(function (input) {
        if (!validateField(input) && !invalid) invalid = input;
      });
    }
    if (invalid) {
      e.preventDefault();
      invalid.focus();
      invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }, true);

  // ---- Two-step confirmation for permanent/destructive actions (delete) ----
  window.doubleConfirm = function (msg1, msg2) {
    if (!confirm(msg1)) return false;
    return confirm(msg2);
  };

  // ---- Stop double-click / slow-network double submit on every form ----
  // (payments, sales, investor entries, etc. - anywhere a Save/Record
  // button exists). Runs after any onsubmit="return confirm(...)" handler,
  // so a cancelled confirm (e.defaultPrevented) doesn't lock the form.
  document.addEventListener('submit', function (e) {
    if (e.defaultPrevented) return;
    var form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.dataset.submitted === '1') { e.preventDefault(); return; }
    form.dataset.submitted = '1';
    setTimeout(function () {
      form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
        btn.disabled = true;
      });
      if (form.id) {
        document.querySelectorAll('button[form="' + form.id + '"], input[form="' + form.id + '"]').forEach(function (btn) {
          btn.disabled = true;
        });
      }
    }, 0);
  });

  // ---- Share a generated PDF straight to WhatsApp (or any app) ----
  // On phones (Chrome/Android, Safari/iOS over HTTPS) this opens the native
  // share sheet with the PDF already attached - the admin just taps
  // WhatsApp and picks who to send it to. No manual download needed.
  // On desktops/browsers without file-sharing support, it falls back to
  // downloading the PDF and opening a WhatsApp chat with the message
  // pre-filled, so the admin attaches the file manually.
  window.shareFileToWhatsApp = async function (url, filename, text, btn) {
    var originalLabel = btn ? btn.innerHTML : '';
    try {
      if (btn) { btn.disabled = true; btn.textContent = 'Preparing PDF…'; }
      var res = await fetch(url, { credentials: 'same-origin' });
      if (!res.ok) throw new Error('Could not generate the PDF (server error).');
      var blob = await res.blob();
      var file = new File([blob], filename, { type: 'application/pdf' });

      if (navigator.canShare && navigator.canShare({ files: [file] })) {
        await navigator.share({ files: [file], title: filename, text: text });
      } else {
        var blobUrl = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = blobUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(blobUrl); }, 10000);
        window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
        alert('PDF download ho gayi hai aur WhatsApp khul raha hai — chat me PDF file manually attach kar dijiye (is browser me direct file-share support nahi hai; mobile Chrome/Safari par seedha chalta hai).');
      }
    } catch (err) {
      if (err && err.name !== 'AbortError') { // AbortError = user cancelled the share sheet, not an error
        alert('PDF share nahi ho paya: ' + (err && err.message ? err.message : err));
      }
    } finally {
      if (btn) { btn.disabled = false; btn.innerHTML = originalLabel; }
    }
  };
})();
