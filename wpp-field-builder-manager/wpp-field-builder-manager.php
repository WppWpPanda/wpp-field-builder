<?php
/**
 * Plugin Name: WPP Field Builder Manager
 * Description: Универсальный менеджер форм для WordPress. Поддерживает кастомные поля, условную логику, валидацию и адаптивный дизайн на Bootstrap.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: wpp-field-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Защита от прямого доступа
}

// Константы плагина
define( 'WPP_FIELD_BUILDER_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPP_FIELD_BUILDER_URL', plugin_dir_url( __FILE__ ) );
define( 'WPP_FIELD_BUILDER_VERSION', '1.0.0' );
define( 'WPP_FIELD_BUILDER_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Загрузка необходимых классов плагина
 */
require_once WPP_FIELD_BUILDER_PATH . 'includes/class-wpp-form-field.php';
require_once WPP_FIELD_BUILDER_PATH . 'includes/class-wpp-field-loader.php';
require_once WPP_FIELD_BUILDER_PATH . 'includes/class-wpp-assets.php';

/**
 * Загрузка административной части (только для админки)
 */
if ( is_admin() ) {
	require_once WPP_FIELD_BUILDER_PATH . 'admin/class-wpp-form-builder-admin.php';
}

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
	
	// Инициализация административной панели (если класс существует)
	if ( is_admin() && class_exists( 'WPP_Form_Builder_Admin' ) ) {
		new WPP_Form_Builder_Admin();
	}
	
	// Добавление ссылки на настройки в список плагинов
	add_filter( 'plugin_action_links_' . WPP_FIELD_BUILDER_BASENAME, 'wpp_field_builder_add_settings_link' );
	
	// Загрузка тестовой формы, если существует
	$test_form_path = WPP_FIELD_BUILDER_PATH . 'test/test-form.php';
	if ( file_exists( $test_form_path ) ) {
		require_once $test_form_path;
	}
}

/**
 * Добавляет ссылку на настройки в список плагинов.
 *
 * @param array $links Существующие ссылки плагина.
 * @return array Обновлённый массив ссылок.
 */
function wpp_field_builder_add_settings_link( $links ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=wpp-form-builder' ) . '">' . __( 'Конструктор форм', 'wpp-field-builder' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}