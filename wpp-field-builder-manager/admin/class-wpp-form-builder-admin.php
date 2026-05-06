<?php
/**
 * Класс административной панели визуального конструктора форм
 *
 * @package WPP_Field_Builder_Manager
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс WPP_Form_Builder_Admin
 * 
 * Управляет страницей административной панели для визуального создания форм
 * с возможностью копирования конфигурации в буфер обмена
 */
class WPP_Form_Builder_Admin {

	/**
	 * Slug страницы меню
	 *
	 * @var string
	 */
	private $page_slug = 'wpp-form-builder';

	/**
	 * Доступные типы полей
	 *
	 * @var array
	 */
	private $available_fields = array();

	/**
	 * Конструктор класса
	 */
	public function __construct() {
		$this->init_available_fields();
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_wpp_export_form_config', array( $this, 'ajax_export_form_config' ) );
		
		// Добавление ссылки на настройки в списке плагинов
		add_filter( 'plugin_action_links_' . WPP_FIELD_BUILDER_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Добавление ссылки на настройки в список плагинов
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Существующие ссылки.
	 * @return array Обновленные ссылки.
	 */
	public function add_plugin_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=' . $this->page_slug ) . '">' . __( 'Конструктор форм', 'wpp-field-builder-manager' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Инициализация списка доступных типов полей
	 *
	 * @since 1.0.0
	 */
	private function init_available_fields() {
		$this->available_fields = array(
			array(
				'type'  => 'text',
				'label' => 'Текстовое поле',
				'icon'  => 'dashicons-editor-textcolor',
			),
			array(
				'type'  => 'email',
				'label' => 'Email',
				'icon'  => 'dashicons-email',
			),
			array(
				'type'  => 'tel',
				'label' => 'Телефон',
				'icon'  => 'dashicons-phone',
			),
			array(
				'type'  => 'number',
				'label' => 'Число',
				'icon'  => 'dashicons-list-view',
			),
			array(
				'type'  => 'select',
				'label' => 'Выпадающий список',
				'icon'  => 'dashicons-arrow-down-alt2',
			),
			array(
				'type'  => 'multiselect',
				'label' => 'Множественный выбор',
				'icon'  => 'dashicons-editor-ul',
			),
			array(
				'type'  => 'checkbox',
				'label' => 'Чекбокс',
				'icon'  => 'dashicons-yes-alt',
			),
			array(
				'type'  => 'radio',
				'label' => 'Радиокнопки',
				'icon'  => 'dashicons-radio',
			),
			array(
				'type'  => 'textarea',
				'label' => 'Текстовая область',
				'icon'  => 'dashicons-editor-paragraph',
			),
			array(
				'type'  => 'date',
				'label' => 'Дата',
				'icon'  => 'dashicons-calendar-alt',
			),
			array(
				'type'  => 'file',
				'label' => 'Загрузка файла',
				'icon'  => 'dashicons-upload',
			),
			array(
				'type'  => 'accordion',
				'label' => 'Аккордеон',
				'icon'  => 'dashicons-menu',
			),
			array(
				'type'  => 'repeater',
				'label' => 'Повторяющиеся поля',
				'icon'  => 'dashicons-controls-repeat',
			),
			array(
				'type'  => 'fields_block',
				'label' => 'Блок полей',
				'icon'  => 'dashicons-screenoptions',
			),
			array(
				'type'  => 'super_accordion',
				'label' => 'Супер аккордеон',
				'icon'  => 'dashicons-editor-expand',
			),
		);

		/**
		 * Фильтр для добавления собственных типов полей
		 *
		 * @since 1.0.0
		 *
		 * @param array $available_fields Массив доступных типов полей.
		 */
		$this->available_fields = apply_filters( 'wpp_form_builder_available_fields', $this->available_fields );
	}

	/**
	 * Добавление страницы меню в админку
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Конструктор форм WPP', 'wpp-field-builder-manager' ),
			__( 'WPP Field Builder', 'wpp-field-builder-manager' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'render_admin_page' ),
			'dashicons-editor-table',
			30
		);
		
		add_submenu_page(
			$this->page_slug,
			__( 'Конструктор форм', 'wpp-field-builder-manager' ),
			__( 'Конструктор форм', 'wpp-field-builder-manager' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Подключение стилей и скриптов
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook Хук текущей страницы.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_' . $this->page_slug !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'wpp-form-builder-admin',
			WPP_FIELD_BUILDER_URL . 'admin/css/form-builder-admin.css',
			array(),
			WPP_FIELD_BUILDER_VERSION
		);

		// Подключаем jQuery UI CSS для drag-and-drop
		wp_enqueue_style( 'jquery-ui-sortable' );

		wp_enqueue_script(
			'wpp-form-builder-admin',
			WPP_FIELD_BUILDER_URL . 'admin/js/form-builder-admin.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable' ),
			WPP_FIELD_BUILDER_VERSION,
			true
		);

		wp_localize_script(
			'wpp-form-builder-admin',
			'wppFormBuilderData',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'wpp_form_builder_nonce' ),
				'availableFields' => $this->available_fields,
				'i18n'            => array(
					'dragToAdd'          => __( 'Перетащите поле в форму', 'wpp-field-builder-manager' ),
					'addField'           => __( 'Добавить поле', 'wpp-field-builder-manager' ),
					'removeField'        => __( 'Удалить поле', 'wpp-field-builder-manager' ),
					'fieldSettings'      => __( 'Настройки поля', 'wpp-field-builder-manager' ),
					'saveConfig'         => __( 'Сохранить конфигурацию', 'wpp-field-builder-manager' ),
					'copyConfig'         => __( 'Копировать конфигурацию', 'wpp-field-builder-manager' ),
					'configCopied'       => __( 'Конфигурация скопирована в буфер обмена!', 'wpp-field-builder-manager' ),
					'confirmDelete'      => __( 'Вы уверены, что хотите удалить это поле?', 'wpp-field-builder-manager' ),
					'fieldName'          => __( 'Название поля', 'wpp-field-builder-manager' ),
					'fieldLabel'         => __( 'Метка', 'wpp-field-builder-manager' ),
					'fieldPlaceholder'   => __( 'Подсказка', 'wpp-field-builder-manager' ),
					'fieldRequired'      => __( 'Обязательное', 'wpp-field-builder-manager' ),
					'fieldWidth'         => __( 'Ширина', 'wpp-field-builder-manager' ),
					'conditionalLogic'   => __( 'Условная логика', 'wpp-field-builder-manager' ),
					'options'            => __( 'Опции', 'wpp-field-builder-manager' ),
					'addOption'          => __( 'Добавить опцию', 'wpp-field-builder-manager' ),
					'showIf'             => __( 'Показывать если', 'wpp-field-builder-manager' ),
					'equals'             => __( 'равно', 'wpp-field-builder-manager' ),
					'notEquals'          => __( 'не равно', 'wpp-field-builder-manager' ),
					'contains'           => __( 'содержит', 'wpp-field-builder-manager' ),
					'emptyConfig'        => __( 'Конфигурация пуста', 'wpp-field-builder-manager' ),
					'exportSuccess'      => __( 'Конфигурация экспортирована успешно', 'wpp-field-builder-manager' ),
					'exportError'        => __( 'Ошибка при экспорте конфигурации', 'wpp-field-builder-manager' ),
				),
			)
		);
	}

	/**
	 * Отрисовка страницы административной панели
	 *
	 * @since 1.0.0
	 */
	public function render_admin_page() {
		?>
		<div class="wrap wpp-form-builder-wrap">
			<h1><?php echo esc_html__( 'Конструктор форм WPP', 'wpp-field-builder-manager' ); ?></h1>
			
			<div class="wpp-form-builder-container">
				<!-- Панель инструментов -->
				<div class="wpp-form-builder-toolbar">
					<button type="button" class="button button-primary" id="wpp-add-new-field">
						<span class="dashicons dashicons-plus-alt"></span>
						<?php esc_html_e( 'Добавить поле', 'wpp-field-builder-manager' ); ?>
					</button>
					<button type="button" class="button" id="wpp-copy-config">
						<span class="dashicons dashicons-clipboard"></span>
						<?php esc_html_e( 'Копировать конфигурацию', 'wpp-field-builder-manager' ); ?>
					</button>
					<button type="button" class="button" id="wpp-clear-form">
						<span class="dashicons dashicons-trash"></span>
						<?php esc_html_e( 'Очистить форму', 'wpp-field-builder-manager' ); ?>
					</button>
				</div>

				<!-- Основная рабочая область -->
				<div class="wpp-form-builder-workspace">
					<!-- Палитра полей -->
					<div class="wpp-form-builder-palette">
						<h3><?php esc_html_e( 'Доступные поля', 'wpp-field-builder-manager' ); ?></h3>
						<div class="wpp-palette-fields">
							<?php foreach ( $this->available_fields as $field ) : ?>
								<div class="wpp-palette-field" data-field-type="<?php echo esc_attr( $field['type'] ); ?>">
									<span class="dashicons <?php echo esc_attr( $field['icon'] ); ?>"></span>
									<span class="field-label"><?php echo esc_html( $field['label'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- Область формы -->
					<div class="wpp-form-builder-canvas">
						<h3><?php esc_html_e( 'Конструктор формы', 'wpp-field-builder-manager' ); ?></h3>
						<div class="wpp-form-canvas" id="wpp-form-canvas">
							<div class="wpp-canvas-placeholder">
								<span class="dashicons dashicons-admin-page"></span>
								<p><?php esc_html_e( 'Перетащите поля сюда или нажмите "Добавить поле"', 'wpp-field-builder-manager' ); ?></p>
							</div>
						</div>
					</div>

					<!-- Панель настроек -->
					<div class="wpp-form-builder-settings">
						<h3><?php esc_html_e( 'Настройки поля', 'wpp-field-builder-manager' ); ?></h3>
						<div class="wpp-settings-panel" id="wpp-settings-panel">
							<div class="wpp-no-selection">
								<p><?php esc_html_e( 'Выберите поле для редактирования настроек', 'wpp-field-builder-manager' ); ?></p>
							</div>
						</div>
					</div>
				</div>

				<!-- Модальное окно для просмотра конфигурации -->
				<div class="wpp-config-modal" id="wpp-config-modal" style="display: none;">
					<div class="wpp-modal-content">
						<div class="wpp-modal-header">
							<h2><?php esc_html_e( 'Конфигурация формы', 'wpp-field-builder-manager' ); ?></h2>
							<button type="button" class="wpp-modal-close" aria-label="<?php esc_attr_e( 'Закрыть', 'wpp-field-builder-manager' ); ?>">
								<span class="dashicons dashicons-no"></span>
							</button>
						</div>
						<div class="wpp-modal-body">
							<textarea id="wpp-config-output" readonly></textarea>
							<p class="description">
								<?php esc_html_e( 'Скопируйте этот код и используйте его в шорткоде или функции:', 'wpp-field-builder-manager' ); ?>
							</p>
							<code>wpp_form( $config );</code>
						</div>
						<div class="wpp-modal-footer">
							<button type="button" class="button button-primary" id="wpp-copy-from-modal">
								<span class="dashicons dashicons-clipboard"></span>
								<?php esc_html_e( 'Копировать', 'wpp-field-builder-manager' ); ?>
							</button>
							<button type="button" class="button" id="wpp-close-modal">
								<?php esc_html_e( 'Закрыть', 'wpp-field-builder-manager' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX обработчик для экспорта конфигурации формы
	 *
	 * @since 1.0.0
	 */
	public function ajax_export_form_config() {
		check_ajax_referer( 'wpp_form_builder_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Недостаточно прав', 'wpp-field-builder-manager' ) ) );
		}

		$config = isset( $_POST['config'] ) ? $_POST['config'] : array();

		if ( empty( $config ) ) {
			wp_send_json_error( array( 'message' => __( 'Конфигурация пуста', 'wpp-field-builder-manager' ) ) );
		}

		// Валидация и санитизация конфигурации
		$validated_config = $this->validate_config( $config );

		wp_send_json_success( array(
			'config'   => $validated_config,
			'message'  => __( 'Конфигурация экспортирована успешно', 'wpp-field-builder-manager' ),
		) );
	}

	/**
	 * Валидация конфигурации формы
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Конфигурация формы.
	 * @return array Валидированная конфигурация.
	 */
	private function validate_config( $config ) {
		$validated = array();

		foreach ( $config as $field ) {
			$validated_field = array();

			// Обязательные поля
			if ( ! empty( $field['type'] ) ) {
				$validated_field['type'] = sanitize_key( $field['type'] );
			}

			if ( ! empty( $field['name'] ) ) {
				$validated_field['name'] = sanitize_key( $field['name'] );
			}

			// Необязательные поля
			if ( isset( $field['label'] ) ) {
				$validated_field['label'] = sanitize_text_field( $field['label'] );
			}

			if ( isset( $field['placeholder'] ) ) {
				$validated_field['placeholder'] = sanitize_text_field( $field['placeholder'] );
			}

			if ( isset( $field['required'] ) ) {
				$validated_field['required'] = (bool) $field['required'];
			}

			if ( isset( $field['width'] ) ) {
				$validated_field['width'] = in_array( $field['width'], array( 'full', 'half', 'third', 'quarter' ), true )
					? $field['width']
					: 'full';
			}

			if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
				$validated_field['options'] = array_map( 'sanitize_text_field', $field['options'] );
			}

			if ( isset( $field['conditional_logic'] ) && is_array( $field['conditional_logic'] ) ) {
				$validated_field['conditional_logic'] = $this->validate_conditional_logic( $field['conditional_logic'] );
			}

			if ( ! empty( $validated_field ) ) {
				$validated[] = $validated_field;
			}
		}

		return $validated;
	}

	/**
	 * Валидация условной логики
	 *
	 * @since 1.0.0
	 *
	 * @param array $logic Массив условной логики.
	 * @return array Валидированная условная логика.
	 */
	private function validate_conditional_logic( $logic ) {
		$validated = array();

		foreach ( $logic as $rule ) {
			$validated_rule = array();

			if ( ! empty( $rule['field'] ) ) {
				$validated_rule['field'] = sanitize_key( $rule['field'] );
			}

			if ( ! empty( $rule['operator'] ) ) {
				$allowed_operators = array( 'equals', 'not_equals', 'contains' );
				$validated_rule['operator'] = in_array( $rule['operator'], $allowed_operators, true )
					? $rule['operator']
					: 'equals';
			}

			if ( isset( $rule['value'] ) ) {
				$validated_rule['value'] = sanitize_text_field( $rule['value'] );
			}

			if ( ! empty( $validated_rule ) ) {
				$validated[] = $validated_rule;
			}
		}

		return $validated;
	}
}
