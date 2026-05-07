# Документация полей WPP Field Builder

Этот каталог содержит документацию по всем типам полей, доступным в конструкторе форм.

## 📚 Доступные поля

### Базовые поля

| Поле | Класс | Описание | Файл |
|------|-------|----------|------|
| **Текст** | `WPP_Text_Field` | Текстовое поле с поддержкой text, email, tel, hidden | [README](../fields/text/README.md) |
| **Textarea** | `WPP_Textarea_Field` | Многострочное текстовое поле | [README](../fields/textarea/README.md) |
| **Число** | `WPP_Number_Field` | Поле для ввода чисел | [README](../fields/number/README.md) |
| **Селект** | `WPP_Select_Field` | Выпадающий список | [README](../fields/select/README.md) |
| **Радио** | `WPP_Radio_Field` | Радио-кнопки | [README](../fields/radio/README.md) |
| **Чекбокс** | `WPP_Checkbox_Field` | Чекбокс (галочка) | [README](../fields/checkbox/README.md) |
| **Дата** | `WPP_Datepicker_Field` | Поле выбора даты | [README](../fields/datepicker/README.md) |

### Специализированные поля

| Поле | Класс | Описание | Файл |
|------|-------|----------|------|
| **Кнопка** | `WPP_Button_Field` | Кнопка действия | [README](../fields/button/README.md) |
| **Группа кнопок** | `WPP_Button_Group_Field` | Группа кнопок как радио-поля | [README](../fields/button_group/README.md) |
| **Адрес** | `WPP_Address_Field` | Поле адреса с автозаполнением Google Maps | [README](../fields/address/README.md) |
| **Проценты/Деньги** | `WPP_Percent_Money_Field` | Двойное поле: сумма и проценты | [README](../fields/percent_money/README.md) |
| **Загрузка документов** | `WPP_Documents_Upload_Field` | AJAX-загрузка файлов | [README](../fields/documents_upload/README.md) |
| **Контент** | `WPP_Content_Field` | Произвольный HTML-контент | [README](../fields/content/README.md) |

### Составные поля

| Поле | Класс | Описание | Файл |
|------|-------|----------|------|
| **Аккордеон** | `WPP_Accordion_Field` | Раскрывающийся блок с контентом, поддерживает вложенность | [README](../fields/accordion/README.md) |
| **Повторитель** | `WPP_Repeater_Field` | Динамическое добавление блоков полей | [README](../fields/repeater/README.md) |
| **Блок полей** | `WPP_Fields_Block_Field` | Группировка нескольких полей | [README](../fields/fields_block/README.md) |

## 🚀 Быстрый старт

### 1. Использование простого поля

```php
new WPP_Text_Field([
    'type' => 'text',
    'name' => 'username',
    'label' => 'Имя пользователя',
    'required' => true
]);
```

### 2. Использование составного поля

```php
new WPP_Repeater_Field([
    'type' => 'repeater',
    'name' => 'contacts',
    'title' => 'Контакт',
    'fields' => [
        'phone' => [
            'type' => 'text',
            'label' => 'Телефон'
        ],
        'email' => [
            'type' => 'text',
            'element_type' => 'email',
            'label' => 'Email'
        ]
    ]
]);
```

### 3. Комбинирование полей

```php
$fields = [
    // Секция: Личная информация
    'personal_title' => [
        'type' => 'content',
        'label' => 'Личная информация',
        'content' => '<hr>'
    ],
    
    'full_name' => [
        'type' => 'text',
        'label' => 'ФИО',
        'required' => true
    ],
    
    'birthdate' => [
        'type' => 'datepicker',
        'label' => 'Дата рождения'
    ],
    
    // Секция: Контакты
    'contact_title' => [
        'type' => 'content',
        'label' => 'Контакты',
        'content' => '<hr>'
    ],
    
    'contacts_repeater' => [
        'type' => 'repeater',
        'name' => 'contacts',
        'title' => 'Способ связи',
        'fields' => [
            'type' => [
                'type' => 'select',
                'label' => 'Тип',
                'options' => [
                    'phone' => 'Телефон',
                    'email' => 'Email'
                ]
            ],
            'value' => [
                'type' => 'text',
                'label' => 'Значение'
            ]
        ]
    ],
    
    // Секция: Документы
    'docs_accordion' => [
        'type' => 'super_accordion',
        'name' => 'documents',
        'title' => 'Документы',
        'fields' => [
            'passport' => [
                'type' => 'documents_upload',
                'name' => 'rd_passport',
                'label' => 'Паспорт'
            ],
            'snils' => [
                'type' => 'documents_upload',
                'name' => 'rd_snils',
                'label' => 'СНИЛС'
            ]
        ]
    ]
];
```

## 📖 Руководства

- **[Создание нового поля](CREATE_NEW_FIELD.md)** - Полное руководство по созданию собственного типа поля
- **[Рефакторинг кода](REFACTORING.md)** - Информация о проведённом рефакторинге (если существует)

## 🔧 Общие параметры всех полей

Все поля поддерживают следующие базовые параметры:

| Параметр | Тип | Описание |
|----------|-----|----------|
| `type` | string | Тип поля (обязательно) |
| `name` | string | Имя поля (обязательно) |
| `label` | string | Подпись к полю |
| `description` | string | Описание под полем |
| `default` | mixed | Значение по умолчанию |
| `classes` | array | Дополнительные CSS-классы |
| `width` | string | Ширина: full, 1/2, 1/3, 1/4 |
| `conditional` | array | Условия отображения |
| `required` | boolean | Обязательность заполнения |
| `validation` | callable | Функция валидации |

## 💡 Советы по использованию

1. **Группировка**: Используйте `WPP_Content_Field` для разделения секций формы
2. **Вложенность**: Составные поля (repeater, accordion, fields_block) можно вкладывать друг в друга
3. **Валидация**: Добавляйте callback-функции для сложной валидации
4. **Условия**: Используйте conditional logic для показа/скрытия полей
5. **Стили**: Применяйте custom classes для кастомизации внешнего вида

## 🆘 Поддержка

При возникновении проблем:

1. Изучите документацию конкретного поля
2. Проверьте примеры использования
3. Включите WordPress Debug Mode
4. Проверьте консоль браузера на ошибки JS

## 📝 Примеры использования по типу задачи

### Форма заявки на заём

```php
$fields = [
    'amount' => [
        'type' => 'number',
        'label' => 'Сумма займа',
        'min' => 1000,
        'max' => 1000000
    ],
    'term' => [
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
        'label' => 'Цель займа'
    ]
];
```

### Анкета клиента

```php
$fields = [
    'personal_info' => [
        'type' => 'super_accordion',
        'name' => 'personal',
        'title' => 'Персональная информация',
        'fields' => [
            'full_name' => ['type' => 'text', 'label' => 'ФИО'],
            'birthdate' => ['type' => 'datepicker', 'label' => 'Дата рождения'],
            'passport' => ['type' => 'text', 'label' => 'Паспортные данные']
        ]
    ],
    'contacts' => [
        'type' => 'repeater',
        'name' => 'contact_list',
        'title' => 'Контакт',
        'fields' => [
            'type' => ['type' => 'select', 'options' => ['phone' => 'Телефон', 'email' => 'Email']],
            'value' => ['type' => 'text']
        ]
    ]
];
```

### Загрузка документов

```php
$fields = [
    'identity_docs' => [
        'type' => 'content',
        'label' => 'Документы, удостоверяющие личность',
        'content' => '<hr>'
    ],
    'passport_main' => [
        'type' => 'documents_upload',
        'name' => 'rd_passport_main',
        'label' => 'Паспорт - главная страница',
        'width' => '1/2'
    ],
    'passport_reg' => [
        'type' => 'documents_upload',
        'name' => 'rd_passport_reg',
        'label' => 'Паспорт - регистрация',
        'width' => '1/2'
    ]
];
```

---

**Версия документации**: 1.0  
**Последнее обновление**: 2024
