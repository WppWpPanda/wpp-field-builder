(function ($) {
    // Функция для обработки кнопок
    function handleButtonGroup(group) {
        const input = group.find('input[type="hidden"]');
        const selectedValue = input.val();

        if (selectedValue) {
            group.find('.btn').removeClass('active');
            group.find(`[data-value="${selectedValue}"]`).addClass('active');
        }

        // Обработка кликов по кнопкам
        group.find('.btn').off('click.wpp').on('click.wpp', function(e) {
            e.preventDefault();
            const button = $(this);
            const value = button.data('value');

            input.val(value);
            group.find('.btn').removeClass('active');
            button.addClass('active');
        });
    }

    // Обработка существующих элементов при загрузке
    $(document).ready(function () {
        $('.wpp-button-group').each(function () {
            handleButtonGroup($(this));
        });

        // Настройка MutationObserver для отслеживания динамических изменений
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                $(mutation.addedNodes).each(function() {
                    const node = $(this);

                    // Проверяем добавленные элементы
                    if (node.hasClass('wpp-button-group')) {
                        handleButtonGroup(node);
                    }

                    // Проверяем дочерние элементы
                    node.find('.wpp-button-group').each(function() {
                        handleButtonGroup($(this));
                    });
                });
            });
        });

        // Начинаем наблюдение за изменениями в body
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

})(jQuery);