# WPP Field Builder Manager

Универсальный плагин для управления формами в WordPress с поддержкой кастомных полей, условной логики, валидации и адаптивного дизайна на Bootstrap.

## Возможности

- **Кастомные типы полей**: Расширяемая система полей (text, select, checkbox, accordion, repeater и др.)
- **Условная логика**: Показ/скрытие полей в зависимости от значений других полей
- **Валидация**: Встроенная и пользовательская валидация через callback-функции
- **Адаптивный дизайн**: Интеграция с Bootstrap 5 для мобильных форм
- **Поддержка админки и фронтенда**: Работает как в админ-панели WordPress, так и на публичной части сайта
- **Поддержка Select2**: Улучшенные выпадающие списки с поиском

## Установка

1. Загрузите папку `wpp-field-builder-manager` в `/wp-content/plugins/`
2. Активируйте плагин через меню 'Плагины' в WordPress
3. Используйте классы полей в коде вашей темы или плагина

## Использование

### Создание базового поля

```php
// Текстовое поле
$text_field = new WPP_Text_Field([
    'name' => 'first_name',
    'label' => 'Имя',
    'placeholder' => 'Введите ваше имя',
    'required' => true,
    'width' => '1/2'
]);
$text_field->render();
```

### Условная логика

```php
// Поле, которое отображается только когда другое поле имеет определённое значение
$conditional_field = new WPP_Text_Field([
    'name' => 'company_name',
    'label' => 'Название компании',
    'conditional' => [
        'employment_status' => ['employed']
    ]
]);
$conditional_field->render();
```

### Выпадающий список с Select2

```php
$select_field = new WPP_Select_Field([
    'name' => 'state',
    'label' => 'Область',
    'options' => [
        'CA' => 'Калининградская',
        'NY' => 'Московская'
    ],
    'select2' => true
]);
$select_field->render();
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
| Repeater | `WPP_Repeater_Field` | Повторяемые группы полей |
| Address | `WPP_Address_Field` | Группа полей адреса |
| Button | `WPP_Button_Field` | Кнопки действий |
| Button Group | `WPP_Button_Group_Field` | Группа кнопок для выбора |
| Documents Upload | `WPP_Documents_Upload_Field` | Загрузка файлов |

## Аргументы полей

| Аргумент | Тип | По умолчанию | Описание |
|----------|------|---------|-------------|
| `name` | string | '' | Имя поля (обязательно) |
| `label` | string | '' | Подпись поля |
| `description` | string | '' | Текст подсказки под полем |
| `default` | mixed | '' | Значение по умолчанию |
| `placeholder` | string | '' | Placeholder для input |
| `classes` | array | [] | Дополнительные CSS-классы |
| `width` | string | 'full' | Ширина поля (full, 1/2, 1/3, 1/4 и т.д.) |
| `required` | bool | false | Сделать поле обязательным |
| `conditional` | array | [] | Правила условного отображения |
| `validation` | callable | null | Callback для пользовательской валидации |

## Опции ширины

- `full` или `12` - Полная ширина (col-12)
- `1/2` или `6` - Половина ширины (col-md-6)
- `1/3` или `4` - Одна треть (col-md-4)
- `1/4` или `3` - Одна четверть (col-md-3)
- `2/3` или `8` - Две трети (col-md-8)
- И другие: 1/12, 1/6, 5, 7, 9, 10, 11

## Хуки и фильтры

### `wpp_form_field_default`
Фильтрует значение поля по умолчанию.

### `wpp_form_field_default_{field_name}`
Фильтрует значение по умолчанию для конкретного поля.

### `wpp_form_field_conditional_value`
Фильтрует значение, используемое для проверки условной логики.

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
│   ├── text/
│   ├── select/
│   ├── checkbox/
│   └── ... (другие типы полей)
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

## Требования

- WordPress 5.0 или выше
- PHP 7.4 или выше
- jQuery (входит в состав WordPress)
- Bootstrap 5.3 (загружается автоматически через CDN)

## Лицензия

GPL-2.0-or-later

## Автор

Your Name <your@email.com>