/**
 * JavaScript для административной панели конструктора форм WPP
 *
 * @package WPP_Field_Builder_Manager
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Основной класс конструктора форм
	 */
	const WPPFormBuilder = {
		
		/**
		 * Конфигурация формы (массив полей)
		 */
		formConfig: [],

		/**
		 * ID текущего выбранного поля
		 */
		selectedFieldId: null,

		/**
		 * Инициализация конструктора
		 */
		init: function() {
			this.initPalette();
			this.initCanvas();
			this.initToolbar();
			this.initModal();
			this.loadSavedConfig();
		},

		/**
		 * Инициализация палитры полей (drag and drop)
		 */
		initPalette: function() {
			const self = this;
			
			$('.wpp-palette-field').draggable({
				helper: 'clone',
				revert: 'invalid',
				cursor: 'move',
				zIndex: 1000,
				start: function() {
					$(this).addClass('dragging');
				},
				stop: function() {
					$(this).removeClass('dragging');
				}
			});
		},

		/**
		 * Инициализация canvas (области формы)
		 */
		initCanvas: function() {
			const self = this;
			const $canvas = $('#wpp-form-canvas');

			// Разрешаем drop на canvas
			$canvas.droppable({
				accept: '.wpp-palette-field, .wpp-form-field-item',
				over: function() {
					$(this).addClass('drag-over');
				},
				out: function() {
					$(this).removeClass('drag-over');
				},
				drop: function(event, ui) {
					$(this).removeClass('drag-over');
					
					if (ui.draggable.hasClass('wpp-palette-field')) {
						// Добавление нового поля из палитры
						const fieldType = ui.draggable.data('field-type');
						self.addField(fieldType);
					} else if (ui.draggable.hasClass('wpp-form-field-item')) {
						// Перемещение существующего поля
						ui.draggable.appendTo($canvas);
						self.updateFieldOrder();
					}
				}
			});

			// Делаем поля в canvas перетаскиваемыми
			$canvas.sortable({
				items: '.wpp-form-field-item',
				placeholder: 'ui-sortable-placeholder',
				handle: '.wpp-field-header',
				opacity: 0.7,
				update: function() {
					self.updateFieldOrder();
				}
			});
		},

		/**
		 * Инициализация панели инструментов
		 */
		initToolbar: function() {
			const self = this;

			// Кнопка добавления поля (открывает модальное окно выбора)
			$('#wpp-add-new-field').on('click', function() {
				self.showFieldSelector();
			});

			// Кнопка копирования конфигурации
			$('#wpp-copy-config').on('click', function() {
				self.copyConfig();
			});

			// Кнопка очистки формы
			$('#wpp-clear-form').on('click', function() {
				if (confirm(wppFormBuilderData.i18n.confirmDelete)) {
					self.clearForm();
				}
			});
		},

		/**
		 * Инициализация модального окна
		 */
		initModal: function() {
			const self = this;
			const $modal = $('#wpp-config-modal');

			// Закрытие модального окна
			$('.wpp-modal-close, #wpp-close-modal').on('click', function() {
				$modal.hide();
			});

			// Закрытие по клику вне окна
			$modal.on('click', function(e) {
				if ($(e.target).is($modal)) {
					$modal.hide();
				}
			});

			// Копирование из модального окна
			$('#wpp-copy-from-modal').on('click', function() {
				self.copyToClipboard($('#wpp-config-output').val());
			});

			// Клавиша Escape для закрытия
			$(document).on('keydown', function(e) {
				if (e.key === 'Escape' && $modal.is(':visible')) {
					$modal.hide();
				}
			});
		},

		/**
		 * Загрузка сохранённой конфигурации (из localStorage)
		 */
		loadSavedConfig: function() {
			const saved = localStorage.getItem('wpp_form_builder_config');
			if (saved) {
				try {
					this.formConfig = JSON.parse(saved);
					this.renderFormFromConfig();
				} catch (e) {
					console.error('Ошибка при загрузке конфигурации:', e);
					this.showNotification('Ошибка при загрузке сохранённой конфигурации', 'error');
				}
			}
		},

		/**
		 * Добавление нового поля в форму
		 * 
		 * @param {string} fieldType Тип поля
		 * @param {object} settings Настройки поля (опционально)
		 */
		addField: function(fieldType, settings) {
			const fieldInfo = this.getFieldInfo(fieldType);
			if (!fieldInfo) {
				console.error('Неизвестный тип поля:', fieldType);
				return;
			}

			const fieldId = 'field_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
			
			const defaultSettings = {
				id: fieldId,
				type: fieldType,
				name: fieldType + '_' + ($('#wpp-form-canvas .wpp-form-field-item').length + 1),
				label: fieldInfo.label,
				placeholder: '',
				required: false,
				width: 'full',
				options: ['select', 'multiselect', 'radio'].includes(fieldType) ? ['Опция 1', 'Опция 2'] : undefined,
				conditional_logic: [],
				// Специфичные настройки для сложных полей
				title: ['accordion', 'super_accordion', 'repeater'].includes(fieldType) ? fieldInfo.label : undefined,
				header: fieldType === 'super_accordion' ? '' : undefined,
				fields: ['accordion', 'fields_block', 'super_accordion', 'repeater'].includes(fieldType) ? [] : undefined,
				button_text: fieldType === 'repeater' ? '+ Добавить' : undefined,
				min: fieldType === 'repeater' ? 1 : undefined,
				max: fieldType === 'repeater' ? 999 : undefined
			};

			const fieldConfig = $.extend({}, defaultSettings, settings || {});
			this.formConfig.push(fieldConfig);
			this.saveConfig();
			this.renderField(fieldConfig);
			this.selectField(fieldId);
		},

		/**
		 * Отрисовка поля на canvas
		 * 
		 * @param {object} fieldConfig Конфигурация поля
		 */
		renderField: function(fieldConfig) {
			const fieldInfo = this.getFieldInfo(fieldConfig.type);
			const $canvas = $('#wpp-form-canvas');
			
			// Убираем placeholder если это первое поле
			$canvas.find('.wpp-canvas-placeholder').hide();

			const $fieldItem = $(`
				<div class="wpp-form-field-item" data-field-id="${fieldConfig.id}" data-field-type="${fieldConfig.type}">
					<div class="wpp-field-header">
						<div class="wpp-field-type-badge">
							<span class="dashicons ${fieldInfo.icon}"></span>
							<span>${fieldInfo.label}</span>
						</div>
						<div class="wpp-field-actions">
							<button type="button" class="button edit-field" title="Редактировать">
								<span class="dashicons dashicons-edit"></span>
							</button>
							<button type="button" class="button remove-field" title="Удалить">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</div>
					</div>
					<div class="wpp-field-preview">
						${this.getFieldPreviewHTML(fieldConfig)}
					</div>
				</div>
			`);

			// Обработчики событий
			$fieldItem.on('click', () => {
				this.selectField(fieldConfig.id);
			});

			$fieldItem.find('.edit-field').on('click', (e) => {
				e.stopPropagation();
				this.selectField(fieldConfig.id);
				this.scrollToSettings();
			});

			$fieldItem.find('.remove-field').on('click', (e) => {
				e.stopPropagation();
				if (confirm(wppFormBuilderData.i18n.confirmDelete)) {
					this.removeField(fieldConfig.id);
				}
			});

			$canvas.append($fieldItem);
		},

		/**
		 * Генерация HTML предпросмотра поля
		 * 
		 * @param {object} fieldConfig Конфигурация поля
		 * @returns {string} HTML код
		 */
		getFieldPreviewHTML: function(fieldConfig) {
			let html = '';
			const requiredMark = fieldConfig.required ? '<span class="wpp-field-required-marker">*</span>' : '';
			const label = fieldConfig.label ? `<label>${fieldConfig.label}${requiredMark}</label>` : '';

			switch (fieldConfig.type) {
				case 'text':
				case 'email':
				case 'tel':
				case 'number':
				case 'date':
					html = `${label}<input type="${fieldConfig.type}" placeholder="${fieldConfig.placeholder || ''}" disabled>`;
					break;

				case 'textarea':
					html = `${label}<textarea placeholder="${fieldConfig.placeholder || ''}" disabled></textarea>`;
					break;

				case 'select':
				case 'multiselect':
					const selectMultiple = fieldConfig.type === 'multiselect' ? 'multiple' : '';
					let optionsHTML = '<option value="">Выберите...</option>';
					if (fieldConfig.options && fieldConfig.options.length) {
						optionsHTML += fieldConfig.options.map(opt => `<option value="${opt}">${opt}</option>`).join('');
					}
					html = `${label}<select ${selectMultiple}>${optionsHTML}</select>`;
					break;

				case 'checkbox':
					html = `${label}<div class="wpp-checkbox-group"><input type="checkbox" disabled> <span>${fieldConfig.label}</span></div>`;
					break;

				case 'radio':
					let radioHTML = '';
					if (fieldConfig.options && fieldConfig.options.length) {
						radioHTML = fieldConfig.options.map((opt, i) => 
							`<div class="wpp-checkbox-group"><input type="radio" name="${fieldConfig.name}" disabled> <span>${opt}</span></div>`
						).join('');
					}
					html = `${label}${radioHTML}`;
					break;

				case 'file':
					html = `${label}<input type="file" disabled>`;
					break;

				case 'accordion':
					const accordionId = fieldConfig.name + '_acc';
					const accordionTitle = fieldConfig.title || fieldConfig.label || 'Аккордеон';
					html = `${label}
						<div class="accordion" id="${accordionId}">
							<div class="accordion-item">
								<h2 class="accordion-header">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${accordionId}-collapse">
										${accordionTitle}
									</button>
								</h2>
								<div id="${accordionId}-collapse" class="accordion-collapse collapse" data-bs-parent="#${accordionId}">
									<div class="accordion-body">
										<div class="description">Настройте поля аккордеона в панели настроек</div>
									</div>
								</div>
							</div>
						</div>`;
					break;

				case 'fields_block':
					const blockLabel = fieldConfig.label || 'Блок полей';
					html = `${label}
						<div class="wpp-fields-block-preview border rounded p-3">
							<label class="fw-bold mb-2">${blockLabel}</label>
							<div class="row">
								<div class="col-12 text-muted small">
									<i class="dashicons dashicons-plus"></i> Добавьте поля в настройках блока
								</div>
							</div>
						</div>`;
					break;

				case 'super_accordion':
					const superAccId = fieldConfig.name + '_sacc';
					const superAccTitle = fieldConfig.title || fieldConfig.label || 'Супер Аккордеон';
					const headerTemplate = fieldConfig.header || '';
					html = `${label}
						<div class="wpp-super-accordion" id="${superAccId}" data-header="${headerTemplate}">
							<div class="wpp-super-accordion-header">
								<h5 class="row">${superAccTitle}</h5>
								<span class="toggle-icon">▶</span>
							</div>
							<div class="wpp-super-accordion-body row" style="display: none;">
								<div class="col-12 text-muted small">
									<i class="dashicons dashicons-plus"></i> Настройте поля в панели настроек
								</div>
							</div>
						</div>`;
					break;

				case 'repeater':
					const repeaterName = fieldConfig.name;
					const repeaterTitle = fieldConfig.title || fieldConfig.label || 'Повторитель';
					const buttonText = fieldConfig.button_text || '+ Добавить';
					html = `${label}
						<div class="wpp-repeater-container" data-name="${repeaterName}">
							<div class="wpp-repeater-header d-flex justify-content-between align-items-center mb-3">
								<h5>${repeaterTitle}</h5>
								<button type="button" class="btn btn-sm btn-success wpp-repeater-add" disabled>${buttonText}</button>
							</div>
							<div class="wpp-repeater-inner">
								<div class="wpp-repeater-block border mb-3 position-relative p-3">
									<div class="text-muted small">
										<i class="dashicons dashicons-plus"></i> Настройте поля повторителя в панели настроек
									</div>
								</div>
							</div>
						</div>`;
					break;

				default:
					html = `${label}<input type="text" disabled>`;
			}

			return html;
		},

		/**
		 * Выбор поля для редактирования
		 * 
		 * @param {string} fieldId ID поля
		 */
		selectField: function(fieldId) {
			this.selectedFieldId = fieldId;
			
			// Снимаем выделение со всех полей
			$('.wpp-form-field-item').removeClass('selected');
			
			// Выделяем текущее поле
			$(`.wpp-form-field-item[data-field-id="${fieldId}"]`).addClass('selected');
			
			// Отрисовываем панель настроек
			this.renderSettingsPanel(fieldId);
		},

		/**
		 * Отрисовка панели настроек выбранного поля
		 * 
		 * @param {string} fieldId ID поля
		 */
		renderSettingsPanel: function(fieldId) {
			const fieldConfig = this.getFieldConfig(fieldId);
			if (!fieldConfig) {
				return;
			}

			const fieldInfo = this.getFieldInfo(fieldConfig.type);
			const $settingsPanel = $('#wpp-settings-panel');

			let optionsHTML = '';
			if (['select', 'multiselect', 'radio'].includes(fieldConfig.type) && fieldConfig.options) {
				optionsHTML = `
					<div class="wpp-setting-group">
						<label>${wppFormBuilderData.i18n.options}</label>
						<div class="wpp-options-list">
							${fieldConfig.options.map((opt, i) => `
								<div class="wpp-option-item">
									<input type="text" value="${opt}" data-option-index="${i}">
									<button type="button" class="button remove-option" data-option-index="${i}">
										<span class="dashicons dashicons-no"></span>
									</button>
								</div>
							`).join('')}
						</div>
						<button type="button" class="button wpp-add-option-btn">
							<span class="dashicons dashicons-plus-alt"></span>
							${wppFormBuilderData.i18n.addOption}
						</button>
					</div>
				`;
			}

			let conditionalLogicHTML = '';
			if (fieldConfig.conditional_logic && fieldConfig.conditional_logic.length > 0) {
				conditionalLogicHTML = `
					<div class="wpp-conditional-rules">
						${fieldConfig.conditional_logic.map((rule, i) => `
							<div class="wpp-conditional-rule" data-rule-index="${i}">
								<select class="rule-field" data-rule-index="${i}">
									<option value="">${wppFormBuilderData.i18n.showIf}</option>
									${this.getFieldsOptions(fieldConfig.id)}
								</select>
								<select class="rule-operator" data-rule-index="${i}">
									<option value="equals" ${rule.operator === 'equals' ? 'selected' : ''}>${wppFormBuilderData.i18n.equals}</option>
									<option value="not_equals" ${rule.operator === 'not_equals' ? 'selected' : ''}>${wppFormBuilderData.i18n.notEquals}</option>
									<option value="contains" ${rule.operator === 'contains' ? 'selected' : ''}>${wppFormBuilderData.i18n.contains}</option>
								</select>
								<input type="text" class="rule-value" value="${rule.value || ''}" placeholder="Значение" data-rule-index="${i}">
								<button type="button" class="button wpp-remove-rule" data-rule-index="${i}">
									<span class="dashicons dashicons-no"></span>
								</button>
							</div>
						`).join('')}
					</div>
				`;
			}

			// Специфичные настройки для сложных полей
			let complexFieldsSettingsHTML = '';
			if (['accordion', 'super_accordion', 'repeater', 'fields_block'].includes(fieldConfig.type)) {
				const fieldsCount = fieldConfig.fields ? fieldConfig.fields.length : 0;
				const fieldsListHTML = fieldConfig.fields && fieldConfig.fields.length > 0 
					? `<div class="wpp-subfields-list">${fieldConfig.fields.map((f, i) => `
						<div class="wpp-subfield-item" data-index="${i}">
							<span class="dashicons ${this.getFieldInfo(f.type)?.icon || 'dashicons-admin-generic'}"></span>
							<span class="subfield-name">${f.label || f.name}</span>
							<button type="button" class="button edit-subfield" data-index="${i}">
								<span class="dashicons dashicons-edit"></span>
							</button>
							<button type="button" class="button remove-subfield" data-index="${i}">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</div>`).join('')}</div>`
					: '<div class="wpp-no-subfields"><em>Поля не добавлены</em></div>';
				
				complexFieldsSettingsHTML += `
					<div class="wpp-setting-group wpp-subfields-section">
						<label>Поля внутри ${fieldInfo.label}</label>
						${fieldsListHTML}
						<div class="wpp-subfields-actions" style="margin-top: 10px;">
							<button type="button" class="button button-primary wpp-add-subfield-btn" data-field-id="${fieldId}">
								<span class="dashicons dashicons-plus-alt"></span>
								Добавить поле
							</button>
						</div>
					</div>
				`;
			}

			if (fieldConfig.type === 'super_accordion') {
				complexFieldsSettingsHTML += `
					<div class="wpp-setting-group">
						<label for="field-header">Шаблон заголовка (используйте {field_name})</label>
						<input type="text" id="field-header" value="${fieldConfig.header || ''}" data-setting="header">
						<span class="description">Пример: {name} - {email}</span>
					</div>
				`;
			}

			if (fieldConfig.type === 'repeater') {
				complexFieldsSettingsHTML += `
					<div class="wpp-setting-group">
						<label for="field-button-text">Текст кнопки добавления</label>
						<input type="text" id="field-button-text" value="${fieldConfig.button_text || '+ Добавить'}" data-setting="button_text">
					</div>
					<div class="wpp-setting-group">
						<label for="field-min">Минимум блоков</label>
						<input type="number" id="field-min" value="${fieldConfig.min || 1}" data-setting="min">
					</div>
					<div class="wpp-setting-group">
						<label for="field-max">Максимум блоков</label>
						<input type="number" id="field-max" value="${fieldConfig.max || 999}" data-setting="max">
					</div>
				`;
			}

			const settingsHTML = `
				<div class="wpp-field-settings-form" data-field-id="${fieldId}">
					<div class="wpp-setting-group">
						<label for="field-name">${wppFormBuilderData.i18n.fieldName}</label>
						<input type="text" id="field-name" value="${fieldConfig.name}" data-setting="name">
						<span class="description">Уникальное имя поля (латиница)</span>
					</div>

					<div class="wpp-setting-group">
						<label for="field-label">${wppFormBuilderData.i18n.fieldLabel}</label>
						<input type="text" id="field-label" value="${fieldConfig.label || ''}" data-setting="label">
					</div>

					${!['checkbox', 'radio', 'accordion', 'fields_block', 'super_accordion', 'repeater'].includes(fieldConfig.type) ? `
					<div class="wpp-setting-group">
						<label for="field-placeholder">${wppFormBuilderData.i18n.fieldPlaceholder}</label>
						<input type="text" id="field-placeholder" value="${fieldConfig.placeholder || ''}" data-setting="placeholder">
					</div>
					` : ''}

					${complexFieldsSettingsHTML}

					<div class="wpp-setting-group">
						<label for="field-width">${wppFormBuilderData.i18n.fieldWidth}</label>
						<select id="field-width" data-setting="width">
							<option value="full" ${fieldConfig.width === 'full' ? 'selected' : ''}>На всю ширину</option>
							<option value="half" ${fieldConfig.width === 'half' ? 'selected' : ''}>Половина</option>
							<option value="third" ${fieldConfig.width === 'third' ? 'selected' : ''}>Треть</option>
							<option value="quarter" ${fieldConfig.width === 'quarter' ? 'selected' : ''}>Четверть</option>
						</select>
					</div>

					<div class="wpp-setting-group">
						<div class="wpp-checkbox-group">
							<input type="checkbox" id="field-required" ${fieldConfig.required ? 'checked' : ''} data-setting="required">
							<label for="field-required">${wppFormBuilderData.i18n.fieldRequired}</label>
						</div>
					</div>

					${optionsHTML}

					<div class="wpp-conditional-logic-section">
						<h4>${wppFormBuilderData.i18n.conditionalLogic}</h4>
						${conditionalLogicHTML}
						<button type="button" class="button wpp-add-rule-btn">
							<span class="dashicons dashicons-plus-alt"></span>
							Добавить условие
						</button>
					</div>

					<div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #c3c4c7;">
						<button type="button" class="button button-primary save-field-settings">
							<span class="dashicons dashicons-yes"></span>
							Сохранить изменения
						</button>
					</div>
				</div>
			`;

			$settingsPanel.html(settingsHTML);
			this.initSettingsHandlers(fieldId);
		},

		/**
		 * Инициализация обработчиков событий в панели настроек
		 * 
		 * @param {string} fieldId ID поля
		 */
		initSettingsHandlers: function(fieldId) {
			const self = this;
			const $settingsForm = $('.wpp-field-settings-form');

			// Изменение текстовых полей и textarea (используем input для мгновенной реакции)
			$settingsForm.find('input[type="text"], input[type="number"], textarea').on('input change', function() {
				const setting = $(this).data('setting');
				const value = $(this).val();
				self.updateFieldSetting(fieldId, setting, value);
			});

			// Изменение селектов
			$settingsForm.find('select').on('change', function() {
				const setting = $(this).data('setting');
				const value = $(this).val();
				self.updateFieldSetting(fieldId, setting, value);
			});

			// Изменение чекбокса "Обязательное"
			$settingsForm.find('#field-required').on('change', function() {
				self.updateFieldSetting(fieldId, 'required', $(this).is(':checked'));
			});

			// Добавление опции
			$settingsForm.find('.wpp-add-option-btn').on('click', function() {
				const fieldConfig = self.getFieldConfig(fieldId);
				if (!fieldConfig.options) {
					fieldConfig.options = [];
				}
				fieldConfig.options.push('Опция ' + (fieldConfig.options.length + 1));
				self.updateFieldConfig(fieldId, fieldConfig);
				self.renderSettingsPanel(fieldId);
				self.updateFieldPreview(fieldId);
			});

			// Удаление опции
			$settingsForm.on('click', '.remove-option', function() {
				const index = $(this).data('option-index');
				const fieldConfig = self.getFieldConfig(fieldId);
				if (fieldConfig.options && fieldConfig.options[index]) {
					fieldConfig.options.splice(index, 1);
					self.updateFieldConfig(fieldId, fieldConfig);
					self.renderSettingsPanel(fieldId);
					self.updateFieldPreview(fieldId);
				}
			});

			// Изменение значения опции (используем input для мгновенной реакции)
			$settingsForm.on('input change', '.wpp-option-item input[type="text"]', function() {
				const index = $(this).data('option-index');
				const value = $(this).val();
				const fieldConfig = self.getFieldConfig(fieldId);
				if (fieldConfig.options && fieldConfig.options[index]) {
					fieldConfig.options[index] = value;
					self.updateFieldConfig(fieldId, fieldConfig);
				}
			});

			// Добавление правила условной логики
			$settingsForm.find('.wpp-add-rule-btn').on('click', function() {
				const fieldConfig = self.getFieldConfig(fieldId);
				if (!fieldConfig.conditional_logic) {
					fieldConfig.conditional_logic = [];
				}
				fieldConfig.conditional_logic.push({
					field: '',
					operator: 'equals',
					value: ''
				});
				self.updateFieldConfig(fieldId, fieldConfig);
				self.renderSettingsPanel(fieldId);
			});

			// Удаление правила условной логики
			$settingsForm.on('click', '.wpp-remove-rule', function() {
				const index = $(this).data('rule-index');
				const fieldConfig = self.getFieldConfig(fieldId);
				if (fieldConfig.conditional_logic && fieldConfig.conditional_logic[index]) {
					fieldConfig.conditional_logic.splice(index, 1);
					self.updateFieldConfig(fieldId, fieldConfig);
					self.renderSettingsPanel(fieldId);
				}
			});

			// Изменение правил условной логики (используем input для мгновенной реакции)
			$settingsForm.on('input change', '.rule-field, .rule-operator, .rule-value', function() {
				const index = $(this).data('rule-index');
				const fieldConfig = self.getFieldConfig(fieldId);
				
				if ($(this).hasClass('rule-field')) {
					fieldConfig.conditional_logic[index].field = $(this).val();
				} else if ($(this).hasClass('rule-operator')) {
					fieldConfig.conditional_logic[index].operator = $(this).val();
				} else if ($(this).hasClass('rule-value')) {
					fieldConfig.conditional_logic[index].value = $(this).val();
				}
				
				self.updateFieldConfig(fieldId, fieldConfig);
			});

			// Сохранение настроек
			$settingsForm.find('.save-field-settings').on('click', function() {
				self.updateFieldPreview(fieldId);
				self.showNotification('Настройки сохранены', 'success');
			});
		},

		/**
		 * Обновление настройки поля
		 * 
		 * @param {string} fieldId ID поля
		 * @param {string} setting Название настройки
		 * @param {mixed} value Значение
		 */
		updateFieldSetting: function(fieldId, setting, value) {
			const fieldConfig = this.getFieldConfig(fieldId);
			if (fieldConfig) {
				fieldConfig[setting] = value;
				this.updateFieldConfig(fieldId, fieldConfig);
			}
		},

		/**
		 * Обновление конфигурации поля
		 * 
		 * @param {string} fieldId ID поля
		 * @param {object} fieldConfig Новая конфигурация
		 */
		updateFieldConfig: function(fieldId, fieldConfig) {
			const index = this.formConfig.findIndex(f => f.id === fieldId);
			if (index !== -1) {
				this.formConfig[index] = fieldConfig;
				this.saveConfig();
			}
		},

		/**
		 * Обновление предпросмотра поля
		 * 
		 * @param {string} fieldId ID поля
		 */
		updateFieldPreview: function(fieldId) {
			const fieldConfig = this.getFieldConfig(fieldId);
			if (!fieldConfig) {
				return;
			}

			const $fieldItem = $(`.wpp-form-field-item[data-field-id="${fieldId}"]`);
			const previewHTML = this.getFieldPreviewHTML(fieldConfig);
			$fieldItem.find('.wpp-field-preview').html(previewHTML);
		},

		/**
		 * Получение конфигурации поля по ID
		 * 
		 * @param {string} fieldId ID поля
		 * @returns {object|null} Конфигурация поля
		 */
		getFieldConfig: function(fieldId) {
			const field = this.formConfig.find(f => f.id === fieldId);
			return field || null;
		},

		/**
		 * Получение информации о типе поля
		 * 
		 * @param {string} fieldType Тип поля
		 * @returns {object|null} Информация о поле
		 */
		getFieldInfo: function(fieldType) {
			const field = wppFormBuilderData.availableFields.find(f => f.type === fieldType);
			return field || null;
		},

		/**
		 * Получение HTML опций для выбора полей в условной логике
		 * 
		 * @param {string} currentFieldId ID текущего поля (для исключения)
		 * @returns {string} HTML опций
		 */
		getFieldsOptions: function(currentFieldId) {
			return this.formConfig
				.filter(f => f.id !== currentFieldId && !['accordion', 'fields_block', 'super_accordion', 'repeater'].includes(f.type))
				.map(f => `<option value="${f.name}" ${f.name === currentFieldId ? 'selected' : ''}>${f.label || f.name}</option>`)
				.join('');
		},

		/**
		 * Удаление поля
		 * 
		 * @param {string} fieldId ID поля
		 */
		removeField: function(fieldId) {
			const index = this.formConfig.findIndex(f => f.id === fieldId);
			if (index !== -1) {
				this.formConfig.splice(index, 1);
				this.saveConfig();
				
				$(`.wpp-form-field-item[data-field-id="${fieldId}"]`).remove();
				
				if (this.selectedFieldId === fieldId) {
					this.selectedFieldId = null;
					$('#wpp-settings-panel').html(`
						<div class="wpp-no-selection">
							<p>${wppFormBuilderData.i18n.fieldSettings}</p>
						</div>
					`);
				}

				// Показываем placeholder если полей не осталось
				if (this.formConfig.length === 0) {
					$('#wpp-form-canvas .wpp-canvas-placeholder').show();
				}
			}
		},

		/**
		 * Обновление порядка полей после drag-and-drop
		 */
		updateFieldOrder: function() {
			const newOrder = [];
			$('#wpp-form-canvas .wpp-form-field-item').each(function() {
				const fieldId = $(this).data('field-id');
				const fieldConfig = this.formConfig.find(f => f.id === fieldId);
				if (fieldConfig) {
					newOrder.push(fieldConfig);
				}
			}.bind(this));

			this.formConfig = newOrder;
			this.saveConfig();
		},

		/**
		 * Отрисовка формы из сохранённой конфигурации
		 */
		renderFormFromConfig: function() {
			const $canvas = $('#wpp-form-canvas');
			$canvas.empty();

			if (this.formConfig.length === 0) {
				$canvas.html(`
					<div class="wpp-canvas-placeholder">
						<span class="dashicons dashicons-admin-page"></span>
						<p>${wppFormBuilderData.i18n.dragToAdd}</p>
					</div>
				`);
				return;
			}

			this.formConfig.forEach(fieldConfig => {
				this.renderField(fieldConfig);
			});
		},

		/**
		 * Очистка формы
		 */
		clearForm: function() {
			this.formConfig = [];
			this.saveConfig();
			this.renderFormFromConfig();
			this.selectedFieldId = null;
			$('#wpp-settings-panel').html(`
				<div class="wpp-no-selection">
					<p>${wppFormBuilderData.i18n.fieldSettings}</p>
				</div>
			`);
		},

		/**
		 * Сохранение конфигурации в localStorage
		 */
		saveConfig: function() {
			localStorage.setItem('wpp_form_builder_config', JSON.stringify(this.formConfig));
		},

		/**
		 * Копирование конфигурации в буфер обмена
		 */
		copyConfig: function() {
			if (this.formConfig.length === 0) {
				this.showNotification(wppFormBuilderData.i18n.emptyConfig, 'error');
				return;
			}

			// Форматируем конфиг для вывода
			const configOutput = '<?php\nreturn ' + JSON.stringify(this.formConfig, null, 4) + ';';
			
			// Показываем в модальном окне
			$('#wpp-config-output').val(configOutput);
			$('#wpp-config-modal').show();
		},

		/**
		 * Копирование текста в буфер обмена
		 * 
		 * @param {string} text Текст для копирования
		 */
		copyToClipboard: function(text) {
			const $tempInput = $('<textarea>');
			$('body').append($tempInput);
			$tempInput.val(text).select();
			
			try {
				document.execCommand('copy');
				this.showNotification(wppFormBuilderData.i18n.configCopied, 'success');
			} catch (err) {
				this.showNotification('Не удалось скопировать', 'error');
			}
			
			$tempInput.remove();
		},

		/**
		 * Показ модального окна выбора поля
		 */
		showFieldSelector: function() {
			// В будущей версии можно сделать красивое модальное окно с выбором типа поля
			// Сейчас просто добавляем первое поле для демонстрации
			this.addField('text');
		},

		/**
		 * Прокрутка к панели настроек
		 */
		scrollToSettings: function() {
			$('html, body').animate({
				scrollTop: $('.wpp-form-builder-settings').offset().top - 100
			}, 300);
		},

		/**
		 * Показ уведомления
		 * 
		 * @param {string} message Сообщение
		 * @param {string} type Тип уведомления (success, error, info)
		 */
		showNotification: function(message, type) {
			const $notification = $(`
				<div class="wpp-notification ${type || 'info'}">
					${message}
				</div>
			`);

			$('body').append($notification);

			setTimeout(() => {
				$notification.fadeOut(300, function() {
					$(this).remove();
				});
			}, 3000);
		}
	};

	/**
	 * Инициализация после загрузки DOM
	 */
	$(document).ready(function() {
		WPPFormBuilder.init();
	});

})(jQuery);
