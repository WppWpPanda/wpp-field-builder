(function ($) {
    'use strict';

    // Делаем функцию доступной глобально для вызова из других скриптов (например, repeater)
    window.updateAccordionHeaders = function() {
        $('.wpp-super-accordion').each(function () {
            const $accordion = $(this);
            const headerTemplate = $accordion.attr('data-header');

            if (!headerTemplate) return;

            const fields = $accordion.find('input, select, textarea');
            const data = {};

            fields.each(function () {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (name) {
                    // Используем только имя поля без индексов repeater для шаблона
                    const cleanName = name.replace(/\[\d+\]/g, '').replace(/\[\]/g, '');
                    data[cleanName] = '<span>' + (value || '') + '</span>';
                }
            });

            const $titleEl = $accordion.find('.wpp-super-accordion-header h5');

            const parsedHeader = headerTemplate.replace(/\{([a-zA-Z0-9_]+)\}/g, function (_, key) {
                return data[key] || key;
            });

            if ($titleEl.length && parsedHeader) {
                $titleEl.html(parsedHeader);
            }
        });
    };

    // Вызываем при изменении полей внутри аккордеона - используем делегирование
    $(document).on('input', '.wpp-super-accordion input, .wpp-super-accordion select, .wpp-super-accordion textarea', function () {
        updateAccordionHeaders();
    });

    $(document).ready(function () {
        // Инициализация заголовков при загрузке
        updateAccordionHeaders();

        // Обработчик клика на заголовок аккордеона - используем делегирование
        $(document).on('click', '.wpp-super-accordion-header', function (e) {
            // Предотвращаем срабатывание при клике на input/select внутри заголовка
            if ($(e.target).is('input, select, textarea, button, a')) {
                return;
            }

            const $header = $(this);
            const accordion = $header.closest('.wpp-super-accordion');
            const body = accordion.find('.wpp-super-accordion-body');
            const icon = accordion.find('.toggle-icon');

            if (body.is(':visible')) {
                body.slideUp(200);
                icon.html('▶');
                accordion.removeClass('open');
            } else {
                body.slideDown(200);
                icon.html('▼');
                accordion.addClass('open');
            }
        });
    });

})(jQuery);
