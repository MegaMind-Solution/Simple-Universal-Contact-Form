<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ----------------------------
 * Gutenberg Block
 * ----------------------------
 */
function sucf_register_gutenberg_block() {
    if (!function_exists('register_block_type')) return;

    register_block_type('sucf/contact-form', [
        'editor_script'   => 'sucf-editor-script', // enqueue separately
        'render_callback' => 'sucf_render_form',
        'attributes' => [
            'show_name'    => ['type' => 'boolean', 'default' => true],
            'show_email'   => ['type' => 'boolean', 'default' => true],
            'show_phone'   => ['type' => 'boolean', 'default' => true],
            'show_message' => ['type' => 'boolean', 'default' => true],
            'button_text'  => ['type' => 'string', 'default' => 'Send Message'],
            'layout'       => ['type' => 'string', 'default' => 'block'],
            'columns'      => ['type' => 'number', 'default' => 1],
            'gap'          => ['type' => 'number', 'default' => 15],
        ],
    ]);
}
add_action('init', 'sucf_register_gutenberg_block');

/**
 * ----------------------------
 * Elementor Widget
 * ----------------------------
 */
function sucf_register_elementor_widget($widgets_manager) {
    if (!class_exists('Elementor\Widget_Base')) return;

    class SUCF_Elementor_Widget extends \Elementor\Widget_Base {

        public function get_name() {
            return 'sucf_contact_form';
        }

        public function get_title() {
            return 'Simple Contact Form';
        }

        public function get_icon() {
            return 'eicon-mail';
        }

        public function get_categories() {
            return ['general'];
        }

        protected function register_controls() {

            // ---------------- Fields Section ----------------
            $this->start_controls_section('section_fields', ['label' => 'Form Fields']);

            $fields = ['show_name', 'show_email', 'show_phone', 'show_message'];
            foreach ($fields as $field) {
                $this->add_control(
                    $field,
                    [
                        'label' => ucfirst(str_replace('_', ' ', $field)),
                        'type'  => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => 'Show',
                        'label_off' => 'Hide',
                        'return_value' => 'yes',
                        'default' => 'yes',
                    ]
                );
            }

            $this->add_control(
                'button_text',
                [
                    'label' => 'Button Text',
                    'type'  => \Elementor\Controls_Manager::TEXT,
                    'default' => 'Send Message',
                ]
            );

            $this->end_controls_section();

            // ---------------- Layout Section ----------------
            $this->start_controls_section('section_layout', ['label' => 'Layout']);

            $this->add_control(
                'layout',
                [
                    'label' => 'Layout Type',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'block',
                    'options' => [
                        'block' => 'Block',
                        'flex'  => 'Flex',
                        'grid'  => 'Grid',
                    ],
                ]
            );

            $this->add_control(
                'columns',
                [
                    'label' => 'Columns (for Grid)',
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 4,
                    'default' => 1,
                ]
            );

            $this->add_control(
                'gap',
                [
                    'label' => 'Gap (px)',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'range' => ['px' => ['min'=>0,'max'=>50]],
                    'default' => ['size'=>15],
                    'selectors' => [],
                ]
            );

            $this->end_controls_section();

            // ---------------- Style Section ----------------
            $this->start_controls_section('section_style', [
                'label' => 'Style',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]);

            $this->add_control(
                'field_color',
                [
                    'label' => 'Field Text Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .sucf-form input, {{WRAPPER}} .sucf-form textarea' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'field_bg',
                [
                    'label' => 'Field Background',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .sucf-form input, {{WRAPPER}} .sucf-form textarea' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'border_color',
                [
                    'label' => 'Border Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .sucf-form input, {{WRAPPER}} .sucf-form textarea' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'border_radius',
                [
                    'label' => 'Border Radius (px)',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'range' => ['px' => ['min'=>0,'max'=>50]],
                    'selectors' => [
                        '{{WRAPPER}} .sucf-form input, {{WRAPPER}} .sucf-form textarea, {{WRAPPER}} .sucf-form button' => 'border-radius: {{SIZE}}px;',
                    ],
                ]
            );

            $this->add_control(
                'button_bg',
                [
                    'label' => 'Button Background',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .sucf-form button' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_color',
                [
                    'label' => 'Button Text Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .sucf-form button' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();
            $atts = [
                'show_name'    => $settings['show_name'] === 'yes',
                'show_email'   => $settings['show_email'] === 'yes',
                'show_phone'   => $settings['show_phone'] === 'yes',
                'show_message' => $settings['show_message'] === 'yes',
                'button_text'  => $settings['button_text'],
                'layout'       => $settings['layout'],
                'columns'      => $settings['columns'],
                'gap'          => $settings['gap']['size'] ?? 15,
                'form_id'      => 'sucf_elementor_' . $this->get_id(),
            ];

            sucf_render_form($atts);
        }
    }

    $widgets_manager->register(new SUCF_Elementor_Widget());
}
add_action('elementor/widgets/register', 'sucf_register_elementor_widget');
