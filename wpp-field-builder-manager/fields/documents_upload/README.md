# Поле: Загрузка документов — `WPP_Documents_Upload_Field`

Поле для загрузки и управления документами через AJAX. Поддерживает множественную загрузку, предпросмотр и удаление файлов.

## ⚙️ Параметры

| Параметр       | Тип         | Описание |
|----------------|-------------|----------|
| `type`         | string      | `'documents_upload'` |
| `name`         | string      | Имя поля (обязательно) |
| `label`        | string      | Подпись к полю |
| `description`  | string      | Описание под полем |
| `document_key` | string      | Ключ документа в системе (по умолчанию извлекается из name) |
| `classes`      | array       | Дополнительные CSS-классы |
| `width`        | string      | Ширина: full, 1/2, 1/3 и т.д. |
| `conditional`  | array       | Условия отображения |

## ✅ Примеры

### 1. Базовая загрузка документа

```php
new WPP_Documents_Upload_Field([
    'type' => 'documents_upload',
    'name' => 'rd_passport',
    'label' => 'Паспорт',
    'description' => 'Загрузите скан паспорта (PDF, JPG, PNG)',
    'width' => 'full'
]);
```

### 2. Загрузка с явным ключом документа

```php
new WPP_Documents_Upload_Field([
    'type' => 'documents_upload',
    'name' => 'doc_income_certificate',
    'label' => 'Справка о доходах',
    'document_key' => 'income_cert',
    'description' => '2-НДФЛ или справка по форме банка',
    'width' => 'full'
]);
```

### 3. Несколько документов в форме

```php
$fields = [
    'passport_section' => [
        'type' => 'content',
        'label' => 'Документы, удостоверяющие личность',
        'content' => '<hr>',
        'width' => 'full'
    ],
    
    'passport_main' => [
        'type' => 'documents_upload',
        'name' => 'rd_passport_main',
        'label' => 'Паспорт - главная страница',
        'document_key' => 'passport_main',
        'width' => '1/2'
    ],
    
    'passport_registration' => [
        'type' => 'documents_upload',
        'name' => 'rd_passport_reg',
        'label' => 'Паспорт - регистрация',
        'document_key' => 'passport_reg',
        'width' => '1/2'
    ],
    
    'income_section' => [
        'type' => 'content',
        'label' => 'Финансовые документы',
        'content' => '<hr>',
        'width' => 'full'
    ],
    
    'income_2ndfl' => [
        'type' => 'documents_upload',
        'name' => 'rd_2ndfl',
        'label' => 'Справка 2-НДФЛ',
        'document_key' => '2ndfl',
        'width' => '1/2'
    ],
    
    'bank_statement' => [
        'type' => 'documents_upload',
        'name' => 'rd_bank_statement',
        'label' => 'Выписка из банка',
        'document_key' => 'bank_statement',
        'width' => '1/2'
    ]
];
```

### 4. Документы внутри аккордеона

```php
new WPP_Super_Accordion_Field([
    'type' => 'super_accordion',
    'name' => 'documents_accordion',
    'title' => 'Загрузка документов',
    'open' => false,
    'fields' => [
        'passport_doc' => [
            'type' => 'documents_upload',
            'name' => 'rd_passport',
            'label' => 'Паспорт',
            'document_key' => 'passport'
        ],
        'snils_doc' => [
            'type' => 'documents_upload',
            'name' => 'rd_snils',
            'label' => 'СНИЛС',
            'document_key' => 'snils'
        ],
        'inn_doc' => [
            'type' => 'documents_upload',
            'name' => 'rd_inn',
            'label' => 'ИНН',
            'document_key' => 'inn'
        ]
    ]
]);
```

### 5. Документы внутри repeater

```php
new WPP_Repeater_Field([
    'type' => 'repeater',
    'name' => 'additional_documents',
    'title' => 'Дополнительный документ',
    'min' => 0,
    'max' => 5,
    'fields' => [
        'doc_type' => [
            'type' => 'select',
            'label' => 'Тип документа',
            'options' => [
                'driver_license' => 'Водительское удостоверение',
                'pension_card' => 'Пенсионное удостоверение',
                'student_card' => 'Студенческий билет',
                'other' => 'Другое'
            ]
        ],
        'doc_file' => [
            'type' => 'documents_upload',
            'name' => 'rd_additional',
            'label' => 'Файл',
            'document_key' => 'additional_doc'
        ]
    ]
]);
```

## 📝 Примечания

- **Зависимость от Loan ID**: поле требует наличия `loan_id` в URL или глобальной переменной
- **AJAX-загрузка**: файлы загружаются асинхронно через admin-ajax.php
- **Поддерживаемые форматы**: PDF, DOC, DOCX, JPG, JPEG, PNG, TXT
- **Font Awesome**: автоматически подключается CDN для иконок
- **Nonce-защита**: используется WordPress nonce для безопасности

## 🔧 Особенности

- Автоматическая подгрузка списка уже загруженных файлов
- Предпросмотр загруженных документов с иконками типов файлов
- Возможность удаления файлов через интерфейс
- Индикация процесса загрузки (spinner)
- Валидация типов файлов на стороне клиента
- Поддержка множественной загрузки (через repeater)

## 🎨 Стилизация

Базовые стили подключаются автоматически. Для кастомизации:

```css
.wpp-documents-upload-wrap {
    margin-bottom: 1rem;
}

.uploaded-files-container {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    background: #f8f9fa;
}

.uploaded-file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    background: white;
    border-radius: 0.25rem;
}

.uploaded-file-item .file-icon {
    font-size: 1.5rem;
    color: #007bff;
}

.uploaded-file-item .file-name {
    flex-grow: 1;
    margin-left: 1rem;
}

.uploaded-file-item .delete-btn {
    color: #dc3545;
    cursor: pointer;
}
```

## 🔐 Безопасность

- Проверка nonce перед каждой операцией
- Валидация типа файла по расширению
- Проверка прав доступа к loan_id
- Санитизация имён файлов
- Ограничение размера на стороне сервера (настраивается отдельно)

## 💡 Советы по использованию

1. **Группировка**: объединяйте документы по категориям с помощью `WPP_Content_Field`
2. **Подсказки**: указывайте допустимые форматы в `description`
3. **Обязательность**: визуально отмечайте обязательные документы
4. **Прогресс**: для больших файлов добавьте индикатор прогресса
5. **Предзаполнение**: при редактировании автоматически показывайте загруженные файлы

## 🔄 AJAX-события

Поле использует следующие AJAX-действия:

- `wpp_upload_document` - загрузка файла
- `wpp_delete_document` - удаление файла
- `wpp_get_documents` - получение списка документов

Все запросы требуют valid nonce (`wpp_docs_nonce`).
