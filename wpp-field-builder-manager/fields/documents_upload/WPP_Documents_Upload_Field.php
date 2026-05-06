<?php
/**
 * WPP_Field_Builder - WPP_Documents_Upload_Field.php
 *
 * Класс для поля загрузки документов, совместимый с первой системой
 *
 * @package WPP_Field_Builder
 * @subpackage Fields
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

class WPP_Documents_Upload_Field extends WPP_Form_Field {

	public function __construct($args = []) {
		parent::__construct($args);
		add_action('wp_footer', [$this, 'enqueue_assets']);
		add_action('admin_footer', [$this, 'enqueue_assets']);
	}

	public function enqueue_assets() {
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

		wp_enqueue_script(
			'wpp-documents-upload',
			WPP_FIELD_BUILDER_URL . 'fields/documents_upload/documents-upload.js',
			['jquery'],
			file_exists(WPP_FIELD_BUILDER_PATH . 'fields/documents_upload/documents-upload.js')
				? filemtime(WPP_FIELD_BUILDER_PATH . 'fields/documents_upload/documents-upload.js')
				: time(),
			true
		);

		wp_localize_script('wpp-documents-upload', 'wpp_docs_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wpp_docs_nonce')
		]);

		wp_enqueue_style(
			'wpp-documents-upload',
			WPP_FIELD_BUILDER_URL . 'fields/documents_upload/documents-upload.css',
			[],
			file_exists(WPP_FIELD_BUILDER_PATH . 'fields/documents_upload/documents-upload.css')
				? filemtime(WPP_FIELD_BUILDER_PATH . 'fields/documents_upload/documents-upload.css')
				: time(),
			'all'
		);
	}

	public function render() {
		$this->render_wrapper_start();

		echo '<div class="wpp-documents-upload-wrap">';
		$this->render_label();
		$this->render_description();
		echo '</div>';

		$name = esc_attr($this->args['name']);
		$document_key = $this->args['document_key'] ?? str_replace('rd_', '', $name);
		$loan_id = $this->get_loan_id();

		echo '<div class="wpp-documents-upload-field" 
                  data-loan-id="' . $loan_id . '" 
                  data-field-name="' . $name . '"
                  data-document-key="' . $document_key . '">';

		// Контейнер для загруженных файлов
		echo '<div class="uploaded-files-container" data-key="' . $document_key . '">';
		$this->render_uploaded_files($loan_id, $document_key);
		echo '</div>';

		// Кнопка загрузки
		echo '<div class="file-upload mt-2">';
		echo '<input type="file" class="document-file d-none" data-key="' . $document_key . '" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">';
		echo '<button type="button" class="btn btn-sm btn-outline-primary upload-btn">';
		echo '<i class="fas fa-cloud-upload-alt"></i> Upload File';
		echo '</button>';
		echo '</div>';

		echo '</div>';
		$this->render_wrapper_end();
	}

	private function get_loan_id() {
		global $loan_id;
		if (!empty($loan_id)) return $loan_id;

		return isset($_GET['loan']) ? intval($_GET['loan']) : 0;
	}

	private function render_uploaded_files($loan_id, $document_key) {
		if (!$loan_id) {
			echo '<span class="text-muted">Select a loan first</span>';
			return;
		}

		// Показываем прелоадер, файлы загрузятся через AJAX
		echo '<div class="text-center py-2">';
		echo '<div class="spinner-border spinner-border-sm text-primary" role="status">';
		echo '<span class="sr-only">Loading...</span>';
		echo '</div>';
		echo '<span class="text-muted ml-2">Loading documents...</span>';
		echo '</div>';
	}
}