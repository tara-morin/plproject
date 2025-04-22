document.addEventListener('DOMContentLoaded', () => {
  const switchEl = document.getElementById('flexSwitchCheckDefault');
  const schoolEl = document.getElementById('schoolStats');
  const workEl   = document.getElementById('workStats');

  if (!switchEl || !schoolEl || !workEl) {
    console.warn('profileStats.js: missing element(s)', { switchEl, schoolEl, workEl });
    return;
  }

  // arrow‑function
  const updateView = () => {
    if (switchEl.checked) {
      schoolEl.style.display = 'none';
      workEl.style.display   = 'block';
      console.log('Switched to WORK view');
    } else {
      workEl.style.display   = 'none';
      schoolEl.style.display = 'block';
      console.log('Switched to SCHOOL view');
    }
  };

  // initialize
  updateView();

  // arrow callback for the event listener
  switchEl.addEventListener('change', () => updateView());
});

  