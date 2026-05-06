jQuery(document).ready(function ($) {
    // Обработчик кнопки загрузки
    $(document).on('click', '.upload-btn', function () {
        console.log('=== UPLOAD BUTTON CLICKED ===');
        console.log('Button:', this);
        console.log('wpp_docs_ajax:', typeof wpp_docs_ajax !== 'undefined' ? wpp_docs_ajax : 'NOT DEFINED');

        const fileInput = $(this).siblings('.document-file');
        console.log('File input found:', fileInput.length);

        if (fileInput.length > 0) {
            fileInput.click();
        } else {
            console.error('File input not found!');
        }
    });

    // Обработчик выбора файла
    $(document).on('change', '.document-file', function () {
        console.log('=== FILE SELECTED ===');
        const file = this.files[0];
        const key = $(this).data('key');
        const $container = $(this).closest('.wpp-documents-upload-field');
        const loanId = $container.data('loan-id');

        console.log('File:', file);
        console.log('Key:', key);
        console.log('Loan ID:', loanId);
        console.log('wpp_docs_ajax:', typeof wpp_docs_ajax !== 'undefined' ? wpp_docs_ajax : 'NOT DEFINED');

        if (file && loanId) {
            uploadDocument(file, key, loanId, $container);
            // Сбрасываем значение input чтобы можно было загрузить тот же файл снова
            $(this).val('');
        } else if (!loanId) {
            alert('Please select a loan first');
        }
    });

    // Функция загрузки документа
    function uploadDocument(file, key, applicationId, $container) {
        console.log('=== UPLOADING DOCUMENT ===');
        console.log('File:', file.name);
        console.log('Key:', key);
        console.log('Application ID:', applicationId);

        // Проверяем wpp_docs_ajax
        if (typeof wpp_docs_ajax === 'undefined') {
            console.error('wpp_docs_ajax is NOT defined!');
            alert('AJAX configuration not loaded. Please refresh the page.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'upload_application_document');
        formData.append('application_id', applicationId);
        formData.append('document_key', key);
        formData.append('document_file', file);
        formData.append('nonce', wpp_docs_ajax.nonce);

        console.log('Sending upload request...');

        // Показываем индикатор загрузки
        const $filesContainer = $container.find('.uploaded-files-container');
        showContainerPreloader($filesContainer);

        $.ajax({
            url: wpp_docs_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log('Upload response:', response);
                if (response.success) {
                    loadDocumentsForKey(applicationId, key, $filesContainer);
                } else {
                    alert('Error when uploading a file: ' + response.data);
                    $filesContainer.html('<span class="text-danger">Upload failed</span>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Upload error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Error when uploading a file');
                $filesContainer.html('<span class="text-danger">Upload failed</span>');
            }
        });
    }

    // Функция загрузки документов для ключа
    function loadDocumentsForKey(applicationId, key, $container) {
        if (!applicationId) {
            $container.html('<span class="text-muted">Select a loan first</span>');
            return;
        }

        $.ajax({
            url: wpp_docs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_application_documents',
                application_id: applicationId,
                document_key: key,
                nonce: wpp_docs_ajax.nonce
            },
            beforeSend: function () {
                if ($container.find('.spinner-border').length === 0) {
                    showContainerPreloader($container);
                }
            },
            success: function (response) {
                if (response.success) {
                    $container.html(response.data);
                } else {
                    $container.html('<span class="text-danger">Error loading files: ' + response.data + '</span>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Load documents error:', error);
                $container.html('<span class="text-danger">Error loading files</span>');
            }
        });
    }

    // Функция показа прелоадера для контейнера
    function showContainerPreloader(container) {
        container.html(`
            <div class="text-center py-2">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <span class="text-muted ml-2">Loading...</span>
            </div>
        `);
    }

    // Обработчик удаления документа
    $(document).on('click', '.delete-document', function (e) {
        e.preventDefault();

        const fileName = $(this).data('file');
        const key = $(this).data('key');
        const $container = $(this).closest('.uploaded-files-container');
        const loanId = $container.closest('.wpp-documents-upload-field').data('loan-id');

        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        showContainerPreloader($container);

        $.ajax({
            url: wpp_docs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_application_document',
                application_id: loanId,
                document_key: key,
                file_name: fileName,
                nonce: wpp_docs_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    loadDocumentsForKey(loanId, key, $container);
                } else {
                    alert('Error when deleting a file: ' + response.data);
                    loadDocumentsForKey(loanId, key, $container);
                }
            },
            error: function (xhr, status, error) {
                console.error('Delete error:', error);
                alert('Error when deleting a file');
                loadDocumentsForKey(loanId, key, $container);
            }
        });
    });

    // Загрузка документов при загрузке страницы - ОТКЛЮЧАЕМ для табов
    // loadAllDocuments(); // Не вызываем автоматически!

    // Обновляем документы при изменении loan_id (если есть динамическое изменение)
    $(document).on('loan_changed', function (event, loanId) {
        $('.wpp-documents-upload-field').data('loan-id', loanId);
        loadAllDocuments();
    });


    $(document).on('change', '.wpp-status-doc-select', function (e) {
        e.preventDefault();

        var $el = $(this),
            $val = $el.val(),
            $key = $el.attr('data-document-key'),
            $parent_el = $el.parents('.wpp-documents-upload-field'),
            $id = $parent_el.attr('data-loan-id'),
            $field = $parent_el.attr('data-document-key');

        $.ajax({
            url: wpp_docs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'change_doc_status',
                value: $val,
                document_key: $key,
                loan_id: $id,
                field_id: $field,
            },
            success: function (response) {
                if (response.success) {

                } else {
                    alert('Error when deleting a file: ' + response.data);

                }
            },
            error: function (xhr, status, error) {
                console.error('Delete error:', error);
                alert('Error when deleting a file');
            }
        });


    })


});
