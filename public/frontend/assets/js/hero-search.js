window.toursSearch = function(){
  return {
    destination:'', destination_id:'', date:'', language:'',
    selectDestination(id, name){ this.destination_id=id; this.destination=name; },
    selectLanguage(lang){ this.language=lang; },
    submit(){
      const p = new URLSearchParams({
        type:'tour',
        destination:this.destination,
        destination_id:this.destination_id,
        date:this.date,
        check_in:this.date, // compat vechi
        language:this.language
      });
      const to = document.querySelector('meta[name="tours-list-route"]')?.content || (location.origin + '/tours');
      window.location.href = `${to}?` + p.toString();
    }
  };
};

document.addEventListener('DOMContentLoaded', function(){
  const root = document.getElementById('hero-search');
  if(!root) return;

  // culoare pastile = culoarea titlurilor
  const t = root.querySelector('.tg-booking-form-title');
  if(t){ root.style.setProperty('--pill-color', getComputedStyle(t).color); }

  // flyout portal
  const flyout = document.createElement('div');
  flyout.id = 'pill-flyout';
  document.body.appendChild(flyout);

  function htmlFromTemplate(sel){
    const tpl = document.querySelector(sel);
    return tpl ? tpl.innerHTML.trim() : '';
  }
  function openFlyout(btn){
    const sel = btn.getAttribute('data-menu');
    const html = htmlFromTemplate(sel);
    if(!html) return;
    flyout.innerHTML = `<div class="pill-menu">${html}</div>`;
    flyout.style.display = 'block';
    const r = btn.getBoundingClientRect();
    const w = flyout.offsetWidth || 260;
    const margin = 8;
    let left = Math.min(Math.max(margin, r.left), window.innerWidth - w - margin);
    flyout.style.left = left + 'px';
    flyout.style.top  = (r.bottom + 6) + 'px';
    flyout.querySelectorAll('a').forEach(a => a.addEventListener('click', () => closeFlyout()));
  }
  function closeFlyout(){ flyout.style.display='none'; flyout.innerHTML=''; }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#hero-search [data-pill]');
    if (btn){ openFlyout(btn); return; }
    if (!e.target.closest('#pill-flyout')) closeFlyout();
  });
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeFlyout(); });

  // click pe toată capsula de dată => calendar nativ
  root.addEventListener('click', function(e){
    const box = e.target.closest('.tg-booking-add-input-date');
    if(!box) return;
    const input = box.querySelector('input[type="date"]');
    if(!input) return;
    input.focus(); if (input.showPicker) input.showPicker();
  }, true);
});
