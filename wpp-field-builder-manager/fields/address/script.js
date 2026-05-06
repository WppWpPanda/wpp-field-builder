jQuery(document).ready(function ($) {
    'use strict';

   // console.log('WPP Address Field: Google Places Autocomplete запущен');

    function initGoogleAutocompleteFields() {
        $('.wpp-wpp_address_field').each(function () {
            // Проверяем, является ли элемент input или содержит его внутри
            const input = this.tagName === 'INPUT' ? this : this.querySelector('input[type="text"]');

            if (!input) {
               // console.warn('⚠️ Элемент не содержит <input type="text"> — пропускаем', this);
                return;
            }

            if (input._autocomplete_initialized) {
               // console.log(`🔄 Поле "${input.id}" уже инициализировано → пропускаем`);
                return;
            }

            const fieldName = $(input).attr('data-field-name') || $(input).attr('name');
            const fieldId = input.id;
            const detailsContainer = $('#' + fieldId + '-details');

           // console.groupCollapsed(`🔧 Инициализация поля "${fieldName}"`);
            //console.log('DOM элемент:', input);
           // console.log('ID поля:', fieldId);
            //console.log('Классы:', input.classList.value);
           // console.log('Текстовое значение:', input.value.trim() || '(пусто)');
           // console.groupEnd();

            // Настройки автозаполнения
            const autocompleteOptions = {
                types: ['address'],
                componentRestrictions: { country: 'ru' },
                fields: ['place_id', 'geometry', 'formatted_address', 'address_components']
            };

            // Инициализируем автозаполнение
            const autocomplete = new google.maps.places.Autocomplete(input, autocompleteOptions);

            input._autocomplete = autocomplete;
            input._autocomplete_initialized = true;

          //  console.log(`📍 Autocomplete инициализирован для поля "${fieldName}"`);

            // Логируем параметры запроса к Google
            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();
                const request = {
                    input: input.value,
                    options: autocompleteOptions
                };

               // console.groupCollapsed(`📡 Запрос к Google: ${fieldName}`);
               // console.log('Запрашиваемый адрес:', request.input);
               // console.log('Параметры:', request.options);
              //  console.groupEnd();

              //  console.groupCollapsed(`📥 Ответ от Google: ${fieldName}`);
                if (!place.geometry) {
                 //   console.warn('⚠️ Место не найдено — пользователь ввёл вручную');
                    if (detailsContainer.length) detailsContainer.hide();
                //    console.groupEnd();
                    return;
                }

                // Логируем ответ
               // console.log('Форматированный адрес:', place.formatted_address);
               // console.log('Координаты:', {
                   // lat: place.geometry.location.lat(),
                    //lng: place.geometry.location.lng()
               // });

                // Парсим компоненты
                if (place.address_components && Array.isArray(place.address_components)) {
                    place.address_components.forEach((component, index) => {
                       // console.log(` - [${index}] ${component.types.join(', ')}: ${component.long_name}`);
                    });
                } else {
                   // console.warn('⚠️ address_components не передан или пуст');
                }

                // Сохраняем данные в DOM
                if (detailsContainer.length) {
                    const city = place.address_components?.find(c => c.types.includes('locality'))?.long_name || 'Не найден';
                    const postalCode = place.address_components?.find(c => c.types.includes('postal_code'))?.long_name || 'Не найден';
                    const lat = place.geometry?.location?.lat() || '';
                    const lng = place.geometry?.location?.lng() || '';

                    detailsContainer.find('.address-full').text(place.formatted_address || '');
                    detailsContainer.find('.address-city').text(city);
                    detailsContainer.find('.address-zip').text(postalCode);
                    detailsContainer.find('.address-lat').text(lat);
                    detailsContainer.find('.address-lng').text(lng);
                    detailsContainer.show();
                }

                console.groupEnd();
            });
        });
    }

    // Ждём загрузки Google Maps API
    const checkGoogleMaps = setInterval(() => {
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            clearInterval(checkGoogleMaps);
            initGoogleAutocompleteFields();
        }
    }, 500);

    // MutationObserver для динамически подгружаемых форм
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.type === 'childList') {
                initGoogleAutocompleteFields();
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});