<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles', 1000 );
function parallax_enqueue_scripts_styles() {
	// Styles
	wp_enqueue_style( 'icomoon-fonts', get_stylesheet_directory_uri() . '/icomoon.css', array() );
	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/style.css', array() );
	
}

// Removes Query Strings from scripts and styles
function remove_script_version( $src ){
  if ( strpos( $src, 'uploads/bb-plugin' ) !== false || strpos( $src, 'uploads/bb-theme' ) !== false ) {
    return $src;
  }
  else {
    $parts = explode( '?ver', $src );
    return $parts[0];
  }
}
add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );

// Add Additional Image Sizes
add_image_size( 'news-thumb', 260, 150, false );
add_image_size( 'news-full', 800, 300, false );
add_image_size( 'sidebar-thumb', 200, 150, false );
add_image_size( 'blog-thumb', 388, 288, true );
add_image_size( 'mailchimp', 564, 9999, false );
add_image_size( 'amp', 600, 9999, false );
add_image_size( 'home-news', 340, 187, true );
add_image_size( 'program', 314, 153, true );
add_image_size( 'product-thumb', 351, 194, true );
add_image_size( 'product-main', 1100, 539, true );
add_image_size( 'single-main', 1080, 529, true );
add_image_size( 'prod-thumb', 433, 253, true );

// Gravity Forms confirmation anchor on all forms
add_filter( 'gform_confirmation_anchor', '__return_true' );

//Sets the number of revisions for all post types
add_filter( 'wp_revisions_to_keep', 'revisions_count', 10, 2 );
function revisions_count( $num, $post ) {
	$num = 3;
    return $num;
}

// Enable Featured Images in RSS Feed and apply Custom image size so it doesn't generate large images in emails
function featuredtoRSS($content) {
global $post;
if ( has_post_thumbnail( $post->ID ) ){
$content = '<div>' . get_the_post_thumbnail( $post->ID, 'mailchimp', array( 'style' => 'margin-bottom: 15px;' ) ) . '</div>' . $content;
}
return $content;
}
 
add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');

//Show custom widget in navigation

// Register sidebar/ widget
register_sidebar( array(
'id' => 'mobile-nav-widget',
'name' => __( 'Mobile Nav Widget', 'lj-create' ),
'description' => __( 'Custom Widget Area', 'childtheme' ),
'before_widget' => '<li class="cstm-html">',
'after_widget' => '</li>',
) );

// Register sidebar/ widget UK
register_sidebar( array(
'id' => 'mobile-nav-widget-uk',
'name' => __( 'Mobile Nav Widget uk', 'lj-create' ),
'description' => __( 'Custom Widget Area', 'childtheme' ),
'before_widget' => '<li class="cstm-html">',
'after_widget' => '</li>',
) );

// Register sidebar/ widget ES
register_sidebar( array(
'id' => 'mobile-nav-widget-es',
'name' => __( 'Mobile Nav Widget es', 'lj-create' ),
'description' => __( 'Custom Widget Area', 'childtheme' ),
'before_widget' => '<li class="cstm-html">',
'after_widget' => '</li>',
) );

// add sidebar to menu
add_filter( 'wp_nav_menu_items','add_phone_mobile_menu_us', 10, 2 );
function add_phone_mobile_menu_us( $items, $args ) {
	
//	print_r($args->menu );

	if (ICL_LANGUAGE_CODE == 'en') {
//	if ($args->menu == 'main-menu') {
		ob_start();
		dynamic_sidebar('mobile-nav-widget');
		$sidebar = ob_get_contents();
		ob_end_clean();
		$items = $sidebar . $items;
//	}
	}
	return $items;
	
}

// add sidebar to menu
add_filter( 'wp_nav_menu_items','add_phone_mobile_menu_uk', 10, 2 );
function add_phone_mobile_menu_uk( $items, $args ) {
	
//	print_r($args->menu );

	if (ICL_LANGUAGE_CODE == 'uk') {
//	if ($args->menu == 'main-menu') {
		ob_start();
		dynamic_sidebar('mobile-nav-widget-uk');
		$sidebar = ob_get_contents();
		ob_end_clean();
		$items = $sidebar . $items;
//	}
	}
	return $items;
	
}

// add sidebar to menu
add_filter( 'wp_nav_menu_items','add_phone_mobile_menu_es', 10, 2 );
function add_phone_mobile_menu_es( $items, $args ) {
	
//	print_r($args->menu );

	if (ICL_LANGUAGE_CODE == 'es') {
//	if ($args->menu == 'main-menu') {
		ob_start();
		dynamic_sidebar('mobile-nav-widget-es');
		$sidebar = ob_get_contents();
		ob_end_clean();
		$items = $sidebar . $items;
//	}
	}
	return $items;
	
}


//****** AMP Customizations ******/

// Add Fav Icon to AMP Pages
add_action('amp_post_template_head','amp_favicon');
function amp_favicon() { ?>
	<link rel="icon" href="<?php echo get_site_icon_url(); ?>" />
<?php } 

// Add Banner below content of AMP Pages
add_action('ampforwp_after_post_content','amp_custom_banner_extension_insert_banner');
function amp_custom_banner_extension_insert_banner() { ?>
	<div class="amp-custom-banner-after-post">
		<h2>IF YOU HAVE ANY QUESTIONS, PLEASE DO NOT HESITATE TO CONTACT US</h2>
		<a class="ampforwp-comment-button" href="/contact-us">
			CONTACT US
		</a>
	</div>
<?php }


/**
 * Add Global Trade Identification Numbers (GTINs) to WooCommerce products.
 */

function woocommerce_render_gtin_field() {
   $input   = array(
      'id'          => '_gtin',
      'label'       => sprintf(
         '<abbr title="%1$s">%2$s</abbr>',
         _x( 'Global Trade Identification Number', 'field label', 'my-theme' ),
         _x( 'GTIN', 'abbreviated field label', 'my-theme' )
      ),
      'value'       => get_post_meta( get_the_ID(), '_gtin', true ),
      'desc_tip'    => true,
      'description' => __( 'Enter the Global Trade Identification Number (UPC, EAN, ISBN, etc.)', 'my-theme' ),
   );
?>

   <div id="gtin_attr" class="options_group">
      <?php woocommerce_wp_text_input( $input ); ?>
   </div>

<?php
}

add_action( 'woocommerce_product_options_inventory_product_data', 'woocommerce_render_gtin_field' );

/**
 * Save the product's GTIN number, if provided.
 *
 * @param int $product_id The ID of the product being saved.
 */
function woocommerce_save_gtin_field( $product_id ) {
   if (
      ! isset( $_POST['_gtin'], $_POST['woocommerce_meta_nonce'] )
      || ( defined( 'DOING_AJAX' ) && DOING_AJAX )
      || ! current_user_can( 'edit_products' )
      || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' )
   ) {
      return;
   }

   $gtin = sanitize_text_field( $_POST['_gtin'] );

   update_post_meta( $product_id, '_gtin', $gtin );
}

add_action( 'woocommerce_process_product_meta','woocommerce_save_gtin_field' );

/*
** Remove "Products" from Yoast SEO breadcrumbs in WooCommerce
*/
add_filter( 'wpseo_breadcrumb_links', function( $links ) {

    // Check if we're on a WooCommerce page
    // Checks if key 'ptarchive' is set
    // Checks if 'product' is the value of the key 'ptarchive', in position 1 in the links array 
    if ( is_woocommerce() && isset( $links[1]['ptarchive'] ) && 'product' === $links[1]['ptarchive'] ) {

        // True, remove 'Products' archive from breadcrumb links
        unset( $links[1] );
    
    }

    // Rebase array keys
    $links = array_values( $links );

    // Return modified array
    return $links;

});

// Woocomerce SKU Uniqueness
add_filter( 'wc_product_has_unique_sku', '__return_false' );

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    //wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'font-awesome' ); // FontAwesome 4
    wp_enqueue_style( 'font-awesome-5' ); // FontAwesome 5

    wp_dequeue_script( 'bootstrap' );
    wp_dequeue_script( 'jquery-fitvids' );
    wp_dequeue_script( 'jquery-waypoints' );
}, 9999 );

/* Site Optimization - Removing several assets from Home page that we dont need */

// Remove Assets from HOME page only
function remove_home_assets() {
  if (is_front_page()) {
      
	  wp_dequeue_style('yoast-seo-adminbar');
	  wp_dequeue_style('addtoany');
	  wp_dequeue_style('wpautoterms_css');
	  wp_dequeue_style('wc-blocks-vendors-style');
	  wp_dequeue_style('wc-blocks-style');
	  wp_dequeue_style('woocommerce-layout');
	  wp_dequeue_style('woocommerce-smallscreen');
	  wp_dequeue_style('woocommerce-general');
	  wp_dequeue_style('wpdreams-asl-basic');
	  wp_dequeue_style('wpdreams-ajaxsearchlite');
	  wp_dequeue_style('font-awesome-5');
	  wp_dequeue_style('font-awesome');
	  
	  wp_dequeue_script('addtoany');
	  wp_dequeue_script('wpautoterms_base');
	  wp_dequeue_script('wpdreams-ajaxsearchlite');
  }
  
};
add_action( 'wp_enqueue_scripts', 'remove_home_assets', 9999 );

// Woocommerce
add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );
function child_manage_woocommerce_styles() {
 //remove generator meta tag
 remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
 
 //first check that woo exists to prevent fatal errors
 if ( function_exists( 'is_woocommerce' ) ) {
 //dequeue scripts and styles
 if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
	wp_dequeue_script( 'wc-add-to-cart' );
 	wp_dequeue_script( 'wc-cart-fragments' );
	
	 wp_dequeue_style( 'woocommerce_frontend_styles' );
	 wp_dequeue_style( 'woocommerce_fancybox_styles' );
	 wp_dequeue_style( 'woocommerce_chosen_styles' );
	 wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
	 wp_dequeue_script( 'wc_price_slider' );
	 wp_dequeue_script( 'wc-single-product' );
	 wp_dequeue_script( 'wc-add-to-cart' );
	 wp_dequeue_script( 'wc-cart-fragments' );
	 wp_dequeue_script( 'wc-checkout' );
	 wp_dequeue_script( 'wc-add-to-cart-variation' );
	 wp_dequeue_script( 'wc-single-product' );
	 wp_dequeue_script( 'wc-cart' );
	 wp_dequeue_script( 'wc-chosen' );
	 wp_dequeue_script( 'woocommerce' );
	 wp_dequeue_script( 'prettyPhoto' );
	 wp_dequeue_script( 'prettyPhoto-init' );
	 wp_dequeue_script( 'jquery-blockui' );
	 wp_dequeue_script( 'jquery-placeholder' );
	 wp_dequeue_script( 'fancybox' );
	 wp_dequeue_script( 'jqueryui' );
	
 }
 }
 
}


//Removing unused Default Wordpress Emoji Script - Performance Enhancer
function disable_emoji_dequeue_script() {
    wp_dequeue_script( 'emoji' );
}
add_action( 'wp_print_scripts', 'disable_emoji_dequeue_script', 100 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); 
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Removes Emoji Scripts 
add_action('init', 'remheadlink');
function remheadlink() {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	remove_action('wp_head', 'wp_shortlink_header', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
}


//Product File Name
add_shortcode('filename-sc', 'filename_func');
function filename_func() {
	
	global $post;


	$file = get_field('more_info', $post->ID);
	if( $file ): 
		return '<a href="'. $file['url'] .'"> '. $file['title'] .'</a>';
	endif; 	
}

//Redirection base on GTIN
add_action('template_redirect', 'redirectToProduct');
function redirectToProduct(){
   global $wpdb;
   $requestUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
   $requestUrl = parse_url($requestUrl);
   $requestUrlPath = explode('/', $requestUrl['path']);
   $gtinId = $_GET['id'];
   
   if(in_array('product.asp', $requestUrlPath)){
      $results =  $wpdb->get_results( "SELECT post_id FROM {$wpdb->prefix}postmeta where meta_key='_gtin' and meta_value=".$gtinId." ", OBJECT);
      $id = $results[0]->post_id;
      $productUrl = get_permalink($id);
      wp_redirect($productUrl, 301 );
      exit();
   }
}

// Language redirection based on geolocation
add_action('template_redirect', 'lang_redirect');
function lang_redirect() {
	$requestPath = $_SERVER['REQUEST_URI'];
	if ( $requestPath == '/' && ! array_key_exists( 'lj_lang', $_COOKIE ) ) { // If path is site root and user has not visited before
		$ipLookupURL = "https://api.hostip.info/get_json.php?ip=" . $_SERVER['REMOTE_ADDR'];
		$ipLookupResponse = wp_remote_get( $ipLookupURL ); // Lookup location of user's IP
		$locationJSON = wp_remote_retrieve_body($ipLookupResponse);
		if ( ! is_wp_error( $locationJSON ) ) {
			$locationData = json_decode( $locationJSON, false );

			$cookieProps = array( "expires" => time()+60*60*24*30*12, "path" => '/', "secure" => true, "samesite" => true );

			if ( $locationData->{'country_code'} == 'GB' ) {
				setcookie("lj_lang", "uk", $cookieProps); // Set cookie so lookup is not needed again
				wp_redirect('https://ljcreate.com/uk/'); // Redirect to appropriate language
				exit;
			} else if ( $locationData->{'country_code'} == 'ES' ) {
				setcookie("lj_lang", "es", $cookieProps);
				wp_redirect('https://ljcreate.com/es/');
				exit;
			}
		}
	}
}

/**
 * Change several of the breadcrumb defaults
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => ' <span class="woocommerce-seperator">&gt;</span> ',
            'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
        );
}

/**
 * Add javascript to HEAD
 */
add_action( 'wp_head', 'add_lj_js' );
function add_lj_js() {
	?>
		<script>
			// Disable Google analytics (for now)
			window['ga-disable-UA-5340733-1'] = true;

			// Gets the value of a cookie
			function m_findCookie(sName) {
				var saCookies = document.cookie.split('; '),
					sRollingCookie;
				for (var i = 0; i < saCookies.length; i++) {
					sRollingCookie = saCookies[i];
					if (sRollingCookie.substring(0, sName.length) === sName)
						return sRollingCookie.split('=')[1];
				}
				return null;
			}

			function setLangCookie(sLang) {
				var oDate = new Date();
				oDate.setYear(oDate.getFullYear() + 1);
				document.cookie = "lj_lang=" + sLang + ";path=/;secure=true;samesite=strict;expires=" + oDate.toUTCString();
				return true;
			}

			// Add language cookie setter to language switcher
			function m_addLanguageCookieSetter() {
				var oaLanguageSwitchers = document.getElementsByClassName("wpml-ls-legacy-dropdown"), // Get both mobile and desktop language switchers
					oaChangeLanguageLinks;

				for (var i = 0; i < oaLanguageSwitchers.length; i++) {
					oaChangeLanguageLinks = oaLanguageSwitchers[i].getElementsByClassName("wpml-ls-link"); // Get each link from the language switcher
					for (var j = 0; j < oaChangeLanguageLinks.length; j++) {
						if (oaChangeLanguageLinks[j].innerHTML.indexOf("UK") != -1) // Check for UK/ES in the HTML inside the link. Hacky but it works
							oaChangeLanguageLinks[j].onclick = function(){return setLangCookie("uk")}; // Gotta return True otherwise the link will be cancelled
						else if (oaChangeLanguageLinks[j].innerHTML.indexOf("ES") != -1)
							oaChangeLanguageLinks[j].onclick = function(){return setLangCookie("es")};
						else
							oaChangeLanguageLinks[j].onclick = function(){return setLangCookie("en")};
					}
				}
			}
			if (document.body) // If page already loaded, add the onclick functions now, otherwise add onload listener
				m_addLanguageCookieSetter();
			else
				window.addEventListener("load", m_addLanguageCookieSetter);
			
			// Language Redirect (if applicable)
			var bHomepage = (document.location.pathname === '/' && document.location.search === ''),
				sLangSet = m_findCookie("lj_lang"),
				sPath = null,
				sBrowserLanguage = window.navigator.language || window.navigator.userLanguage; // userLanguage on IE

			if (bHomepage) { // Only redirect on homepage
				if (sLangSet === null) { // User has not chosen a language yet; navigate based on browser language (in case PHP redirection fails)
					if (sBrowserLanguage == 'en-GB') {
						sPath = '/uk/';
						setLangCookie("uk");
					} else if (sBrowserLanguage.indexOf('es') == 0) { // Catch subtypes of Spanish (eg Mexican)
						sPath = '/es/';
						setLangCookie("es");
					} else
						setLangCookie("us");
				} else { // User has chosen a language so nav to that one
					if (sLangSet == 'uk')
						sPath = '/uk/';
					else if (sLangSet == 'es')
						sPath = '/es/';
				}

				if (sPath && sPath !== window.location.pathname)
					window.location.replace(window.location.origin + sPath);
			}

			// Draw cookie consent box
			function m_showCookieConsentForm() {
				var oCookieBox = document.createElement("div"),
					oCookieHeader = document.createElement("h3"),
					oCookieText = document.createElement("p"),
					oCookieAcceptBtn = document.createElement("button"),
					oCookieRejectBtn = document.createElement("button");
				oCookieBox.id = "cookie-box";
				<?php if (ICL_LANGUAGE_CODE == 'es') {
					?>oCookieHeader.innerText = "Aviso sobre las cookies";<?php
				} else {
					?>oCookieHeader.innerText = "Cookie Notice";<?php
				}?>
		<?php
			if (ICL_LANGUAGE_CODE == 'uk') { // UK Text
				?>oCookieText.innerHTML = "We use cookies to provide essential functionality on our website. With your consent, we'll also use cookies to improve our services. For more information, please see our <a href='/uk/privacy-policy'>Privacy Policy</a>.";<?php
			} else if (ICL_LANGUAGE_CODE == 'es') { // Spanish Text
				?>oCookieText.innerHTML = "Utilizamos cookies para proporcionar una funcionalidad esencial en nuestro sitio web. Con su consentimiento, también utilizaremos cookies para mejorar nuestros servicios. Para más información, consulte nuestra <a href='/es/privacy-policy'>Política de Privacidad</a>.";<?php
			} else { // US Text
				?>oCookieText.innerHTML = "We use cookies to provide essential functionality on our website. With your consent, we'll also use cookies to improve our services. For more information, please see our <a href='/privacy-policy'>Privacy Policy</a>.";<?php
			}
		?>
				oCookieAcceptBtn.innerText = "Accept";
				oCookieAcceptBtn.onclick = m_acceptCookies;
				oCookieRejectBtn.innerText = "Reject";
				oCookieRejectBtn.onclick = m_rejectCookies;
				oCookieBox.appendChild(oCookieHeader);
				oCookieBox.appendChild(oCookieText);
				oCookieBox.appendChild(oCookieAcceptBtn);
				oCookieBox.appendChild(oCookieRejectBtn);
				document.body.appendChild(oCookieBox);

				function m_acceptCookies() {
					oCookieAcceptBtn.onclick = oCookieRejectBtn.onclick = null; // Disable buttons in case of rapid click
					var oDate = new Date();
					oDate.setYear(oDate.getFullYear() + 1); // Cookie expiration date is current date + 1 year
					document.cookie = "ck_consent=true;path=/;secure=true;samesite=strict;expires=" + oDate.toUTCString(); // Set cookie
					oCookieBox.style.opacity = 0; // Hide form
					window['ga-disable-UA-5340733-1'] = false; // Re-enable analytics
				}
				function m_rejectCookies() {
					oCookieAcceptBtn.onclick = oCookieRejectBtn.onclick = null;
					var oDate = new Date();
					oDate.setYear(oDate.getFullYear() + 1);
					document.cookie = "ck_consent=false;path=/;secure=true;samesite=strict;expires=" + oDate.toUTCString();
					oCookieBox.style.opacity = 0;
				}
			}

			var bCookieConsent = m_findCookie("ck_consent");
			if (bCookieConsent === true) // Re-enable analytics if user has consented
				window['ga-disable-UA-5340733-1'] = false;
			else if (bCookieConsent === null) { // If user hasn't made a decision, show cookie consent box
				if (document.body)
					m_showCookieConsentForm();
				else
					window.addEventListener("load", m_showCookieConsentForm);
			}
		</script>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-5340733-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-5340733-1');
		</script>
	<?php
}

/**
 * Remove 'Private' from hidden product titles
 */
function remove_private_prefix($title) {
	$title = str_replace('Private: ', '', $title);
	return $title;
}
add_filter('the_title', 'remove_private_prefix');
