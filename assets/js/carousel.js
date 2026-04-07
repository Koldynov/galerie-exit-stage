document.addEventListener('DOMContentLoaded', () => {
    const carousels = document.querySelectorAll('.carousel');

    carousels.forEach(carousel => {
        const wrapper = carousel.parentElement;
        const btnPrev = wrapper.querySelector('.nav-btn.prev');
        const btnNext = wrapper.querySelector('.nav-btn.next');

        // Функція для оновлення видимості кнопок
        const updateButtons = () => {
            const scrollLeft = carousel.scrollLeft;
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;

            // Ліва стрілка: показуємо, якщо ми відійшли від початку хоча б на 10px
            if (scrollLeft > 10) {
                btnPrev.classList.add('visible');
            } else {
                btnPrev.classList.remove('visible');
            }

            // Права стрілка: ховаємо, якщо ми дісталися кінця
            if (scrollLeft >= maxScroll - 10) {
                btnNext.classList.remove('visible');
            } else {
                btnNext.classList.add('visible');
            }
        };

        // Гортання вправо
        btnNext.addEventListener('click', () => {
            carousel.scrollBy({ left: 350, behavior: 'smooth' });
        });

        // Гортання вліво
        btnPrev.addEventListener('click', () => {
            carousel.scrollBy({ left: -350, behavior: 'smooth' });
        });

        // Слідкуємо за скролом, щоб перемикати стрілки
        carousel.addEventListener('scroll', updateButtons);
        
        // Викликаємо один раз при завантаженні
        updateButtons();
    });
});