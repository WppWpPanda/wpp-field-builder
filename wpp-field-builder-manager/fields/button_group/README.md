# WPP Button Group Field

Поле `button_group` реализует группу кнопок, которые работают как радио-поля.  
Подходит для выбора одного варианта из нескольких с визуальным акцентом на кнопках вместо стандартных радио-переключателей.

---

## 📌 Основные функции

| Функция | Описание |
|--------|----------|
| Поддержка горизонтального и вертикального отображения | `'orientation' => 'horizontal' / 'vertical'` |
| Совместимость с Bootstrap | Используется `btn-group` / `btn-group-vertical` |
| Сохранение значения | Через скрытое поле `<input type="hidden">` |
| Условная логика (`conditional`) | Работает через `data-condition` |
| AJAX-отправка | Полностью поддерживается |

---

## ⚙️ Параметры

| Параметр        | Тип         | Описание |
|------------------|-------------|----------|
| `type`           | string      | Должен быть `'button_group'` |
| `name`           | string      | Имя поля (обязательный) |
| `label`          | string      | Заголовок поля |
| `description`    | string      | Описание под полем |
| `options`        | array       | Список кнопок: `[ 'value' => 'Label' ]` |
| `default`        | string      | Значение по умолчанию |
| `orientation`    | string      | `'horizontal'` или `'vertical'` |
| `width`          | string      | full, 1/2, 1/3 и т.д. |
| `classes`        | array       | Дополнительные CSS-классы |
| `conditional`    | array       | Условия отображения (например: `['investment_type' => 'new_construction']`) |

---

## ✅ Пример использования

### 1. Горизонтальная группа кнопок

```php
'investment_type' => [
    'type' => 'button_group',
    'name' => 'investment_type',
    'label' => 'What kind of real estate investment are you considering?',
    'description' => 'Select one option below',
    'options' => [
        'bridge_fix_flip_rent' => 'Bridge / Fix and Flip / Fix to Rent',
        'new_construction' => 'New Construction'
    ],
    'width' => 'full'
]
```

### 2. Вертикальная группа кнопок
```php
'investment_type_vertical' => [
    'type' => 'button_group',
    'name' => 'investment_type',
    'label' => 'Choose an option',
    'options' => [
        'option_1' => 'Option One',
        'option_2' => 'Option Two',
        'option_3' => 'Option Three'
    ],
    'orientation' => 'vertical', // Отображение кнопок вертикально
    'width' => 'full'
]