(function(){
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.querySelector('.nav-links');
    if(hamburger && navLinks){
        hamburger.addEventListener('click',()=>{navLinks.classList.toggle('open');});
        navLinks.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>navLinks.classList.remove('open')));
        document.addEventListener('click',(e)=>{if(!hamburger.contains(e.target) && !navLinks.contains(e.target)) navLinks.classList.remove('open');});
    }
    const animatables = document.querySelectorAll('.menu-card, .story-section, .join-banner, .faq-section');
    if('IntersectionObserver' in window){
        const observer = new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){entry.target.style.opacity='1';entry.target.style.transform='translateY(0)';observer.unobserve(entry.target);}});},{threshold:0.12});
        animatables.forEach(el=>{el.style.opacity='0';el.style.transform='translateY(28px)';el.style.transition='opacity 0.55s ease, transform 0.55s ease'; observer.observe(el);});
    }
    document.querySelectorAll('.menu-card').forEach((c,i)=>c.style.transitionDelay=(i*0.1)+'s');
    const navbar = document.querySelector('.navbar');
    if(navbar) window.addEventListener('scroll',()=>{navbar.style.boxShadow=window.scrollY>40?'0 4px 20px rgba(0,0,0,0.35)':'0 2px 12px rgba(0,0,0,0.25)';},{passive:true});
    window.changeQty = function(change){
        const input = document.getElementById('qty-input');
        if(!input) return;
        let val = parseInt(input.value,10)||1;
        val+=change; if(val<1)val=1; if(val>20)val=20;
        input.value=val;
        updateTotal();
    };
    window.updateSize = function(radio){
        document.querySelectorAll('.size-card').forEach(c=>c.classList.remove('size-active'));
        radio.closest('.size-card')?.classList.add('size-active');
        updateTotal();
    };
    window.updateTotal = function(){
        if(typeof SIZES === 'undefined') return;
        const qtyInput = document.getElementById('qty-input');
        const totalEl = document.getElementById('order-total-price');
        const selected = document.querySelector('input[name="size"]:checked');
        if(!qtyInput || !totalEl || !selected) return;
        const qty = parseInt(qtyInput.value,10)||1;
        const size = selected.value;
        const total = (SIZES[size]||0)*qty;
        totalEl.textContent = '₱'+total;
    };
    updateTotal();
})();