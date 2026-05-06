jQuery(document).ready(function ($) {
    'use strict';

    // Используем делегирование событий для работы с динамически добавленными элементами
    $(document).on('click', '.wpp-repeater-add', function () {
        const $addBtn = $(this);
        const container = $addBtn.closest('.wpp-repeater-container');
        const template = container.find('script[type="text/html"]').first();
        const innerContainer = container.find('.wpp-repeater-inner');
        const max = parseInt($addBtn.data('max')) || 999;

        if (!template.length) {
            return;
        }

        const tmplId = template.attr('id');
        const tmpl = document.getElementById(tmplId);

        if (!tmpl) {
            return;
        }

        const currentCount = innerContainer.children('.wpp-repeater-block').length;

        if (currentCount >= max) {
            return;
        }

        const newIndex = getNextIndex(innerContainer);
        let html = tmpl.innerHTML.replace(/__index__/g, newIndex);

        // Добавляем data-атрибут для отслеживания индекса
        const tempDiv = $('<div>').html(html);
        tempDiv.find('.wpp-repeater-block').attr('data-repeater-index', newIndex);
        html = tempDiv.html();

        innerContainer.append(html);

        // Реинициализируем datepicker для нового поля
        innerContainer.find('.wpp-repeater-block:last input[data-type="date"]').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+10'
        });

        // Перезапускаем автозаполнение адреса, если есть
        if (typeof initGoogleAutocompleteFields === 'function') {
            initGoogleAutocompleteFields();
        }
        
        // Обновляем заголовки супер-аккордеонов после добавления блока
        if (typeof updateAccordionHeaders === 'function') {
            updateAccordionHeaders();
        }
    });

    // Удаление блока - используем делегирование
    $(document).on('click', '.wpp-repeater-remove', function () {
        const $removeBtn = $(this);
        const block = $removeBtn.closest('.wpp-repeater-block');
        const container = block.closest('.wpp-repeater-container');
        const innerContainer = container.find('.wpp-repeater-inner');
        const min = parseInt(container.data('min')) || 1;
        const currentCount = innerContainer.children('.wpp-repeater-block').length;

        // Не удаляем, если достигнут минимум
        if (currentCount <= min) {
            return;
        }

        block.remove();
    });

    // Функция для получения следующего уникального индекса
    function getNextIndex($container) {
        let maxIndex = -1;
        $container.find('[data-repeater-index]').each(function() {
            const index = parseInt($(this).data('repeater-index'));
            if (!isNaN(index) && index > maxIndex) {
                maxIndex = index;
            }
        });
        return maxIndex + 1;
    }

    // Инициализация существующих контейнеров repeater
    $('.wpp-repeater-container').each(function () {
        const container = $(this);
        const innerContainer = container.find('.wpp-repeater-inner');
        const min = parseInt(container.data('min')) || 1;

        // Инициализация существующих блоков (если есть)
        // Блоки уже отрендерены PHP, просто добавляем индексы если их нет
        innerContainer.children('.wpp-repeater-block').each(function(index) {
            if (!$(this).data('repeater-index') && $(this).data('repeater-index') !== 0) {
                $(this).attr('data-repeater-index', index);
            }
        });
    });
});
