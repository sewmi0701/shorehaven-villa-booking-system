'use strict';
(() => {
  // Local SVG fallback when Unsplash cannot be loaded (e.g., offline).
  document.querySelectorAll('img[data-fallback]').forEach(img => {
    img.addEventListener('error', () => {
      const fallback = img.dataset.fallback;
      if (fallback && img.getAttribute('src') !== fallback) img.src = fallback;
    }, {once: true});
    // An image may have failed before this script ran (e.g. offline).
    if (img.complete && img.naturalWidth === 0) {
      const fallback = img.dataset.fallback;
      if (fallback && img.getAttribute('src') !== fallback) img.src = fallback;
    }
  });

  const menuButton = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.site-nav');
  if (menuButton && nav) menuButton.addEventListener('click', () => {
    const opening = !nav.classList.contains('is-open');
    nav.classList.toggle('is-open', opening);
    menuButton.setAttribute('aria-expanded', String(opening));
    menuButton.textContent = opening ? '✕' : '☰';
  });

  const dateToUTC = value => /\d{4}-\d{2}-\d{2}/.test(value) ? Date.parse(`${value}T00:00:00Z`) : NaN;
  const addDay = value => {
    const timestamp = dateToUTC(value);
    return Number.isFinite(timestamp) ? new Date(timestamp + 86400000).toISOString().slice(0, 10) : '';
  };
  const formatLKR = n => 'LKR ' + new Intl.NumberFormat('en-LK', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(n);
  const today = new Date();
  const localToday = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

  document.querySelectorAll('form').forEach(form => {
    const inField = form.querySelector('input[name="check_in"]');
    const outField = form.querySelector('input[name="check_out"]');
    if (!inField || !outField) return;
    const priceHolder = form.querySelector('.estimate') || form.closest('[data-night-price]')?.querySelector('.estimate');
    const priceSource = form.getAttribute('data-night-price') || form.closest('[data-night-price]')?.getAttribute('data-night-price');
    const price = Number(priceSource);
    const update = () => {
      inField.min = localToday;
      const next = addDay(inField.value || localToday);
      outField.min = next;
      if (inField.value && outField.value && outField.value < next) outField.value = '';
      const nights = (dateToUTC(outField.value) - dateToUTC(inField.value)) / 86400000;
      if (priceHolder && price > 0) {
        priceHolder.textContent = Number.isInteger(nights) && nights > 0 && nights <= 60
          ? `${nights} night${nights > 1 ? 's' : ''} × ${formatLKR(price)} = ${formatLKR(nights * price)}`
          : 'Choose valid dates to see your estimated total';
      }
    };
    inField.addEventListener('change', update);
    outField.addEventListener('change', update);
    update();
    form.addEventListener('submit', evt => {
      if (!inField.value && !outField.value && !inField.required && !outField.required) return;
      const diff = (dateToUTC(outField.value) - dateToUTC(inField.value)) / 86400000;
      if (!inField.value || !outField.value || !Number.isInteger(diff) || diff <= 0 || diff > 60) {
        evt.preventDefault();
        alert('Please select a valid check-in and check-out period (up to 60 nights).');
      }
    });
  });

  document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
  }));

  // Optional live endpoint preview for room details: never replaces server-side booking checks.
  const widget = document.querySelector('.booking-widget[data-night-price]');
  const apiMeta = document.querySelector('meta[name="availability-api"]');
  if (widget && apiMeta) {
    const form = widget.querySelector('form');
    const dates = [form.querySelector('[name="check_in"]'), form.querySelector('[name="check_out"]')];
    const roomId = new URLSearchParams(form.action.split('?')[1] || '').get('room_id') || form.querySelector('[name="room_id"]')?.value;
    const notice = document.createElement('div');
    notice.className = 'availability-notice';
    notice.setAttribute('aria-live', 'polite');
    notice.style.fontSize = '.76rem';
    notice.style.margin = '-5px 0 4px';
    form.insertBefore(notice, form.querySelector('button'));
    let latest = 0;
    const check = async () => {
      const [checkIn, checkOut] = dates.map(el => el.value);
      notice.textContent = '';
      if (!checkIn || !checkOut || checkOut <= checkIn) return;
      const requestId = ++latest;
      try {
        const params = new URLSearchParams({check_in: checkIn, check_out: checkOut, guests: form.querySelector('[name="guests"]').value});
        const response = await fetch(`${apiMeta.content}?${params.toString()}`, {headers: {'Accept':'application/json'}});
        const data = await response.json();
        if (requestId !== latest) return;
        notice.textContent = !data.ok ? data.message : data.room_ids.includes(Number(roomId)) ? '✓ Currently open for these dates — confirmation required' : 'This room is unavailable for those dates.';
        notice.style.color = data.ok && data.room_ids.includes(Number(roomId)) ? '#287a5f' : '#9c493d';
      } catch { /* A network error must not prevent the normal booking form. */ }
    };
    [...dates, form.querySelector('[name="guests"]')].forEach(el => el?.addEventListener('change', check));
    check();
  }
})();
