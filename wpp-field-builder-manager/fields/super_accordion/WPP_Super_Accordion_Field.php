<?php
/**
 * WPP Super Accordion Field
 *
 * Поле аккордеона, которое может содержать другие поля внутри.
 * Поддерживает открытие/закрытие по клику на заголовке.
 * Динамически обновляет заголовок на основе значений полей.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WPP_Super_Accordion_Field') && class_exists('WPP_Form_Field')) :

    class WPP_Super_Accordion_Field extends WPP_Form_Field {

        public function __construct($args = []) {
            parent::__construct($args);

            // Подключаем JS/CSS только если используется
            add_action('wp_footer', [$this, 'enqueue_assets'], 100);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_assets'], 100);
        }

        public function enqueue_assets() {
            wp_enqueue_script(
                'wpp-super_accordion',
                WPP_FIELD_BUILDER_URL . 'fields/super_accordion/super_accordion.js',
                ['jquery'],
                file_exists(WPP_FIELD_BUILDER_PATH . 'fields/super_accordion/super_accordion.js')
                    ? filemtime(WPP_FIELD_BUILDER_PATH . 'fields/super_accordion/super_accordion.js')
                    : time(),
                true
            );

            wp_enqueue_style(
                'wpp-super_accordion',
                WPP_FIELD_BUILDER_URL . 'fields/super_accordion/super_accordion.css',
                [],
                file_exists(WPP_FIELD_BUILDER_PATH . 'fields/super_accordion/super_accordion.css')
                    ? filemtime(WPP_FIELD_BUILDER_PATH . 'fields/super_accordion/super_accordion.css')
                    : time()
            );
        }

        /**
         * Рендерит аккордеон с внутренними полями
         */
        public function render() {
            $this->render_wrapper_start();

            $id = sanitize_key($this->args['name']);
            $title = !empty($this->args['title']) ? esc_html($this->args['title']) : 'Details';
            $header_template = !empty($this->args['header']) ? $this->args['header'] : '';
            $fields = !empty($this->args['fields']) && is_array($this->args['fields']) ? $this->args['fields'] : [];
            $is_open = !empty($this->args['open']);

            ?>
            <div class="wpp-super-accordion <?php echo $is_open ? 'open' : ''; ?>" id="<?php echo esc_attr($id); ?>"
                 data-header="<?php echo esc_attr($header_template); ?>">
                <div class="wpp-super-accordion-header">
                    <h5 class="row">
                        <?php if ($header_template): ?>
                            <span class="dynamic-header"><?php echo esc_html($title); ?></span>
                        <?php else: ?>
                            <?php echo $title; ?>
                        <?php endif; ?>
                    </h5>
                    <span class="toggle-icon"><?php echo $is_open ? '▼' : '▶'; ?></span>
                </div>

                <div class="wpp-super-accordion-body row" style="display: <?php echo $is_open ? 'block' : 'none'; ?>;">
                    <?php foreach ($fields as $name => $config):
                        $class_name = 'WPP_' . ucfirst($config['type']) . '_Field';

                        if (class_exists($class_name)) {
                            $full_name = $id . '[' . $name . ']';
                            $field_args = array_merge($config, ['name' => $full_name]);
                            
                            if (isset($this->args['default'][$name])) {
                                $field_args['default'] = $this->args['default'][$name];
                            }
                            
                            $field = new $class_name($field_args);
                            $field->render();
                        }
                    endforeach; ?>
                </div>
            </div>
            <?php

            $this->render_description();
            $this->render_wrapper_end();
        }
    }

endif;
