// Dark mode
(function(){
  const key='notes_theme';
  const saved = localStorage.getItem(key) || 'light';
  document.documentElement.setAttribute('data-theme', saved);
  window.toggleTheme = () => {
    const cur = document.documentElement.getAttribute('data-theme');
    const next = cur === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem(key, next);
  };
})();

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click', e=>{
    const id = a.getAttribute('href'); if(id.length<2) return;
    const el = document.querySelector(id);
    if(el){ e.preventDefault(); el.scrollIntoView({behavior:'smooth'}); }
  });
});

// AOS-like reveal
const io = new IntersectionObserver(entries=>{
  entries.forEach(en=>{ if(en.isIntersecting){ en.target.classList.add('aos-in'); io.unobserve(en.target); }});
},{threshold:.15});
document.querySelectorAll('[data-aos]').forEach(el=>{
  el.style.opacity=0; el.style.transform='translateY(20px)';
  el.style.transition='opacity .6s ease, transform .6s ease';
  io.observe(el);
});
const style = document.createElement('style');
style.textContent = '.aos-in{opacity:1 !important;transform:none !important}';
document.head.appendChild(style);

// Live search
window.liveSearch = function(input, selector){
  input.addEventListener('input', ()=>{
    const q = input.value.toLowerCase();
    document.querySelectorAll(selector).forEach(card=>{
      card.style.display = card.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
};
