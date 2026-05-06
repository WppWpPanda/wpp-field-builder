<?php
/**
 * WPP_Field_Builder - class-wpp-field-loader.php
 *
 * Этот файл отвечает за автоматическую загрузку классов полей из папки `/fields`.
 * Каждая директория в `/fields` представляет собой тип поля (например, `text`, `select`),
 * где должен быть файл `[Field_Type]_Field.php`.
 *
 * @package WPP_Field_Builder
 * @subpackage Загрузчик
 * @since 1.0.0
 * @author Your Name <your@email.com>
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Защита от прямого доступа
}

/**
 * Класс WPP_Field_Loader
 *
 * Отвечает за автоматическое подключение классов полей,
 * находящихся в папке `/fields`. Каждое поле должно иметь:
 * - Папку с названием типа поля (например, `/text`)
 * - Файл класса вида `WPP_[Type]_Field.php`
 *
 * Пример: `/fields/text/WPP_Text_Field.php`
 */
class WPP_Field_Loader {

	/**
	 * Экземпляр singleton
	 *
	 * @var WPP_Field_Loader|null
	 */
	private static $instance = null;

	/**
	 * Получить экземпляр singleton
	 *
	 * @since 1.0.0
	 * @return WPP_Field_Loader
	 */
	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Приватный конструктор для предотвращения прямой инстанциации
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->load_fields();
	}

	/**
	 * Загрузить все классы полей из директории /fields
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function load_fields() {
		$fields_dir = WPP_FIELD_BUILDER_PATH . 'fields/';

		if ( ! is_dir( $fields_dir ) ) {
			error_log( '❌ WPP_Field_Builder: Папка fields/ не найдена' );
			return;
		}

		$field_dirs = array_filter( glob( $fields_dir . '*' ), 'is_dir' );

		if ( empty( $field_dirs ) ) {
			error_log( '❌ WPP_Field_Builder: Нет папок в fields/' );
			return;
		}

		foreach ( $field_dirs as $dir ) {
			$folder_name = basename( $dir );
			$class_name = 'WPP_' . $this->normalize_class_name( $folder_name ) . '_Field';
			$file_path = $dir . '/' . $class_name . '.php';

			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				//error_log( "✅ Загружено поле: {$class_name}" );
			} else {
				error_log( "❌ Файл не найден: {$file_path}" );
			}
		}
	}

	/**
	 * Нормализовать имя папки в часть имени класса
	 *
	 * Преобразует имена папок вида 'button_group' в 'Button_Group'
	 *
	 * @since 1.0.0
	 * @param string $name Имя папки
	 * @return string Нормализованная часть имени класса
	 */
	private function normalize_class_name( string $name ): string {
		return str_replace( '_', '', ucwords( str_replace( [ '-', '_' ], '_', $name ), '_' ) );
	}
}