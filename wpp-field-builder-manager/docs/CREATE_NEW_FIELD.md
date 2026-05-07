# Создание и подключение нового поля в WPP Field Builder

Это руководство описывает процесс создания собственного типа поля для конструктора форм.

## 📋 Структура поля

Каждое поле должно находиться в отдельной папке в директории `/fields/`:

```
fields/
└── your_field/
    ├── README.md                      # Документация
    ├── WPP_Your_Field_Field.php       # Основной класс
    ├── script.js                      # JavaScript (опционально)
    └── style.css                      # Стили (опционально)
```

## 🔧 Шаг 1: Создание класса поля

### Базовый шаблон класса

Создайте файл `WPP_Your_Field_Field.php`:

```php
<?php
/**
 * WPP Your Field - Описание поля
 *
 * Краткое описание функциональности
 *
 * @package WPP_Field_Builder
 * @subpackage Fields
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WPP_Your_Field_Field') && class_exists('WPP_Form_Field')) :

    class WPP_Your_Field_Field extends WPP_Form_Field {

        /**
         * Конструктор
         *
         * @param array $args Параметры поля
         */
        public function __construct($args = []) {
            parent::__construct($args);

            // Подключение ресурсов (если нужны JS/CSS)
            add_action('wp_footer', [$this, 'enqueue_assets']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        }

        /**
         * Подключение скриптов и стилей
         */
        public function enqueue_assets() {
            // Скрипт
            wp_enqueue_script(
                'wpp-your-field',
                WPP_FIELD_BUILDER_URL . 'fields/your_field/script.js',
                ['jquery'],
                file_exists(WPP_FIELD_BUILDER_PATH . 'fields/your_field/script.js')
                    ? filemtime(WPP_FIELD_BUILDER_PATH . 'fields/your_field/script.js')
                    : time(),
                true
            );

            // Стили
            wp_enqueue_style(
                'wpp-your-field',
                WPP_FIELD_BUILDER_URL . 'fields/your_field/style.css',
                [],
                file_exists(WPP_FIELD_BUILDER_PATH . 'fields/your_field/style.css')
                    ? filemtime(WPP_FIELD_BUILDER_PATH . 'fields/your_field/style.css')
                    : time(),
                'all'
            );
        }

        /**
         * Рендеринг HTML-кода поля
         */
        public function render() {
            $this->render_wrapper_start();

            // Получение параметров
            $name = esc_attr($this->get_name());
            $id = sanitize_key($this->args['name']);
            $value = esc_attr($this->get_value());
            $label = !empty($this->args['label']) ? esc_html($this->args['label']) : '';
            $description = !empty($this->args['description']) ? esc_html($this->args['description']) : '';

            // Ваш HTML-код поля
            ?>
            <input type="text"
                   id="<?php echo $id; ?>"
                   name="<?php echo $name; ?>"
                   value="<?php echo $value; ?>"
                   class="form-control wpp-your-field <?php echo esc_attr(implode(' ', $this->args['classes'] ?? [])); ?>">
            <?php

            // Рендеринг описания
            $this->render_description();
            $this->render_wrapper_end();
        }

        /**
         * Валидация значения поля (опционально)
         *
         * @param mixed $value Значение поля
         * @return mixed|WP_Error Очищенное значение или ошибка
         */
        public function validate($value) {
            // Ваша логика валидации
            if (empty($value) && !empty($this->args['required'])) {
                return new WP_Error('required', 'Поле обязательно для заполнения');
            }

            // Санитизация
            return sanitize_text_field($value);
        }
    }

endif;
```

## 🔧 Шаг 2: Создание JavaScript (если нужен)

Создайте файл `script.js` для клиентской логики:

```javascript
/**
 * WPP Your Field - Client-side functionality
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Инициализация полей
        initYourField();

        // Обработчик изменений
        $(document).on('input change', '.wpp-your-field', function() {
            handleYourFieldChange($(this));
        });

        // Для работы с динамическими полями (repeater)
        $(document).on('wpp_repeater_added', function(e, $container) {
            $container.find('.wpp-your-field').each(function() {
                initYourFieldInstance($(this));
            });
        });
    });

    /**
     * Инициализация всех полей
     */
    function initYourField() {
        $('.wpp-your-field').each(function() {
            initYourFieldInstance($(this));
        });
    }

    /**
     * Инициализация одного экземпляра поля
     */
    function initYourFieldInstance($field) {
        // Ваша логика инициализации
        console.log('Initialized your field:', $field.attr('name'));
    }

    /**
     * Обработка изменения значения
     */
    function handleYourFieldChange($field) {
        // Ваша логика при изменении
        console.log('Field changed:', $field.val());
    }

})(jQuery);
```

## 🔧 Шаг 3: Создание стилей (если нужны)

Создайте файл `style.css`:

```css
/**
 * WPP Your Field Styles
 */

.wpp-your-field {
    /* Базовые стили */
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.wpp-your-field:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Дополнительные классы */
.wpp-your-field.has-error {
    border-color: #dc3545;
}

.wpp-your-field-wrapper {
    position: relative;
}
```

## 🔧 Шаг 4: Создание документации

Создайте `README.md` по шаблону других полей:

```markdown
# Поле: Ваше название — `WPP_Your_Field_Field`

Краткое описание функциональности.

## ⚙️ Параметры

| Параметр       | Тип         | Описание |
|----------------|-------------|----------|
| `type`         | string      | `'your_field'` |
| `name`         | string      | Имя поля (обязательно) |
| `label`        | string      | Подпись к полю |
| ...            | ...         | ... |

## ✅ Примеры использования

### 1. Базовый пример

```php
new WPP_Your_Field_Field([
    'type' => 'your_field',
    'name' => 'my_field',
    'label' => 'Моё поле'
]);
```

## 📝 Примечания

- Важные замечания по использованию

## 🔧 Особенности

- Уникальные возможности поля
```

## 🔧 Шаг 5: Регистрация поля (автоматическая)

Поле автоматически регистрируется при создании экземпляра. Убедитесь, что:

1. Класс назван правильно: `WPP_{TypeName}_Field`
2. Файл находится в папке `/fields/{type_name}/`
3. Класс расширяет `WPP_Form_Field`

## 📝 Полная структура проекта

```
wpp-field-builder-manager/
├── fields/
│   ├── your_field/
│   │   ├── README.md
│   │   ├── WPP_Your_Field_Field.php
│   │   ├── script.js
│   │   └── style.css
│   ├── text/
│   ├── select/
│   └── ...
├── admin/
├── includes/
└── ...
```

## 🎯 Лучшие практики

### 1. Именование

- Используйте префикс `WPP_` для всех классов
- Называйте файлы в соответствии с классом
- Используйте snake_case для имён файлов и kebab-case для CSS-классов

### 2. Безопасность

- Всегда экранируйте вывод: `esc_html()`, `esc_attr()`, `esc_url()`
- Проверяйте nonce в AJAX-запросах
- Валидируйте и санитизируйте все входные данные
- Используйте `current_user_can()` для проверки прав

### 3. Производительность

- Подключайте JS/CSS только если поле используется
- Используйте versioning на основе `filemtime()`
- Минимизируйте DOM-операции в JavaScript
- Кэшируйте тяжёлые вычисления

### 4. Совместимость

- Поддерживайте работу в админке и на фронтенде
- Тестируйте с разными темами WordPress
- Проверяйте работу с другими плагинами
- Учитывайте возможность отключения JavaScript

### 5. Документирование

- Добавляйте PHPDoc комментарии
- Создавайте подробный README
- Приводите примеры использования
- Описывайте все параметры и методы

## 🔍 Отладка

### Логирование ошибок

```php
error_log('WPP_Your_Field: ' . print_r($data, true));
```

### Консоль браузера

```javascript
console.log('Your Field:', data);
```

### WordPress Debug Mode

В `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📦 Публикация

Перед публикацией убедитесь:

- [ ] Все файлы созданы
- [ ] Код соответствует стандартам WordPress
- [ ] Документация полная
- [ ] Протестировано на разных окружениях
- [ ] Нет ошибок в консоли
- [ ] Работает с repeater и accordion
- [ ] Корректная валидация данных

## 💡 Примеры из существующих полей

Изучите реализацию других полей для вдохновения:

- **Простое поле**: `/fields/text/WPP_Text_Field.php`
- **Поле с опциями**: `/fields/select/WPP_Select_Field.php`
- **Сложное поле**: `/fields/repeater/WPP_Repeater_Field.php`
- **Поле с JS**: `/fields/datepicker/WPP_Datepicker_Field.php`
- **Группирующее поле**: `/fields/fields_block/WPP_Fields_Block_Field.php`

## 🆘 Поддержка

При возникновении проблем:

1. Проверьте логи ошибок WordPress
2. Включите debug mode
3. Проверьте консоль браузера
4. Убедитесь, что все зависимости подключены
5. Сверьтесь с документацией других полей
