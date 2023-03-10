<?php
if ( class_exists( 'CurlyCoreClassWidget' ) ) {

	class CurlyMikadofSeparatorWidget extends CurlyCoreClassWidget {
		public function __construct() {
			parent::__construct(
				'mkdf_separator_widget',
				esc_html__( 'Curly Separator Widget', 'curly' ),
				array( 'description' => esc_html__( 'Add a separator element to your widget areas', 'curly' ) )
			);
			
			$this->setParams();
		}
		
		protected function setParams() {
			$this->params = array(
				array(
					'type'    => 'dropdown',
					'name'    => 'type',
					'title'   => esc_html__( 'Type', 'curly' ),
					'options' => array(
						'normal'     => esc_html__( 'Normal', 'curly' ),
						'full-width' => esc_html__( 'Full Width', 'curly' )
					)
				),
				array(
					'type'    => 'dropdown',
					'name'    => 'position',
					'title'   => esc_html__( 'Position', 'curly' ),
					'options' => array(
						'center' => esc_html__( 'Center', 'curly' ),
						'left'   => esc_html__( 'Left', 'curly' ),
						'right'  => esc_html__( 'Right', 'curly' )
					)
				),
				array(
					'type'    => 'dropdown',
					'name'    => 'border_style',
					'title'   => esc_html__( 'Style', 'curly' ),
					'options' => array(
						'solid'  => esc_html__( 'Solid', 'curly' ),
						'dashed' => esc_html__( 'Dashed', 'curly' ),
						'dotted' => esc_html__( 'Dotted', 'curly' )
					)
				),
				array(
					'type'  => 'colorpicker',
					'name'  => 'color',
					'title' => esc_html__( 'Color', 'curly' )
				),
				array(
					'type'  => 'textfield',
					'name'  => 'width',
					'title' => esc_html__( 'Width (px or %)', 'curly' )
				),
				array(
					'type'  => 'textfield',
					'name'  => 'thickness',
					'title' => esc_html__( 'Thickness (px)', 'curly' )
				),
				array(
					'type'  => 'textfield',
					'name'  => 'top_margin',
					'title' => esc_html__( 'Top Margin (px or %)', 'curly' )
				),
				array(
					'type'  => 'textfield',
					'name'  => 'bottom_margin',
					'title' => esc_html__( 'Bottom Margin (px or %)', 'curly' )
				)
			);
		}
		
		public function widget( $args, $instance ) {
			if ( ! is_array( $instance ) ) {
				$instance = array();
			}
			
			//prepare variables
			$params = '';
			
			//is instance empty?
			if ( is_array( $instance ) && count( $instance ) ) {
				//generate shortcode params
				foreach ( $instance as $key => $value ) {
					$params .= " $key='$value' ";
				}
			}
			
			echo '<div class="widget mkdf-separator-widget">';
			echo do_shortcode( "[mkdf_separator $params]" ); // XSS OK
			echo '</div>';
		}
	}
}