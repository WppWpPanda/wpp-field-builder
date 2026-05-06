# Поле: Адрес с Google Autocomplete — `WPP_Address_Field`

Реализует текстовое поле с автозаполнением через Google Maps API.

## ⚙️ Параметры

| Параметр         | Тип      | Описание |
|------------------|----------|----------|
| `name`           | string   | Имя поля (обязательный) |
| `label`          | string   | Подпись к полю |
| `description`    | string   | Описание под полем |
| `default`        | string   | Значение по умолчанию |
| `classes`        | array    | Дополнительные CSS-классы |
| `width`          | string   | full, 1/2, 1/3 и т.д. |
| `conditional`    | array    | Условия отображения |

## 🔑 Тестовый Google API Key
AIzaSyD5ge7CvA3Q0OqFjMj9WugjR4lXa2Z6iUk


📌 Этот ключ работает только в тестовой среде. Для продакшена нужно получить свой ключ с доступом к **Places API** и **Maps JavaScript API**

---

## ✅ Пример использования

### 1. Обычное поле адреса

```php
new WPP_Address_Field([
    'type'        => 'address',
    'name'        => 'user_address',
    'label'       => 'Адрес доставки',
    'placeholder' => 'Введите адрес',
    'width'       => 'full'
]);
```

2. Поле с условной логикой

```php
new WPP_Address_Field([
    'type'        => 'address',
    'name'        => 'shipping_address',
    'label'       => 'Адрес доставки',
    'placeholder' => 'Введите адрес',
    'width'       => 'full',
    'conditional' => ['country' => 'США']
]);
```

3. Поле с кастомными классами

```php
new WPP_Address_Field([
    'type'        => 'address',
    'name'        => 'office_address',
    'label'       => 'Адрес офиса',
    'placeholder' => 'Введите адрес',
    'classes'     => ['custom-input', 'mb-3'],
    'width'       => 'full'
]);
```