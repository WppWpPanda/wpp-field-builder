# Select Field — `WPP_Select_Field`

Реализует выпадающий список `<select>` с поддержкой:
- Обычного выбора
- Множественного выбора (`multiple`)
- Инициализации через Select2 (если нужно)

---

## 🧩 Типы полей

| Режим | Описание |
|-------|----------|
| Обычный `select` | Стандартное HTML-поле |
| Множественный выбор (`multiple`) | Позволяет выбрать несколько значений |
| Select2 (`'select2' => true`) | Улучшенный интерфейс с поиском, иконками и т.д. |

---

## ⚙️ Поддерживаемые параметры

| Параметр       | Тип         | Описание |
|----------------|-------------|----------|
| `name`         | string      | Имя поля (обязательно) |
| `label`        | string      | Подпись к полю |
| `description`  | string      | Описание под полем |
| `default`      | string/array| Значение по умолчанию (или массив для multiple) |
| `options`      | array       | Список опций: `[value => label]` |
| `classes`      | array       | Дополнительные CSS-классы |
| `width`        | string      | Ширина: full, 1/2, 1/3, 2/3, 1/4 |
| `conditional`  | array       | Условия отображения: `['field_name' => 'value']` |
| `validation`   | callable    | Callback-функция валидации |
| `multiple`     | boolean     | Включает множественный выбор |
| `select2`      | boolean     | Если `true`, инициализирует Select2 (требуется JS/CSS) |

---

## ✅ Примеры использования

### 1. Обычный селект

```php
new WPP_Select_Field([
    'name'     => 'country',
    'label'    => 'Страна',
    'default'  => 'ru',
    'options'  => [
        'ru' => 'Россия',
        'ua' => 'Украина',
        'kz' => 'Казахстан'
    ],
    'classes' => ['form-control'],
    'width'   => 'full'
]);
```

### 2. Селект с множественным выбором
```php
new WPP_Select_Field([
    'name'     => 'colors',
    'label'    => 'Любимые цвета',
    'default'  => ['red', 'blue'],
    'options'  => [
        'red'   => 'Красный',
        'green' => 'Зелёный',
        'blue'  => 'Синий'
    ],
    'multiple' => true,
    'width'    => '1/2'
]);
```

### 3. Select2 (улучшенный интерфейс)
```php
new WPP_Select_Field([
    'name'     => 'cities',
    'label'    => 'Город проживания',
    'default'  => 'moscow',
    'options'  => [
        'moscow' => 'Москва',
        'spb'    => 'Санкт-Петербург',
        'ekb'    => 'Екатеринбург'
    ],
    'select2' => true,
    'width'   => 'full'
]);
```

### 4. Select2 с множественным выбором и условной логикой
```php
new WPP_Select_Field([
    'name'        => 'hobbies',
    'label'       => 'Ваши увлечения',
    'default'     => ['reading', 'sports'],
    'options'     => [
        'reading' => 'Чтение',
        'sports'  => 'Спорт',
        'music'   => 'Музыка',
        'games'   => 'Игры'
    ],
    'select2'     => true,
    'multiple'    => true,
    'conditional' => ['subscribe_newsletter' => '1']
]);
```