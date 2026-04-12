document.addEventListener('DOMContentLoaded', () => {
    
    // Створюємо спостерігача
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15 // Елемент почне анімацію, коли з'явиться на 15% екрану
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Додаємо клас .active, коли елемент ВИДНО (запускає анімацію CSS)
                entry.target.classList.add('active');
            } else {
                // ПРИБИРАЄМО клас, коли елемент ХОВАЄТЬСЯ
                // Це дозволяє анімації відпрацювати ЗНОВУ при наступному скролі!
                entry.target.classList.remove('active');
            }
        });
    }, observerOptions);

    // Знаходимо всі елементи з класом .reveal і вішаємо на них спостерігача
    const revealElements = document.querySelectorAll('.reveal');
    revealElements.forEach(el => observer.observe(el));

    // Додаємо трохи "магії" для карток у каруселі
    const cards = document.querySelectorAll('.carousel-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
});