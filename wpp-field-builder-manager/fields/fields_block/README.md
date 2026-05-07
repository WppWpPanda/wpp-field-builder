# Поле: Блок полей — `WPP_Fields_Block_Field`

Группирует несколько полей в один контейнер с общей меткой.

## ⚙️ Параметры

| Параметр       | Тип         | Описание |
|----------------|-------------|----------|
| `type`         | string      | `'fields_block'` |
| `name`         | string      | Имя блока (используется для группировки) |
| `label`        | string      | Общая метка блока |
| `fields`       | array       | Ассоциативный массив внутренних полей |
| `classes`      | array       | Дополнительные CSS-классы |
| `width`        | string      | Ширина: full, 1/2, 1/3 и т.д. |
| `conditional`  | array       | Условия отображения |
| `description`  | string      | Описание под блоком |

## ✅ Примеры

### 1. Простой блок с несколькими полями

```php
new WPP_Fields_Block_Field([
    'type' => 'fields_block',
    'name' => 'address_block',
    'label' => 'Адрес регистрации',
    'width' => 'full',
    'fields' => [
        'city' => [
            'type' => 'text',
            'label' => 'Город',
            'placeholder' => 'Москва'
        ],
        'street' => [
            'type' => 'text',
            'label' => 'Улица',
            'placeholder' => 'ул. Ленина'
        ],
        'building' => [
            'type' => 'text',
            'label' => 'Дом',
            'placeholder' => '10'
        ],
        'apartment' => [
            'type' => 'text',
            'label' => 'Квартира',
            'placeholder' => '25'
        ]
    ]
]);
```

### 2. Блок с разными типами полей

```php
new WPP_Fields_Block_Field([
    'type' => 'fields_block',
    'name' => 'contact_block',
    'label' => 'Контактная информация',
    'width' => 'full',
    'fields' => [
        'phone' => [
            'type' => 'text',
            'element_type' => 'tel',
            'label' => 'Телефон',
            'placeholder' => '+7 (___) ___-__-__'
        ],
        'email' => [
            'type' => 'text',
            'element_type' => 'email',
            'label' => 'Email',
            'placeholder' => 'example@domain.com'
        ],
        'preferred_contact' => [
            'type' => 'radio',
            'label' => 'Предпочтительный способ связи',
            'options' => [
                'phone' => 'Телефон',
                'email' => 'Email',
                'any' => 'Любой'
            ]
        ]
    ]
]);
```

### 3. Блок с вложенными селектами и чекбоксами

```php
new WPP_Fields_Block_Field([
    'type' => 'fields_block',
    'name' => 'employment_block',
    'label' => 'Информация о работе',
    'width' => 'full',
    'fields' => [
        'employment_status' => [
            'type' => 'select',
            'label' => 'Статус занятости',
            'options' => [
                'employed' => 'Работаю',
                'self_employed' => 'Самозанятый',
                'unemployed' => 'Не работаю',
                'retired' => 'Пенсионер'
            ]
        ],
        'company_name' => [
            'type' => 'text',
            'label' => 'Название компании'
        ],
        'position' => [
            'type' => 'text',
            'label' => 'Должность'
        ],
        'has_additional_income' => [
            'type' => 'checkbox',
            'label' => 'Есть дополнительный доход'
        ]
    ]
]);
```

### 4. Блок внутри repeater

```php
new WPP_Repeater_Field([
    'type' => 'repeater',
    'name' => 'references',
    'title' => 'Референс',
    'min' => 1,
    'max' => 3,
    'fields' => [
        'reference_info' => [
            'type' => 'fields_block',
            'name' => 'reference_info',
            'label' => 'Данные контакта',
            'fields' => [
                'name' => [
                    'type' => 'text',
                    'label' => 'Имя'
                ],
                'phone' => [
                    'type' => 'text',
                    'element_type' => 'tel',
                    'label' => 'Телефон'
                ],
                'relationship' => [
                    'type' => 'select',
                    'label' => 'Кем приходится',
                    'options' => [
                        'relative' => 'Родственник',
                        'friend' => 'Друг',
                        'colleague' => 'Коллега'
                    ]
                ]
            ]
        ]
    ]
]);
```

## 📝 Примечания

- **Именование полей**: внутренние поля получают имена относительно блока
- **Валидация**: каждое внутреннее поле валидируется независимо
- **CSS-классы**: блок получает класс `.wpp-fields-block` для стилизации
- **Адаптивность**: используйте параметр `width` для управления шириной

## 🔧 Особенности

- Поддержка всех типов полей билдера внутри блока
- Возможность вложенности блоков друг в друга
- Корректная работа в составе repeater
- Автоматическая инициализация JS-функционала внутренних полей
- Группировка визуально связанных полей

## 🎨 Стилизация

Базовые стили подключаются автоматически. Для кастомизации:

```css
.wpp-fields-block {
    /* Ваши стили */
}

.wpp-fields-block-label {
    font-weight: bold;
    margin-bottom: 10px;
}
```
