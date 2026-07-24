(function () {
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
})();
