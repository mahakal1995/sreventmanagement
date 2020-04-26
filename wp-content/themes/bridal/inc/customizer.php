<?php
/**
 * Bridal Theme Customizer
 *
 * @package Bridal
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function bridal_customize_register( $wp_customize ) {
	
function bridal_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
	
	$wp_customize->get_setting( 'blogname' )->bridal         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->bridal  = 'postMessage';
		
	$wp_customize->add_setting('color_scheme', array(
		'default' => '#ff5f6a',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','bridal'),
			'description'	=> __('Select color from here.','bridal'),
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	$wp_customize->add_setting('header-color', array(
		'default' => '#ffffff',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'header-color',array(
			'description'	=> __('Select background color for header.','bridal'),
			'section' => 'colors',
			'settings' => 'header-color'
		))
	);
	
	$wp_customize->add_setting('tophead-color', array(
		'default' => '#303030',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'tophead-color',array(
			'description'	=> __('Select background color for top header.','bridal'),
			'section' => 'colors',
			'settings' => 'tophead-color'
		))
	);
	
	$wp_customize->add_setting('footer-color', array(
		'default' => '#000000',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'footer-color',array(
			'description'	=> __('Select background color for footer.','bridal'),
			'section' => 'colors',
			'settings' => 'footer-color'
		))
	);
	
	// Slider Section Start		
	$wp_customize->add_section(
        'slider_section',
        array(
            'title' => __('Slider Settings', 'bridal'),
            'priority' => null,
			'description'	=> __('Recommended image size (1420x567). Slider will work only when you select the static front page.','bridal'),	
        )
    );
	
	$wp_customize->add_setting('page-setting7',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide one:','bridal'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting8',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide two:','bridal'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting9',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide three:','bridal'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('hide_slider',array(
			'default' => true,
			'sanitize_callback' => 'bridal_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_slider', array(
		   'settings' => 'hide_slider',
    	   'section'   => 'slider_section',
    	   'label'     => __('Check this to hide slider.','bridal'),
    	   'type'      => 'checkbox'
     ));	
	
	// Slider Section End
	 
// Contact Section

	$wp_customize->add_section(
        'social_section',
        array(
            'title' => __('Social Icons', 'bridal'),
            'priority' => null,
			'description'	=> __('Add your social icons and button link here.','bridal'),	
        )
    );
	
	$wp_customize->add_setting('facebook-link',array(
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('facebook-link',array(
			'type'	=> 'url',
			'label'	=> __('Add facebook link here.','bridal'),
			'section'	=> 'social_section'
	));	
	
	$wp_customize->add_setting('twitter-link',array(
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('twitter-link',array(
			'type'	=> 'url',
			'label'	=> __('Add twitter link here.','bridal'),
			'section'	=> 'social_section'
	));
	
	$wp_customize->add_setting('gplus-link',array(
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('gplus-link',array(
			'type'	=> 'url',
			'label'	=> __('Add google plus link here.','bridal'),
			'section'	=> 'social_section'
	));
	
	$wp_customize->add_setting('linked-link',array(
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('linked-link',array(
			'type'	=> 'url',
			'label'	=> __('Add linkedin link here.','bridal'),
			'section'	=> 'social_section'
	));
	
	$wp_customize->add_setting('request-txt',array(
			'sanitize_callback'	=> 'esc_url_raw'
	));
	
	$wp_customize->add_control('request-txt',array(
			'type'	=> 'url',
			'label'	=> __('Add appointment button link here.','bridal'),
			'section'	=> 'social_section'
	));	
	
}
add_action( 'customize_register', 'bridal_customize_register' );	

function bridal_css(){
		?>
        <style>
				a, 
				.tm_client strong,
				.postmeta a:hover,
				#sidebar ul li a:hover,
				.blog-post h3.entry-title,
				a.blog-more:hover,
				#commentform input#submit,
				input.search-submit,
				.nivo-controlNav a.active,
				.blog-date .date,
				a.read-more,
				.section-box .sec-left a{
					color:<?php echo esc_attr(get_theme_mod('color_scheme','#ff5f6a')); ?>;
				}
				h3.widget-title,
				.nav-links .current,
				.nav-links a:hover,
				p.form-submit input[type="submit"],
				.sitenav ul li a:hover,
				.booking-button a{
					background-color:<?php echo esc_attr(get_theme_mod('color_scheme','#ff5f6a')); ?>;
				}
				.header{
					background-color:<?php echo esc_attr(get_theme_mod('header-color','#ffffff;')); ?>;
				}
				.header-top,
				.sitenav ul li ul{
					background-color:<?php echo esc_attr(get_theme_mod('tophead-color','#303030')); ?>;
				}
				.copyright-wrapper{
					background-color:<?php echo esc_attr(get_theme_mod('footer-color','#000000')); ?>;
				}
				
		</style>
	<?php }
add_action('wp_head','bridal_css');

function bridal_customize_preview_js() {
	wp_enqueue_script( 'bridal-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20141216', true );
}
add_action( 'customize_preview_init', 'bridal_customize_preview_js' );