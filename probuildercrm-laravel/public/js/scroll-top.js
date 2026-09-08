document.addEventListener('DOMContentLoaded', function () {
  var btn = document.getElementById('scroll-top-btn');
  if (!btn) return;

  function toggle() {
    if (window.scrollY > 400) {
      btn.classList.add('is-visible');
    } else {
      btn.classList.remove('is-visible');
    }
  }

  window.addEventListener('scroll', toggle, { passive: true });
  toggle();

  btn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
});
