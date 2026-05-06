jQuery(document).ready(function ($) {
    // Делегирование событий для работы с динамическими элементами (повторяющиеся поля, аккордеоны)
    $(document).on('click', '.wpp-field-type-WPP_Accordion_Field .accordion-button', function () {
        const $button = $(this);
        const $accordion = $button.closest('.wpp-field-type-WPP_Accordion_Field');
        
        // Обновляем атрибуты после клика
        setTimeout(function() {
            const isOpen = $button.attr('aria-expanded') === 'true';
            console.log(`Accordion "${$button.text().trim()}" ${isOpen ? 'открыт' : 'закрыт'}`);
        }, 100);
    });
    
    // Инициализация для существующих аккордеонов
    $('.wpp-field-type-WPP_Accordion_Field').each(function () {
        const $accordion = $(this);
        const $button = $accordion.find('.accordion-button');
        
        // Убеждаемся, что Bootstrap инициализирован
        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
            const target = $button.data('bs-target');
            if (target) {
                new bootstrap.Collapse(target, {
                    toggle: false
                });
            }
        }
    });
});
