<?php
/**
 * Плагин: WPP Field Builder Manager
 * Описание: Универсальный менеджер форм для WordPress. Поддерживает кастомные поля, условную логику, валидацию и адаптивный дизайн на Bootstrap.
 * Версия: 1.0.0
 * Автор: Your Name
 * Текстовое домен: wpp-field-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Защита от прямого доступа
}

// Константы плагина
define( 'WPP_FIELD_BUILDER_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPP_FIELD_BUILDER_URL', plugin_dir_url( __FILE__ ) );
define( 'WPP_FIELD_BUILDER_VERSION', '1.0.0' );

/**
 * Загрузка необходимых классов плагина
 */
require_once WPP_FIELD_BUILDER_PATH . 'includes/class-wpp-form-field.php';
require_once WPP_FIELD_BUILDER_PATH . 'includes/class-wpp-field-loader.php';
require_once WPP_FIELD_BUILDER_PATH . 'includes/class-wpp-assets.php';

/**
 * Инициализация компонентов плагина
 */
add_action( 'plugins_loaded', 'wpp_field_builder_init' );

/**
 * Инициализация WPP Field Builder
 *
 * @since 1.0.0
 * @return void
 */
function wpp_field_builder_init() {
	// Инициализация ресурсов (Bootstrap, jQuery)
	WPP_Assets::init();
	
	// Загрузка всех полей
	WPP_Field_Loader::init();
	
	// Загрузка тестовой формы, если существует
	$test_form_path = WPP_FIELD_BUILDER_PATH . 'test/test-form.php';
	if ( file_exists( $test_form_path ) ) {
		require_once $test_form_path;
	}
}