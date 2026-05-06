(function ($) {
    'use strict';

    $(document).ready(function () {
        // Инициализация условной логики при загрузке страницы
        handleConditionalFields();

        // Обновление видимости полей при изменении других полей
        $(document).on('input change', '.wpp-field input, .wpp-field select, .wpp-field textarea', function () {
            handleConditionalFields();
        });

        /**
         * Получить значение поля по имени
         *
         * @param {string} fieldName - Имя поля
         * @returns {string}
         */
        function getFieldValue(fieldName) {
            const $input = $(`[name="${fieldName}"]`);

            if ($input.length === 0) return '';

            // Игнорировать отключённые поля
            if ($input.prop('disabled')) {
                return '';
            }

            const value = $input.val();

            // Очистить денежные поля от лишних символов
            if ($input.closest('.wpp-field').find('[data-type="money"]').length > 0) {
                return value.replace(/[^0-9.]/g, '');
            }

            return value;
        }

        /**
         * Обработка условного отображения полей
         */
        function handleConditionalFields() {
            const allFields = $('.wpp-field');

            allFields.each(function () {
                const field = $(this);
                const conditionData = field.attr('data-condition');
                const compareType = field.attr('data-compare') || '=';

                if (!conditionData) return;

                try {
                    const conditions = JSON.parse(conditionData);
                    let show = true;

                    $.each(conditions, function (key, expectedValues) {
                        const fieldValue = getFieldValue(key);

                        if (Array.isArray(expectedValues)) {
                            const matches = expectedValues.includes(fieldValue);
                            show = compareType === '!=' ? !matches : matches;
                        } else {
                            const matches = String(fieldValue) === String(expectedValues);
                            show = compareType === '!=' ? !matches : matches;
                        }

                        if (!show) return false;
                    });

                    // Переключить видимость поля и состояние disabled
                    field.toggle(show);
                    field.find('input, select, textarea').prop('disabled', !show);

                } catch (e) {
                    console.error('WPP Field Builder: Ошибка условной логики:', conditionData, e);
                }
            });
        }

        /**
         * Кастомная валидация формы
         */
        $('form.wpp-custom-form').on('submit', function (e) {
            let isValid = true;

            $('.wpp-field [required]').each(function () {
                const value = $(this).val();
                if (!value) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля.');
            }
        });
    });

})(jQuery);