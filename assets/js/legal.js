/* =========================================
   ASSETS/JS/LEGAL.JS
========================================= */
document.addEventListener('DOMContentLoaded', () => {

    // 1. Акордеон для FAQ
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', () => {
                // Закрити інші (якщо хочеш, щоб був відкритий тільки один)
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) otherItem.classList.remove('active');
                });
                // Перемкнути поточний
                item.classList.toggle('active');
            });
        }
    });

    // 2. Scrollspy для бокового меню (Table of Contents)
    const sections = document.querySelectorAll('.legal-section');
    const navLinks = document.querySelectorAll('.legal-toc a');

    if (sections.length > 0 && navLinks.length > 0) {
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    }

    // 3. Обробка форми контактів
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = contactForm.querySelector('button[type="submit"]');
            const status = document.getElementById('form-status');

            // Анімація кнопки
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
            btn.disabled = true;
            status.innerHTML = "";

            // Імітація відправки (потім підключимо бекенд)
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                status.innerHTML = "Merci ! Votre message a bien été envoyé.";
                status.style.color = "#4CAF50";
                contactForm.reset();
            }, 1500);
        });
    }
});