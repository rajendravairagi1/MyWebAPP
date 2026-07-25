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
