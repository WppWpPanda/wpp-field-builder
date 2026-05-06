<?php
/**
 * WPP_Field_Builder - class-wpp-assets.php
 *
 * Этот файл отвечает за подключение всех необходимых ассетов (CSS и JS),
 * включая Bootstrap 5 и jQuery как в админке WordPress, так и на фронтенде.
 *
 * @package WPP_Field_Builder
 * @subpackage Assets
 * @since 1.0.0
 * @author Your Name <your@email.com>
 * @license GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Класс WPP_Assets
 *
 * Отвечает за подключение CSS и JS ресурсов плагина:
 * - Bootstrap 5 (опционально, через CDN или локально)
 * - Кастомные стили и скрипты
 * - jQuery (входит в ядро WordPress)
 *
 * Используется как в административной части WordPress, так и на фронтенде.
 *
 * @package WPP_Field_Builder
 * @subpackage Assets
 * @since 1.0.0
 */
class WPP_Assets {

	/**
	 * URL для загрузки Bootstrap (по умолчанию CDN)
	 *
	 * @var string
	 */
	private static $bootstrap_css_url = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';

	/**
	 * URL для загрузки JavaScript Bootstrap (по умолчанию CDN)
	 *
	 * @var string
	 */
	private static $bootstrap_js_url = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';

	/**
	 * Версия Bootstrap
	 *
	 * @var string
	 */
	private static $bootstrap_version = '5.3.0';

	/**
	 * Флаг использования Bootstrap
	 *
	 * @var bool
	 */
	private static $use_bootstrap = true;

	/**
	 * Регистрирует хуки для подключения ассетов.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_frontend' ] );
	}

	/**
	 * Установить URL для Bootstrap CSS
	 *
	 * @since 1.0.0
	 * @param string $url URL файла CSS
	 * @return void
	 */
	public static function set_bootstrap_css_url( $url ) {
		self::$bootstrap_css_url = $url;
	}

	/**
	 * Установить URL для Bootstrap JS
	 *
	 * @since 1.0.0
	 * @param string $url URL файла JS
	 * @return void
	 */
	public static function set_bootstrap_js_url( $url ) {
		self::$bootstrap_js_url = $url;
	}

	/**
	 * Отключить использование Bootstrap
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function disable_bootstrap() {
		self::$use_bootstrap = false;
	}

	/**
	 * Подключает ассеты для административной панели WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook Текущий хук страницы админки.
	 * @return void
	 */
	public static function enqueue_admin( $hook ) {
		if ( self::$use_bootstrap ) {
			self::enqueue_bootstrap();
		}

		wp_enqueue_script(
			'wpp-admin-js',
			WPP_FIELD_BUILDER_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			WPP_FIELD_BUILDER_VERSION,
			true
		);

		wp_enqueue_style(
			'wpp-admin-css',
			WPP_FIELD_BUILDER_URL . 'assets/css/admin.css',
			[],
			WPP_FIELD_BUILDER_VERSION
		);
	}

	/**
	 * Подключает ассеты для фронтенда сайта.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function enqueue_frontend() {
		if ( self::$use_bootstrap ) {
			self::enqueue_bootstrap();
		}

		// JS
		wp_enqueue_script(
			'wpp-frontend-js',
			WPP_FIELD_BUILDER_URL . 'assets/js/frontend.js',
			[ 'jquery' ],
			WPP_FIELD_BUILDER_VERSION,
			true
		);

		// CSS
		wp_enqueue_style(
			'wpp-frontend-css',
			WPP_FIELD_BUILDER_URL . 'assets/css/frontend.css',
			[],
			WPP_FIELD_BUILDER_VERSION
		);
	}

	/**
	 * Подключает стили и скрипты Bootstrap 5.
	 *
	 * По умолчанию использует CDN, но можно переопределить через фильтры
	 * или методы set_bootstrap_css_url() / set_bootstrap_js_url().
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function enqueue_bootstrap() {
		// Применяем фильтры для возможности кастомизации URL
		$css_url = apply_filters( 'wpp_assets_bootstrap_css_url', self::$bootstrap_css_url );
		$js_url  = apply_filters( 'wpp_assets_bootstrap_js_url', self::$bootstrap_js_url );
		$version = apply_filters( 'wpp_assets_bootstrap_version', self::$bootstrap_version );

		// Подключаем стили Bootstrap 5
		if ( $css_url ) {
			wp_enqueue_style(
				'bootstrap',
				$css_url,
				[],
				$version
			);
		}

		// Подключаем JavaScript Bootstrap 5 (с Popper)
		if ( $js_url ) {
			wp_enqueue_script(
				'bootstrap-js',
				$js_url,
				[],
				$version,
				true
			);
		}
	}
}