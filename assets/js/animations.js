document.addEventListener('DOMContentLoaded', () => {
    // Знаходимо всі елементи, які треба анімувати
    const reveals = document.querySelectorAll('.reveal');

    // Налаштування "спостерігача"
    const observerOptions = {
        root: null,
        threshold: 0.15, // Анімація спрацює, коли видно 15% блоку
        rootMargin: "0px 0px -50px 0px" // Спрацьовує трохи до того, як блок торкнеться низу екрана
    };

    const scrollObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Додаємо клас active для запуску CSS анімації
                entry.target.classList.add('active');
                // Припиняємо стежити (щоб анімація не повторювалась щоразу при скролі туди-сюди)
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Вішаємо спостерігача на кожен елемент
    reveals.forEach(reveal => {
        scrollObserver.observe(reveal);
    });
});