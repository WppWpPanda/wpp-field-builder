/**
 * WPP Fields Block Field JavaScript
 * 
 * Этот файл может использоваться для инициализации полей внутри блока полей
 * если требуется дополнительная JS-логика
 */
jQuery(document).ready(function ($) {
    'use strict';

    // Инициализация блоков полей
    $('.wpp-field-type-WPP_Fields_Block_Field').each(function () {
        const $block = $(this);
        
        // Здесь можно добавить логику для специфичных полей внутри блока
        // Например, инициализация datepicker, select2 и т.д.
    });
    
    // Делегирование событий для динамически добавленных блоков полей
    // (если блоки полей используются внутри repeater)
    $(document).on('wpp-fields-block-initialized', '.wpp-field-type-WPP_Fields_Block_Field', function () {
        const $block = $(this);
        // Логика пост-инициализации
    });
});
