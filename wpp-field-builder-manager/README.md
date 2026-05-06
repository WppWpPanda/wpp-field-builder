# WPP Field Builder Manager

Универсальный плагин для управления формами в WordPress с поддержкой кастомных полей, условной логики, валидации и адаптивного дизайна на Bootstrap.

## Возможности

- **Кастомные типы полей**: Расширяемая система полей (text, select, checkbox, accordion, repeater и др.)
- **Условная логика**: Показ/скрытие полей в зависимости от значений других полей
- **Валидация**: Встроенная и пользовательская валидация через callback-функции
- **Адаптивный дизайн**: Интеграция с Bootstrap 5 для мобильных форм
- **Поддержка админки и фронтенда**: Работает как в админ-панели WordPress, так и на публичной части сайта
- **Поддержка Select2**: Улучшенные выпадающие списки с поиском
- **Гибкая настройка Bootstrap**: Возможность использовать локальные файлы или CDN
- **Визуальный конструктор форм**: Drag-and-drop интерфейс для создания форм в админке с возможностью копирования конфигурации

## Быстрый старт

### Вариант 1: Использование визуального конструктора (рекомендуется)

1. После активации плагина перейдите в меню **WPP Field Builder → Конструктор форм**
2. Перетащите нужные поля из палитры слева в рабочую область
3. Настройте каждое поле: название, метку, обязательность, ширину, опции
4. Добавьте условную логику при необходимости
5. Нажмите кнопку **"Копировать конфигурацию"**
6. Скопируйте полученный PHP-код и используйте в вашем шаблоне:

```php
<?php
$config = [
    // вставьте скопированную конфигурацию здесь
];
wpp_form( $config );
?>
```

### Вариант 2: Программное создание формы

#### Создание базового поля

```php
// Текстовое поле
$text_field = new WPP_Text_Field([
    'name' => 'first_name',
    'label' => 'Имя',
    'placeholder' => 'Введите ваше имя',
    'required' => true,
    'width' => 'half'
]);
$text_field->render();
```

### Выпадающий список

```php
$select_field = new WPP_Select_Field([
    'name' => 'country',
    'label' => 'Страна',
    'options' => [
        'ru' => 'Россия',
        'by' => 'Беларусь',
        'kz' => 'Казахстан'
    ],
    'default' => 'ru'
]);
$select_field->render();
```

### Чекбокс

```php
$checkbox_field = new WPP_Checkbox_Field([
    'name' => 'agree_terms',
    'label' => 'Я согласен с условиями обработки данных',
    'required' => true
]);
$checkbox_field->render();
```

### Условная логика

Поле отображается только когда другое поле имеет определённое значение:

```php
// Поле появляется только если выбрано "employed" в поле employment_status
$conditional_field = new WPP_Text_Field([
    'name' => 'company_name',
    'label' => 'Название компании',
    'conditional' => [
        'employment_status' => ['employed']
    ]
]);
$conditional_field->render();
```

### Группа полей (Fields Block)

```php
$block_field = new WPP_Fields_Block_Field([
    'name' => 'contact_info',
    'label' => 'Контактная информация',
    'fields' => [
        'email' => [
            'type' => 'text',
            'label' => 'Email',
            'placeholder' => 'example@mail.ru'
        ],
        'phone' => [
            'type' => 'text',
            'label' => 'Телефон',
            'placeholder' => '+7 (999) 000-00-00'
        ]
    ]
]);
$block_field->render();
```

### Аккордеон с полями

```php
$accordion_field = new WPP_Accordion_Field([
    'name' => 'details',
    'title' => 'Дополнительная информация',
    'fields' => [
        'comments' => [
            'type' => 'textarea',
            'label' => 'Комментарий',
            'placeholder' => 'Ваш комментарий...'
        ]
    ]
]);
$accordion_field->render();
```

### Поле с процентами и суммой

```php
$percent_money_field = new WPP_Percent_Money_Field([
    'name' => 'payment',
    'label' => 'Сумма платежа',
    'base_amount' => 10000,
    'default' => [
        'money' => 5000,
        'percent' => 50
    ]
]);
$percent_money_field->render();
```

## Доступные типы полей

| Тип поля | Класс | Описание |
|----------|-------|-------------|
| Text | `WPP_Text_Field` | Текст, email, tel, hidden, money, percentage |
| Select | `WPP_Select_Field` | Выпадающий список с опциональным Select2 |
| Checkbox | `WPP_Checkbox_Field` | Одиночный чекбокс |
| Radio | `WPP_Radio_Field` | Группа радио-кнопок |
| Textarea | `WPP_Textarea_Field` | Многострочное текстовое поле |
| Number | `WPP_Number_Field` | Числовое поле |
| Datepicker | `WPP_Datepicker_Field` | Выбор даты |
| Accordion | `WPP_Accordion_Field` | Сворачиваемые секции |
| Super Accordion | `WPP_Super_Accordion_Field` | Аккордеон с динамическим заголовком |
| Repeater | `WPP_Repeater_Field` | Повторяемые группы полей |
| Address | `WPP_Address_Field` | Группа полей адреса |
| Button | `WPP_Button_Field` | Кнопки действий |
| Button Group | `WPP_Button_Group_Field` | Группа кнопок для выбора |
| Documents Upload | `WPP_Documents_Upload_Field` | Загрузка файлов |
| Content | `WPP_Content_Field` | Текстовый контент/разделитель |
| Fields Block | `WPP_Fields_Block_Field` | Блок для группировки полей |
| Percent Money | `WPP_Percent_Money_Field` | Поле с двумя значениями: сумма и процент |

## Аргументы полей

| Аргумент | Тип | По умолчанию | Описание |
|----------|------|---------|-------------|
| `name` | string | '' | **Обязательно.** Имя поля (name="...") |
| `label` | string | '' | Подпись поля |
| `description` | string | '' | Текст подсказки под полем |
| `default` | mixed | '' | Значение по умолчанию |
| `placeholder` | string | '' | Placeholder для input |
| `classes` | array | [] | Дополнительные CSS-классы |
| `width` | string | 'full' | Ширина поля (full, 1/2, 1/3, 1/4 и т.д.) |
| `required` | bool | false | Сделать поле обязательным |
| `conditional` | array | [] | Правила условного отображения |
| `validation` | callable | null | Callback для пользовательской валидации |
| `compare` | string | '=' | Оператор сравнения для условной логики (=, !=, >, < и т.д.) |

## Опции ширины

| Значение | Класс Bootstrap | Описание |
|----------|----------------|----------|
| `full` или `12` | col-12 | Полная ширина |
| `1/2` или `6` | col-md-6 | Половина ширины |
| `1/3` или `4` | col-md-4 | Одна треть |
| `1/4` или `3` | col-md-3 | Одна четверть |
| `2/3` или `8` | col-md-8 | Две трети |
| `1/6` или `2` | col-md-2 | Одна шестая |
| `1/12` или `1` | col-md-1 | Одна двенадцатая |

Также поддерживаются числовые значения: 5, 7, 9, 10, 11.

## Требования

- WordPress 5.0 или выше
- PHP 7.4 или выше
- jQuery (входит в состав WordPress)
- Bootstrap 5.3 (загружается автоматически через CDN, можно отключить)

## Установка

1. Загрузите папку `wpp-field-builder-manager` в `/wp-content/plugins/`
2. Активируйте плагин через меню 'Плагины' в WordPress
3. Перейдите в меню **WPP Field Builder → Конструктор форм** для создания формы визуально
   ИЛИ используйте классы полей в коде вашей темы или плагина

## Управление Bootstrap

### Отключение автоматической загрузки Bootstrap

Если ваша тема уже использует Bootstrap, отключите его загрузку плагином:

```php
add_filter( 'wpp_assets_load_bootstrap', '__return_false' );
```

### Использование локальных файлов Bootstrap

```php
// Локальный CSS
add_filter( 'wpp_assets_bootstrap_css_url', function( $url ) {
    return get_stylesheet_directory_uri() . '/css/bootstrap.min.css';
});

// Локальный JS
add_filter( 'wpp_assets_bootstrap_js_url', function( $url ) {
    return get_stylesheet_directory_uri() . '/js/bootstrap.bundle.min.js';
});
```

## Хуки и фильтры

### `wpp_form_field_default`

Фильтрует значение поля по умолчанию для всех полей.

```php
add_filter( 'wpp_form_field_default', function( $default, $args ) {
    return $default;
}, 10, 2 );
```

### `wpp_form_field_default_{field_name}`

Фильтрует значение по умолчанию для конкретного поля.

```php
add_filter( 'wpp_form_field_default_first_name', function( $default ) {
    return 'Иван';
});
```

### `wpp_form_field_conditional_value`

Фильтрует значение для проверки условной логики.

```php
add_filter( 'wpp_form_field_conditional_value', function( $value, $field_name, $field_object ) {
    return $_POST[ $field_name ] ?? $value;
}, 10, 3 );
```

### `wpp_assets_bootstrap_css_url`

Переопределить URL CSS файла Bootstrap.

```php
add_filter( 'wpp_assets_bootstrap_css_url', function( $url ) {
    return get_stylesheet_directory_uri() . '/css/bootstrap.min.css';
});
```

### `wpp_assets_bootstrap_js_url`

Переопределить URL JS файла Bootstrap.

```php
add_filter( 'wpp_assets_bootstrap_js_url', function( $url ) {
    return get_stylesheet_directory_uri() . '/js/bootstrap.bundle.min.js';
});
```

### `wpp_super_accordion_form_data`

Передать данные для динамического заголовка Super Accordion.

```php
add_filter( 'wpp_super_accordion_form_data', function( $data, $field ) {
    return $_POST['form_data'] ?? [];
}, 10, 2 );
```

### `wpp_percent_money_field_value`

Получить значение для поля Percent Money.

```php
add_filter( 'wpp_percent_money_field_value', function( $value, $field_name, $type, $field ) {
    return $_POST[ $field_name ][ $type ] ?? $value;
}, 10, 4 );
```

## Управление Bootstrap

### Отключение Bootstrap

```php
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'WPP_Assets' ) ) {
        WPP_Assets::disable_bootstrap();
    }
});
```

### Использование локального Bootstrap

```php
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'WPP_Assets' ) ) {
        WPP_Assets::set_bootstrap_css_url( get_stylesheet_directory_uri() . '/css/bootstrap.min.css' );
        WPP_Assets::set_bootstrap_js_url( get_stylesheet_directory_uri() . '/js/bootstrap.bundle.min.js' );
    }
});
```

## Структура файлов

```
wpp-field-builder-manager/
├── wpp-field-builder-manager.php     # Главный файл плагина
├── README.md                         # Этот файл
│
├── includes/                         # Основные классы
│   ├── class-wpp-form-field.php      # Абстрактный базовый класс
│   ├── class-wpp-field-loader.php    # Автозагрузчик полей
│   └── class-wpp-assets.php          # Управление ресурсами
│
├── fields/                           # Реализации полей
│   ├── accordion/                    # Аккордеон
│   ├── address/                      # Адрес
│   ├── button/                       # Кнопка
│   ├── button_group/                 # Группа кнопок
│   ├── checkbox/                     # Чекбокс
│   ├── content/                      # Контент
│   ├── datepicker/                   # Выбор даты
│   ├── documents_upload/             # Загрузка файлов
│   ├── fields_block/                 # Блок полей
│   ├── number/                       # Число
│   ├── percent_money/                # Процент/сумма
│   ├── radio/                        # Радио-кнопки
│   ├── repeater/                     # Повторяемые поля
│   ├── select/                       # Выпадающий список
│   ├── super_accordion/              # Супер-аккордеон
│   ├── text/                         # Текст
│   └── textarea/                     # Многострочный текст
│
├── assets/                           # Общие ресурсы
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
│
└── test/                             # Тестовые файлы
```

## Примеры использования

### Простая форма регистрации

```php
<form method="post" action="">
    <?php
    $first_name = new WPP_Text_Field([
        'name' => 'first_name',
        'label' => 'Имя',
        'required' => true,
        'width' => '1/2'
    ]);
    $first_name->render();

    $last_name = new WPP_Text_Field([
        'name' => 'last_name',
        'label' => 'Фамилия',
        'required' => true,
        'width' => '1/2'
    ]);
    $last_name->render();

    $email = new WPP_Text_Field([
        'name' => 'email',
        'label' => 'Email',
        'placeholder' => 'example@mail.ru',
        'required' => true
    ]);
    $email->render();

    $phone = new WPP_Text_Field([
        'name' => 'phone',
        'label' => 'Телефон',
        'placeholder' => '+7 (999) 000-00-00'
    ]);
    $phone->render();

    $agree = new WPP_Checkbox_Field([
        'name' => 'agree_terms',
        'label' => 'Я согласен с условиями обработки персональных данных',
        'required' => true
    ]);
    $agree->render();

    $submit = new WPP_Button_Field([
        'name' => 'submit',
        'label' => 'Отправить',
        'type' => 'submit'
    ]);
    $submit->render();
    ?>
</form>
```

### Форма с условной логикой

```php
<form method="post" action="">
    <?php
    $employment = new WPP_Radio_Field([
        'name' => 'employment_status',
        'label' => 'Тип занятости',
        'options' => [
            'employed' => 'Работаю по найму',
            'self_employed' => 'Самозанятый',
            'unemployed' => 'Не работаю'
        ],
        'default' => 'employed'
    ]);
    $employment->render();

    $company = new WPP_Text_Field([
        'name' => 'company_name',
        'label' => 'Название компании',
        'conditional' => [
            'employment_status' => ['employed']
        ]
    ]);
    $company->render();

    $inn = new WPP_Text_Field([
        'name' => 'inn',
        'label' => 'ИНН',
        'conditional' => [
            'employment_status' => ['self_employed']
        ]
    ]);
    $inn->render();
    ?>
</form>
```

## Пример использования визуального конструктора

### Шаг 1: Создание формы в конструкторе

1. Перейдите в **WPP Field Builder → Конструктор форм**
2. Перетащите из палитры поля:
   - Текстовое поле (для имени)
   - Email поле (для email)
   - Select поле (для типа занятости)
   - Текстовое поле (для названия компании)
3. Настройте каждое поле:
   - Имя: `first_name`, Метка: `Ваше имя`, Обязательно: ✓
   - Email: `email`, Метка: `Email`, Обязательно: ✓
   - Select: `employment_status`, Метка: `Тип занятости`, Опции: `Работаю по найму`, `Самозанятый`, `Не работаю`
   - Text: `company_name`, Метка: `Название компании`
4. Для поля "Название компании" добавьте условную логику:
   - Показывать если: `employment_status` равно `Работаю по найму`
5. Нажмите **"Копировать конфигурацию"**

### Шаг 2: Использование сгенерированной конфигурации

Скопируйте полученный код и используйте в шаблоне:

```php
<?php
$config = [
    [
        'id' => 'field_1234567890_abc',
        'type' => 'text',
        'name' => 'first_name',
        'label' => 'Ваше имя',
        'placeholder' => '',
        'required' => true,
        'width' => 'full',
        'conditional_logic' => []
    ],
    [
        'id' => 'field_1234567891_def',
        'type' => 'email',
        'name' => 'email',
        'label' => 'Email',
        'placeholder' => 'example@mail.ru',
        'required' => true,
        'width' => 'full',
        'conditional_logic' => []
    ],
    [
        'id' => 'field_1234567892_ghi',
        'type' => 'select',
        'name' => 'employment_status',
        'label' => 'Тип занятости',
        'placeholder' => '',
        'required' => false,
        'width' => 'full',
        'options' => ['Работаю по найму', 'Самозанятый', 'Не работаю'],
        'conditional_logic' => []
    ],
    [
        'id' => 'field_1234567893_jkl',
        'type' => 'text',
        'name' => 'company_name',
        'label' => 'Название компании',
        'placeholder' => '',
        'required' => false,
        'width' => 'full',
        'conditional_logic' => [
            [
                'field' => 'employment_status',
                'operator' => 'equals',
                'value' => 'Работаю по найму'
            ]
        ]
    ]
];

// Отображение формы
echo wpp_render_form( $config );
?>
```

## Структура файлов плагина

```
wpp-field-builder-manager/
├── admin/                          # Административная панель
│   ├── class-wpp-form-builder-admin.php  # Класс конструктора форм
│   ├── css/
│   │   └── form-builder-admin.css        # Стили конструктора
│   └── js/
│       └── form-builder-admin.js         # JavaScript конструктора
├── assets/                         # Публичные ассеты
│   ├── css/                        # CSS файлы
│   └── js/                         # JavaScript файлы
├── fields/                         # Классы полей
│   ├── text/                       # Текстовые поля
│   ├── select/                     # Выпадающие списки
│   ├── checkbox/                   # Чекбоксы
│   └── ...                         # Другие типы полей
├── includes/                       # Основные классы
│   ├── class-wpp-form-field.php    # Базовый класс поля
│   ├── class-wpp-field-loader.php  # Загрузчик полей
│   └── class-wpp-assets.php        # Управление ассетами
├── languages/                      # Файлы перевода
├── test/                           # Тесты
├── README.md                       # Документация
└── wpp-field-builder-manager.php   # Главный файл плагина
```

## Часто задаваемые вопросы

### Как добавить собственное поле?

Создайте класс, наследующийся от `WPP_Form_Field`:

```php
class WPP_Custom_Field extends WPP_Form_Field {
    public function render() {
        // Ваша логика отображения
    }
}
```

Зарегистрируйте поле через фильтр:

```php
add_filter( 'wpp_form_builder_available_fields', function( $fields ) {
    $fields[] = [
        'type' => 'custom',
        'label' => 'Моё поле',
        'icon' => 'dashicons-star-filled'
    ];
    return $fields;
});
```

### Как сохранить данные формы?

Обработайте отправку формы стандартным способом WordPress:

```php
if ( isset( $_POST['wpp_form_submit'] ) ) {
    check_admin_referer( 'wpp_form_submit_action', 'wpp_form_nonce' );
    
    $data = [
        'first_name' => sanitize_text_field( $_POST['first_name'] ),
        'email' => sanitize_email( $_POST['email'] ),
    ];
    
    // Сохранение или обработка данных
    update_option( 'my_form_data', $data );
}
```

## Лицензия

GPL-2.0-or-later

## Автор

Your Name <your@email.com>
