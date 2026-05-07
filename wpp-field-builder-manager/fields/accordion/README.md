# Поле: Аккордеон — `WPP_Accordion_Field`

Поле для создания раскрывающегося блока с произвольным содержимым. Поддерживает вложенность аккордеонов друг в друга.

## ⚙️ Параметры

| Параметр       | Тип          | Описание |
|----------------|---------------|----------|
| `name`         | string        | Имя аккордеона |
| `title`        | string        | Заголовок аккордеона |
| `content`      | string / callable | Контент внутри (HTML или callback) |
| `open`         | boolean       | Открыт ли по умолчанию |
| `classes`      | array         | Дополнительные CSS-классы |
| `width`        | string        | Ширина: full, 1/2 и т.д. |

## ✅ Примеры

### 1. Простой аккордеон с текстом

```php
new WPP_Accordion_Field([
    'type'    => 'accordion',
    'name'    => 'info_block',
    'title'   => 'Информация о доставке',
    'content' => '<p>Доставка осуществляется в течение 3 дней</p>'
]);
```

### 2. Аккордеон с полями формы

```php
new WPP_Accordion_Field([
    'type'    => 'accordion',
    'name'    => 'advanced_settings',
    'title'   => 'Расширенные настройки',
    'open'    => true,
    'content' => function () {
        $checkbox = new WPP_Checkbox_Field([
            'name' => 'enable_debug',
            'label' => 'Включить режим отладки'
        ]);
        $checkbox->render();

        $radio = new WPP_Radio_Field([
            'name' => 'delivery_type',
            'label' => 'Тип доставки',
            'options' => [
                'standard' => 'Стандартная',
                'express' => 'Экспресс'
            ]
        ]);
        $radio->render();
    }
]);
```

### 3. Вложенные аккордеоны (аккордеон внутри аккордеона)

```php
new WPP_Accordion_Field([
    'type'    => 'accordion',
    'name'    => 'main_accordion',
    'title'   => 'Основные настройки',
    'open'    => true,
    'content' => function () {
        // Текстовое поле
        $text = new WPP_Text_Field([
            'name' => 'main_setting',
            'label' => 'Основной параметр'
        ]);
        $text->render();

        // Вложенный аккордеон
        new WPP_Accordion_Field([
            'type'    => 'accordion',
            'name'    => 'nested_accordion_1',
            'title'   => 'Дополнительные опции',
            'content' => function () {
                $checkbox = new WPP_Checkbox_Field([
                    'name' => 'extra_option',
                    'label' => 'Дополнительная опция'
                ]);
                $checkbox->render();
                
                // Ещё один вложенный аккордеон (третий уровень)
                new WPP_Accordion_Field([
                    'type'    => 'accordion',
                    'name'    => 'nested_accordion_2',
                    'title'   => 'Продвинутые настройки',
                    'content' => '<p>Здесь могут быть любые другие поля</p>'
                ])->render();
            }
        ])->render();
    }
]);
```

### 4. Несколько аккордеонов подряд

```php
// Первый аккордеон
new WPP_Accordion_Field([
    'type'    => 'accordion',
    'name'    => 'section_1',
    'title'   => 'Секция 1',
    'content' => '<p>Содержимое первой секции</p>'
]);

// Второй аккордеон
new WPP_Accordion_Field([
    'type'    => 'accordion',
    'name'    => 'section_2',
    'title'   => 'Секция 2',
    'content' => '<p>Содержимое второй секции</p>'
]);

// Третий аккордеон
new WPP_Accordion_Field([
    'type'    => 'accordion',
    'name'    => 'section_3',
    'title'   => 'Секция 3',
    'content' => '<p>Содержимое третьей секции</p>'
]);
```

## 🔧 Особенности

- **Вложенность**: Аккордеоны можно вкладывать друг в друга без ограничений по глубине
- **Bootstrap 5**: Использует классы Bootstrap 5 для стилизации
- **Динамическое содержимое**: Параметр `content` может быть строкой HTML или callback-функцией
- **Автоматическая инициализация**: Скрипт автоматически инициализирует Bootstrap Collapse
- **Работа в конструкторе**: Полностью поддерживается drag-and-drop в конструкторе форм

## 💡 Советы

1. Уникальные имена: Убедитесь, что каждый аккордеон имеет уникальное имя (`name`)
2. Группировка: Используйте аккордеоны для логической группировки связанных полей
3. Производительность: Не создавайте слишком глубокую вложенность (3-4 уровня максимум)
4. UX: Открывайте важные секции по умолчанию (`open => true`)