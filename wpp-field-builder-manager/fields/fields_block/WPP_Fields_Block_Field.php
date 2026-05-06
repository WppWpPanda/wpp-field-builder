<?php
/**
 * Class WPP_Fields_Block_Field
 *
 * Represents a block of fields with a common label.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WPP_Fields_Block_Field') && class_exists('WPP_Form_Field')) :

	/**
	 * Класс WPP_Fields_Block_Field
	 *
	 * Представляет блок полей с общей меткой.
	 * Позволяет группировать несколько полей в один контейнер.
	 *
	 * @package WPP_Field_Builder
	 * @subpackage Fields
	 * @since 1.0.0
	 */
	class WPP_Fields_Block_Field extends WPP_Form_Field {

		/**
		 * Массив внутренних полей блока
		 *
		 * @var array
		 */
		protected $fields = [];

		/**
		 * Конструктор
		 *
		 * @since 1.0.0
		 * @param array $args Параметры поля
		 */
		public function __construct( $args = [] ) {
			parent::__construct( $args );

			// Инициализируем внутренние поля
			if ( ! empty( $this->args['fields'] ) && is_array( $this->args['fields'] ) ) {
				$this->fields = $this->args['fields'];
			}
		}

		/**
		 * Рендерит блок полей с несколькими полями внутри
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render() {
			$this->render_wrapper_start();

			// Рендерим основную метку блока
			if ( ! empty( $this->args['label'] ) ) {
				echo '<label class="wpp-fields-block-label">' . esc_html( $this->args['label'] ) . '</label>';
			}

			echo '<div class="wpp-fields-block row">';

			// Рендерим отдельные поля
			$this->render_inner_fields();

			echo '</div>';

			$this->render_wrapper_end();
		}

		/**
		 * Рендерит внутренние поля блока
		 *
		 * @since 1.0.0
		 * @return void
		 */
		protected function render_inner_fields() {
			if ( empty( $this->fields ) || ! is_array( $this->fields ) ) {
				return;
			}

			foreach ( $this->fields as $field_name => $field_config ) {
				$field_object = $this->create_field_object( $field_name, $field_config );
				
				if ( $field_object instanceof WPP_Form_Field ) {
					$field_object->render();
				}
			}
		}

		/**
		 * Создаёт объект поля для внутренней конфигурации
		 *
		 * @since 1.0.0
		 * @param string $field_name Имя поля
		 * @param array  $field_config Конфигурация поля
		 * @return WPP_Form_Field|null Объект поля или null при ошибке
		 */
		protected function create_field_object( $field_name, $field_config ) {
			if ( empty( $field_config['type'] ) ) {
				return null;
			}

			$class_name = 'WPP_' . ucfirst( $field_config['type'] ) . '_Field';

			if ( ! class_exists( $class_name ) ) {
				error_log( "WPP_Fields_Block_Field: Класс {$class_name} не найден для поля '{$field_name}'" );
				return null;
			}

			$merged_args = array_merge( $field_config, [ 'name' => $field_name ] );
			return new $class_name( $merged_args );
		}
	}

endif;