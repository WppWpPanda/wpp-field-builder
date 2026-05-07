# Поле: Супераккордеон — `WPP_Super_Accordion_Field`

Расширенный аккордеон с динамическим обновлением заголовка на основе значений внутренних полей.

## ⚙️ Параметры

| Параметр       | Тип          | Описание |
|----------------|---------------|----------|
| `type`         | string      | `'super_accordion'` |
| `name`         | string        | Имя аккордеона (используется как ID) |
| `title`        | string        | Заголовок аккордеона |
| `header`       | string        | Шаблон заголовка с плейсхолдерами (опционально) |
| `fields`       | array         | Поля внутри аккордеона |
| `open`         | boolean       | Открыт ли по умолчанию |
| `classes`      | array         | Дополнительные CSS-классы |
| `width`        | string        | Ширина: full, 1/2 и т.д. |
| `default`      | array         | Значения по умолчанию для внутренних полей |

## ✅ Примеры

### 1. Простой супераккордеон с полями

```php
new WPP_Super_Accordion_Field([
    'type' => 'super_accordion',
    'name' => 'client_info',
    'title' => 'Информация о клиенте',
    'open' => false,
    'fields' => [
        'full_name' => [
            'type' => 'text',
            'label' => 'ФИО',
            'placeholder' => 'Иванов Иван Иванович'
        ],
        'email' => [
            'type' => 'text',
            'element_type' => 'email',
            'label' => 'Email',
            'placeholder' => 'client@example.com'
        ],
        'phone' => [
            'type' => 'text',
            'element_type' => 'tel',
            'label' => 'Телефон',
            'placeholder' => '+7 (___) ___-__-__'
        ]
    ]
]);
```

### 2. Супераккордеон с динамическим заголовком

```php
new WPP_Super_Accordion_Field([
    'type' => 'super_accordion',
    'name' => 'loan_details',
    'title' => 'Детали займа',
    'header' => '{loan_amount} руб. на {loan_term} мес.',
    'open' => true,
    'fields' => [
        'loan_amount' => [
            'type' => 'number',
            'label' => 'Сумма займа',
            'default' => 100000
        ],
        'loan_term' => [
            'type' => 'select',
            'label' => 'Срок',
            'options' => [
                '6' => '6 месяцев',
                '12' => '12 месяцев',
                '24' => '24 месяца'
            ]
        ],
        'purpose' => [
            'type' => 'textarea',
            'label' => 'Цель займа',
            'placeholder' => 'Опишите цель...'
        ]
    ]
]);
```

### 3. Несколько супераккордеонов в форме

```php
$fields = [
    'personal_info' => [
        'type' => 'super_accordion',
        'name' => 'personal_info',
        'title' => 'Персональная информация',
        'fields' => [
            'full_name' => [
                'type' => 'text',
                'label' => 'ФИО'
            ],
            'birthdate' => [
                'type' => 'datepicker',
                'label' => 'Дата рождения'
            ]
        ]
    ],
    'contact_info' => [
        'type' => 'super_accordion',
        'name' => 'contact_info',
        'title' => 'Контактная информация',
        'fields' => [
            'phone' => [
                'type' => 'text',
                'element_type' => 'tel',
                'label' => 'Телефон'
            ],
            'email' => [
                'type' => 'text',
                'element_type' => 'email',
                'label' => 'Email'
            ]
        ]
    ]
];
```

## 📝 Примечания

- **Динамический заголовок**: используйте `{field_name}` в параметре `header` для автоматического обновления заголовка
- **Вложенные поля**: поддерживаются все типы полей билдера
- **Инициализация Bootstrap**: требует Bootstrap Collapse для работы анимации
- **JS-зависимости**: автоматически подключает jQuery и super_accordion.js

## 🔧 Особенности

- Автоматическое обновление заголовка при изменении значений полей
- Поддержка textarea в шаблоне заголовка
- Корректная работа с динамически добавленными полями (в repeater)
- Сохранение состояния (открыт/закрыт) при перерисовке формы
