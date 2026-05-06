<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPP_Datepicker_Field' ) && class_exists( 'WPP_Form_Field' ) ) :

	/**
	 * Class WPP_Datepicker_Field
	 *
	 * Представляет поле ввода даты с возможностью указать minDate и maxDate.
	 */
	class WPP_Datepicker_Field extends WPP_Form_Field {

		public function __construct( $args = [] ) {
			// Установка значений по умолчанию
			$default_args = [
				'min_date' => '', // Например: '2024-01-01', '-1m', '+1w' и т.д.
				'max_date' => '', // Например: '2025-12-31', '+2y'
			];

			$this->args = wp_parse_args( $args, $default_args );

			parent::__construct( $this->args );

			// Подключаем стили и скрипты
			add_action( 'wp_footer', [ $this, 'enqueue_assets' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		}

		public function enqueue_assets() {
			wp_enqueue_script( 'jquery-ui-datepicker', false, [ 'jquery', 'jquery-ui-core' ] );

			wp_enqueue_style(
				'jquery-ui-css',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css'
			);

			// Парсим min_date и max_date
			$min_date = ! empty( $this->args['min_date'] ) ? $this->parse_date_string( $this->args['min_date'] ) : null;
			$max_date = ! empty( $this->args['max_date'] ) ? $this->parse_date_string( $this->args['max_date'] ) : null;

			// Преобразуем в JS-совместимые значения
			$min_date_js = $min_date === null ? 'null' : '"' . esc_js( $min_date ) . '"';
			$max_date_js = $max_date === null ? 'null' : '"' . esc_js( $max_date ) . '"';

			// Инициализация datepicker
			$script = "
        jQuery(document).ready(function($) {
            $('input[data-type=\"date\"]').each(function() {
                $(this).datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-80:+10',
                    minDate: $min_date_js,
                    maxDate: $max_date_js
                });
            });
        });
    ";

			wp_add_inline_script( 'jquery-ui-datepicker', $script );

			wp_enqueue_script(
				'wpp-datepicker',
				WPP_FIELD_BUILDER_URL . 'fields/datepicker/script.js',
				[ 'jquery' ],
				file_exists( WPP_FIELD_BUILDER_PATH . 'fields/datepicker/script.js' )
					? filemtime( WPP_FIELD_BUILDER_PATH . 'fields/datepicker/script.js' )
					: time(),
				true
			);
		}

		/**
		 * Преобразует человеко-читаемую строку в формат jQuery UI Datepicker
		 * Примеры:
		 *  'date + 1 d'     → '+1d'
		 *  'date - 2 weeks' → '-2w'
		 *  '2025-12-31'     → '2025-12-31'
		 *
		 * @param string $value
		 * @return string|null
		 */
		private function parse_date_string( $value ) {
			$value = trim( $value );

			// Если это конкретная дата в формате YYYY-MM-DD — оставляем как есть
			if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
				return $value;
			}

			// Приводим к нижнему регистру для обработки
			$lower = strtolower( $value );

			// Проверяем, содержит ли строка "date"
			if ( strpos( $lower, 'date' ) === false ) {
				// Не начинается с "date" — возможно, уже в формате +1d?
				if ( preg_match( '/^([+\-]?\d+)([dwmy])$/', $lower, $matches ) ) {
					return $matches[1] . $matches[2]; // например, +1d
				}
				return null; // неизвестный формат
			}

			// Удаляем "date" и очищаем
			$rest = trim( str_replace( 'date', '', $lower ) );

			// Ищем число и единицу измерения
			if ( preg_match( '/([+\-]?\d+).*?(\w+)/', $rest, $matches ) ) {
				$number = $matches[1]; // может быть +1, -2 и т.д.
				$unit   = trim( $matches[2] );

				switch ( $unit ) {
					case 'd':
					case 'day':
					case 'days':
						return $number . 'd';
					case 'w':
					case 'week':
					case 'weeks':
						return $number . 'w';
					case 'm':
					case 'month':
					case 'months':
						return $number . 'm';
					case 'y':
					case 'year':
					case 'years':
						return $number . 'y';
					default:
						return null;
				}
			}

			return null; // не удалось распознать
		}

		/**
		 * Рендерит HTML-код поля ввода даты
		 */
		public function render() {
			$this->render_wrapper_start();

			$id          = sanitize_key( $this->args['name'] );
			$name        = esc_attr( $this->args['name'] );
			$value       = esc_attr( $this->get_value() );
			$placeholder = esc_attr( $this->args['placeholder'] ?: 'YYYY-MM-DD' );
			$required    = $this->args['required'] ? 'required' : '';

			?>
            <label for="<?php echo $id; ?>">
				<?php echo esc_html( $this->args['label'] ); ?>
				<?php if ( $this->args['required'] ) : ?>
                    <span class="text-danger">*</span>
				<?php endif; ?>
            </label>
            <input type="text"
                   id="<?php echo $id; ?>"
                   name="<?php echo $name; ?>"
                   value="<?php echo $value; ?>"
                   placeholder="<?php echo $placeholder; ?>"
                   data-type="date"
                   class="form-control <?php echo esc_attr( implode( ' ', $this->args['classes'] ) ); ?>"
				<?php echo $required; ?>>

			<?php if ( ! empty( $this->args['description'] ) ) : ?>
                <small class="form-text text-muted"><?php echo esc_html( $this->args['description'] ); ?></small>
			<?php endif; ?>

			<?php
			$this->render_wrapper_end();
		}
	}

endif;