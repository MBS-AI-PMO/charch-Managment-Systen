import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import './ckeditor-init.js';
import { registerAlertStore } from './alert.js';

window.Alpine = Alpine;
window.Sortable = Sortable;

registerAlertStore(Alpine);
Alpine.start();

// Scroll-reveal: observe any element with class "reveal" (or its variants)
// and add `is-in` when it enters the viewport. CSS handles the transition.
const startReveal = () => {
    const els = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-zoom');
    if (!els.length) return;

    if (!('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('is-in'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -8% 0px' },
    );

    // Show already-visible sections immediately so reload doesn't flash blank → styled.
    els.forEach((el) => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight * 0.92 && rect.bottom > 0) {
            el.classList.add('is-in');
        } else {
            observer.observe(el);
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startReveal);
} else {
    startReveal();
}
