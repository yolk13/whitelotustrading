document.addEventListener('DOMContentLoaded', function() {
    initMobileNav();
    initScrollReveal();
});

function initMobileNav() {
    const menuBtn = document.querySelector('.md\\:hidden');
    const mobileNav = document.getElementById('mobileNav');
    if (menuBtn && mobileNav) {
        menuBtn.addEventListener('click', function() {
            mobileNav.classList.toggle('hidden');
        });
    }
}

function initScrollReveal() {
    const observerOptions = { threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('section').forEach(section => {
        section.style.opacity = '0';
        observer.observe(section);
    });
}

function switchTab(category) {
    const hvacGrid = document.getElementById('grid-hvac');
    const wellnessGrid = document.getElementById('grid-wellness');
    const btnHvac = document.getElementById('btn-hvac');
    const btnWellness = document.getElementById('btn-wellness');

    if (!hvacGrid || !wellnessGrid || !btnHvac || !btnWellness) return;

    if (category === 'hvac') {
        hvacGrid.classList.remove('hidden');
        wellnessGrid.classList.add('hidden');
        btnHvac.classList.add('tab-active');
        btnHvac.classList.remove('text-on-surface-variant');
        btnWellness.classList.remove('tab-active');
        btnWellness.classList.add('text-on-surface-variant');
    } else {
        hvacGrid.classList.add('hidden');
        wellnessGrid.classList.remove('hidden');
        btnWellness.classList.add('tab-active');
        btnWellness.classList.remove('text-on-surface-variant');
        btnHvac.classList.remove('tab-active');
        btnHvac.classList.add('text-on-surface-variant');
    }
}

function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    let container = modal.querySelector('#modalContainer');
    if (!container) container = modal.querySelector('#postModalContainer');
    if (!container) container = modal.querySelector('div:first-child + div');

    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        if (container) {
            container.classList.remove('translate-x-full');
            container.classList.add('translate-x-0');
        }
    } else {
        if (container) {
            container.classList.remove('translate-x-0');
            container.classList.add('translate-x-full');
        }
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
}
