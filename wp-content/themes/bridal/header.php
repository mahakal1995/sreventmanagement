<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Bridal
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
	//wp_body_open hook from WordPress 5.2
	if ( function_exists( 'wp_body_open' ) ) {
	    wp_body_open();
	}else{
		do_action( 'wp_body_open' ); 
	}
?>
<a class="skip-link screen-reader-text" href="#sitemain">
<?php _e( 'Skip to content', 'bridal' ); ?></a>
<div id="header">
		<div id="navigation">
				<div class="container">
                    <div class="toggle">
                            <a class="toggleMenu" href="#"><?php esc_html_e('Menu','bridal'); ?></a>
                    </div><!-- toggle --> 	
                    <nav id="main-navigation" class="site-navigation primary-navigation sitenav" role="navigation">					
                            <?php wp_nav_menu( array('theme_location' => 'primary') ); ?>	
                    </nav><!-- navigation -->
   			 </div><!-- container -->
        </div><!-- navigation --><div class="clear"></div>
</div><!-- header -->
                

<div class="header-main">
	<div class="header-inner">
      <div class="logo">
       <?php bridal_the_custom_logo(); ?>
						<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_html(bloginfo( 'name' )); ?></a></h1>

					<?php $description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p><?php echo esc_html($description); ?></p>
					<?php endif; ?>
    </div><!-- .logo -->                 
    <div class="header_right">    
    	<div class="sb-flex">    		              
    		<div class="social-icons">
            	<?php if(!empty(get_theme_mod('facebook-link'))) { ?>
            		<a href="<?php echo esc_url(get_theme_mod('facebook-link')); ?>"><i class="fa fa-facebook"></i></a>
                <?php } ?>
                <?php if(!empty(get_theme_mod('twitter-link'))) { ?>
                    <a href="<?php echo esc_url(get_theme_mod('twitter-link')); ?>"><i class="fa fa-twitter"></i></a>
                <?php } ?>
                <?php if(!empty(get_theme_mod('gplus-link'))) { ?>
                    <a href="<?php echo esc_url(get_theme_mod('gplus-link')); ?>"><i class="fa fa-google-plus"></i></a>
                <?php } ?>
                <?php if(!empty(get_theme_mod('linked-link'))) { ?>
                    <a href="<?php echo esc_url(get_theme_mod('linked-link')); ?>"><i class="fa fa-linkedin"></i></a>
               	<?php } ?>
            </div><!-- social-icons -->
            <?php if(!empty(get_theme_mod('request-txt'))) { ?>
                <div class="booking-button">
                            <a href="<?php echo esc_url(get_theme_mod('request-txt')); ?>"><?php esc_html_e('Book Appointment','bridal'); ?></a>
                </div><!-- booking-button -->
            <?php } ?>  
        </div><!-- sb-flex -->                
    </div><!--header_right--><div class="clear"></div>  
 <div class="clear"></div>
</div><!-- .header-inner-->
</div><!-- .header-main -->