document.addEventListener('DOMContentLoaded', function() {
  const switchEl = document.getElementById('flexSwitchCheckDefault');
  const schoolEl = document.getElementById('schoolStats');
  const workEl   = document.getElementById('workStats');

  // Debug: make sure we found them
  if (!switchEl || !schoolEl || !workEl) {
    console.warn('profileStats.js: missing element(s)', {
      switchEl, schoolEl, workEl
    });
    return;
  }

  function updateView() {
    if (switchEl.checked) {
      schoolEl.style.display = 'none';
      workEl.style.display   = 'block';
      console.log('Switched to WORK view');
    } else {
      workEl.style.display   = 'none';
      schoolEl.style.display = 'block';
      console.log('Switched to SCHOOL view');
    }
  }

  // Initialize
  updateView();

  // Re‑run on toggle
  switchEl.addEventListener('change', updateView);
});

  