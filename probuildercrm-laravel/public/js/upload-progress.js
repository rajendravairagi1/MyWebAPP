// Shows a live percentage bar while a form with a file input uploads, by
// sending the same multipart form data over XMLHttpRequest (which exposes
// upload progress events) instead of a plain form submit. When the upload
// finishes, the browser is sent to wherever the server would normally have
// redirected — so success/validation-error handling stays identical to a
// normal form submit, we just get progress feedback in between.
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form[data-upload-progress]').forEach(function (form) {
    var fileInput = form.querySelector('input[type="file"]');
    var wrap = form.querySelector('[data-upload-progress-wrap]');
    var bar = form.querySelector('[data-upload-progress-bar]');
    var label = form.querySelector('[data-upload-progress-label]');
    if (!fileInput || !wrap || !bar || !label) return;

    form.addEventListener('submit', function (e) {
      if (!fileInput.files || !fileInput.files.length) return;

      e.preventDefault();

      var submitButton = form.querySelector('button[type="submit"]');
      if (submitButton) submitButton.disabled = true;

      wrap.hidden = false;
      bar.style.width = '0%';
      label.textContent = 'Uploading… 0%';

      var xhr = new XMLHttpRequest();
      xhr.open(form.method || 'POST', form.action, true);

      xhr.upload.addEventListener('progress', function (evt) {
        if (!evt.lengthComputable) return;
        var pct = Math.round((evt.loaded / evt.total) * 100);
        bar.style.width = pct + '%';
        label.textContent = 'Uploading… ' + pct + '%';
      });

      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          bar.style.width = '100%';
          // XHR already followed the server's redirect, so xhr.responseText
          // is the final page — including the one-time "saved" flash
          // message. Render it directly instead of navigating again: a
          // second navigation would hit the page fresh and miss that
          // flash, since Laravel only keeps it for a single request.
          if (xhr.responseURL) {
            history.replaceState(null, '', xhr.responseURL);
          }
          document.open();
          document.write(xhr.responseText);
          document.close();
        } else {
          if (submitButton) submitButton.disabled = false;
          label.textContent = 'Something went wrong — please try again.';
        }
      };

      xhr.onerror = function () {
        if (submitButton) submitButton.disabled = false;
        label.textContent = 'Upload failed — check your connection and try again.';
      };

      xhr.send(new FormData(form));
    });
  });
});
