document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('search-form');
  if (!form) return;

  var statusEl = document.getElementById('search-status');
  var resultsEl = document.getElementById('search-results');

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str == null ? '' : str;
    return div.innerHTML;
  }

  function renderResults(results) {
    if (!results.length) {
      resultsEl.innerHTML = '<p class="muted">No results found.</p>';
      return;
    }
    resultsEl.innerHTML = results.map(function (r) {
      return (
        '<div class="result-card" data-place-id="' + escapeHtml(r.place_id) + '">' +
        '<div>' +
        '<strong>' + escapeHtml(r.name) + '</strong><br>' +
        '<span class="muted small">' + escapeHtml(r.address) + '</span><br>' +
        '<span class="small">Rating: ' + (r.rating != null ? r.rating : 'N/A') + ' &middot; Reviews: ' + r.reviews_count + '</span>' +
        '</div>' +
        '<div><button class="btn btn-sm save-lead-btn" type="button">Analyze &amp; Save</button></div>' +
        '</div>'
      );
    }).join('');
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var query = document.getElementById('search-query').value.trim();
    if (!query) return;
    statusEl.textContent = 'Searching...';
    resultsEl.innerHTML = '';

    fetch('api/places_search.php?q=' + encodeURIComponent(query))
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data.ok) {
          statusEl.textContent = 'Error: ' + data.error;
          return;
        }
        var msg = data.results.length + ' weak lead(s) shown for "' + query + '"';
        if (data.stats) {
          msg += ' — Google returned ' + data.stats.total_from_google
               + ', ' + data.stats.weak_matches + ' matched filter (reviews < ' + data.stats.min_reviews
               + ' or rating < ' + data.stats.min_rating + '), top ' + data.stats.shown + ' shown.';
        }
        statusEl.textContent = msg;
        if (data.results.length === 0) {
          resultsEl.innerHTML = '<p class="muted">Koi weak lead nahi mila is search me. Filter loose karna ho to Settings se threshold badha do.</p>';
        } else {
          renderResults(data.results);
        }
        resultsEl.dataset.query = query;
      })
      .catch(function () {
        statusEl.textContent = 'Something went wrong. Please try again.';
      });
  });

  resultsEl.addEventListener('click', function (e) {
    if (!e.target.classList.contains('save-lead-btn')) return;
    var card = e.target.closest('.result-card');
    var placeId = card.dataset.placeId;
    var query = resultsEl.dataset.query || '';

    e.target.disabled = true;
    e.target.textContent = 'Saving...';

    var body = new URLSearchParams();
    body.set('place_id', placeId);
    body.set('search_query', query);
    body.set('csrf_token', window.CSRF_TOKEN);

    fetch('api/save_lead.php', { method: 'POST', body: body })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data.ok) {
          alert('Error: ' + data.error);
          e.target.disabled = false;
          e.target.textContent = 'Analyze & Save';
          return;
        }
        e.target.textContent = 'Saved ✓';
        e.target.classList.add('btn-success');
        var link = document.createElement('a');
        link.href = 'lead-detail.php?id=' + data.lead_id;
        link.className = 'btn btn-sm btn-outline';
        link.style.marginLeft = '8px';
        link.textContent = 'View Lead';
        e.target.after(link);
      })
      .catch(function () {
        alert('Something went wrong while saving.');
        e.target.disabled = false;
        e.target.textContent = 'Analyze & Save';
      });
  });
});
