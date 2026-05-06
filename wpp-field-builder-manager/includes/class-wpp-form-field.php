<?php
/**
 * WPP_Field_Builder - class-wpp-form-field.php
 *
 * Абстрактный родительский класс для всех полей формы.
 * Обеспечивает единое API для всех типов полей (text, select, checkbox и т.д.)
 *
 * @package WPP_Field_Builder
 * @subpackage Fields
 * @since 1.0.0
 * @author Your Name <your@email.com>
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Абстрактный класс WPP_Form_Field
 *
 * Является базовым классом для всех кастомных полей плагина.
 * Реализует общую логику:
 * - Установка параметров поля
 * - Рендеринг обёртки, метки, описания
 * - Валидация
 * - Условная логика отображения
 */
abstract class WPP_Form_Field {

	/**
	 * Массив аргументов и настроек поля
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * Конструктор
	 *
	 * Принимает массив параметров и устанавливает значения по умолчанию.
	 *
	 * @param array $args Пользовательские параметры поля
	 *
	 * @since 1.0.0
	 */
	public function __construct( $args = [] ) {
		$defaults = [
			'name'        => '',           // Имя поля (name="...")
			'label'       => '',           // Подпись поля
			'description' => '',           // Описание под полем
			'default'     => '',           // Значение по умолчанию
			'placeholder' => '',           // Placeholder
			'classes'     => [],           // Дополнительные CSS-классы
			'width'       => 'full',       // Ширина: full, 1/2, 1/3 и т.д.
			'required'    => false,        // Обязательное ли поле
			'conditional' => [],           // Условие отображения ['field_name' => ['value']]
			'validation'  => null,         // Callback для валидации
		];

        $defaults['default'] = apply_filters( 'wpp_form_field_default', $defaults['default'], $args );

        $defaults['default'] = apply_filters( 'wpp_form_field_default_' . $args['name'], $defaults['default'], $args );


		$this->args = wp_parse_args( $args, $defaults );
	}

	/**
	 * Рендерит HTML-код поля.
	 *
	 * Если метод не переопределён в дочернем классе — выводит предупреждение.
	 * Это позволяет избежать фатальных ошибок и помогает при разработке.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function render() {
		$called_class = get_called_class();
		$parent_class = __CLASS__;

		trigger_error(
			"Метод render() не реализован в классе {$called_class}. Используется базовая реализация из {$parent_class}.",
			E_USER_WARNING
		);

		echo '<div class="wpp-field-error alert alert-danger">';
		echo '<strong>Ошибка:</strong> Метод render() должен быть реализован в дочернем классе.';
		echo '</div>';
	}

	// ┌────────────────────────────────────────────┐
	// │           Base Rendering Methods           │
	// └────────────────────────────────────────────┘

	/**
	 * Рендерит начальную обёртку поля (div)
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function render_wrapper_start() {
		$name    = esc_attr( $this->args['name'] );
		$classes = array_map( 'esc_attr', $this->args['classes'] );
		$width   = strtolower( trim( $this->args['width'] ) );

		// Ширина через Bootstrap
		$width_class = $this->get_width_class( $width );

		$classes[] = $width_class;

		// Условная логика
		$condition_data = '';
		if ( ! empty( $this->args['conditional'] ) && is_array( $this->args['conditional'] ) ) {
			$condition_data = htmlspecialchars( wp_json_encode( $this->args['conditional'] ), ENT_QUOTES, 'UTF-8' );
		}

		$compare = ! empty( $this->args['compare'] ) ? esc_attr( $this->args['compare'] ) : '=';

		?>
        <div class="wpp-field wpp-<?php echo sanitize_key( $this->get_field_type() ); ?> <?php echo esc_attr( implode( ' ', $classes ) ); ?>"
             data-name="<?php echo $name; ?>"
			<?php if ( ! $this->should_render() ) : ?> style="display:none"<?php endif; ?>
			<?php if ( $condition_data ) : ?>data-compare="<?php echo $compare; ?>" data-condition='<?php echo $condition_data; ?>'<?php endif; ?>>
		<?php
	}

	/**
	 * Get width class for Bootstrap grid
	 *
	 * @since 1.0.0
	 * @param string $width Width value
	 * @return string Bootstrap column class
	 */
	protected function get_width_class( $width ) {
		$width_map = [
			'1/12' => 'col-md-1',
			'1'    => 'col-md-1',
			'2'    => 'col-md-2',
			'3'    => 'col-md-3',
			'4'    => 'col-md-4',
			'5'    => 'col-md-5',
			'6'    => 'col-md-6',
			'7'    => 'col-md-7',
			'8'    => 'col-md-8',
			'9'    => 'col-md-9',
			'10'   => 'col-md-10',
			'11'   => 'col-md-11',
			'1/6'  => 'col-md-2',
			'2/12' => 'col-md-2',
			'1/2'  => 'col-md-6',
			'1/3'  => 'col-md-4',
			'2/3'  => 'col-md-8',
			'1/4'  => 'col-md-3',
		];

		return $width_map[ $width ] ?? 'col-12';
	}

	/**
	 * Get field type from class name
	 *
	 * @since 1.0.0
	 * @return string Field type
	 */
	protected function get_field_type() {
		return strtolower( preg_replace( '/^WPP_|_Field$/', '', get_class( $this ) ) );
	}

	/**
	 * Рендерит закрывающий тег обёртки поля
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function render_wrapper_end() {
		?>
        </div>
		<?php
	}

	/**
	 * Рендерит подпись (label)
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function render_label() {
		if ( ! empty( $this->args['label'] ) ) {
			$id = sanitize_key( $this->args['name'] );
			echo '<label for="' . $id . '">' . esc_html( $this->args['label'] ) . '</label>';
		}
	}

	/**
	 * Рендерит описание (description)
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function render_description() {
		if ( ! empty( $this->args['description'] ) ) {
			echo '<small class="description">' . esc_html( $this->args['description'] ) . '</small>';
		}
	}

	// ┌────────────────────────────────────────────┐
	// │             Validation & Logic             │
	// └────────────────────────────────────────────┘

	/**
	 * Возвращает значение поля
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_value() {
		return $this->args['default'];
	}

	/**
	 * Возвращает имя поля
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name() {
		return $this->args['name'];
	}

	/**
	 * Возвращает правила условного отображения
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_conditional_rules() {
		return $this->args['conditional'];
	}

	/**
	 * Проверяет валидность значения поля
	 *
	 * Если предоставлен callback, использует его. Иначе применяет стандартную санитизацию.
	 *
	 * @since 1.0.0
	 * @param mixed $value Входное значение
	 * @return mixed Проверенное значение
	 */
	public function validate( $value ) {
		if ( $this->args['validation'] && is_callable( $this->args['validation'] ) ) {
			return call_user_func( $this->args['validation'], $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Определяет, должно ли поле отображаться на основе условной логики
	 *
	 * @since 1.0.0
	 * @return bool True если поле должно отображаться
	 */
	public function should_render() {
		if ( empty( $this->args['conditional'] ) ) {
			return true;
		}

		$conditions = $this->args['conditional'];

		foreach ( $conditions as $condition_field => $condition_values ) {
			$expected_value = $this->get_conditional_value( $condition_field );

			if ( is_array( $condition_values ) ) {
				if ( ! in_array( $expected_value, $condition_values, true ) ) {
					return false;
				}
			} else {
				if ( $expected_value !== $condition_values ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Получить значение для проверки условной логики
	 *
	 * Переопределите этот метод в дочерних классах или используйте фильтры для кастомизации.
	 *
	 * @since 1.0.0
	 * @param string $field_name Имя поля для получения значения
	 * @return mixed Значение поля
	 */
	protected function get_conditional_value( $field_name ) {
		return apply_filters( 'wpp_form_field_conditional_value', '', $field_name, $this );
	}
}