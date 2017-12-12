<?php
/**
 * Plugin Name: Rafa Azure Simple Upload
 * Plugin URI: 
 * Description: Use the Windows Azure to host your website's media files.
 * Version: 1.1.1
 * Author: Rafael Paulino
 * Author URI: http://rafaacademy.com/
 * License: BSD 2-Clause
 * License URI: http://www.opensource.org/licenses/bsd-license.php
 */
define( 'RASU_RASU_CURRENT_DIR', dirname(__FILE__) . '/' );
define( 'RASU_VIEW', RASU_CURRENT_DIR . 'view' );

if ( ! function_exists( 'wp_handle_upload' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

require_once RASU_CURRENT_DIR . 'vendor/autoload.php';
require_once RASU_CURRENT_DIR . 'Azure.php';
require_once RASU_CURRENT_DIR . 'WPAzureOptions.php';
require_once RASU_CURRENT_DIR . 'CorrectFileName.php';
require_once RASU_CURRENT_DIR . 'AzureFactory.php';
require_once RASU_CURRENT_DIR . 'WPUpload.php';
require_once RASU_CURRENT_DIR . 'WPFile.php';
require_once RASU_CURRENT_DIR . 'File.php';
require_once RASU_CURRENT_DIR . 'FileFactory.php';
require_once RASU_CURRENT_DIR . 'Thumbs.php';
require_once RASU_CURRENT_DIR . 'ThumbsFactory.php';
require_once RASU_CURRENT_DIR . 'PostData.php';
require_once RASU_CURRENT_DIR . 'LoadLanguages.php';
require_once RASU_CURRENT_DIR . 'Tools.php';


$loader = new Twig_Loader_Filesystem(RASU_VIEW);
$twig = new Twig_Environment($loader, array(
    'cache' => false,
));

//create item in menu (Settings -> Rafa Azure)
add_action( 'admin_menu', 'rasu_azure_simple_upload_plugin_menu' );

function rasu_azure_simple_upload_plugin_menu() {
	if ( current_user_can( 'manage_options' ) ) {

		add_options_page(
			__( 'Rafa Azure Simple Upload Plugin Settings', 'rafa-azure-simple-upload' ),
			__( 'Rafa Azure Simple Upload', 'rafa-azure-simple-upload' ),
			'manage_options',
			'rafa-azure-simple-upload-plugin-options',
			'rasu_azure_simple_upload_plugin_options_page'
		);
	}
}

//display page settings and save options
function rasu_azure_simple_upload_plugin_options_page()
{
	global $twig;
	
	$strings['confirm'] = false;
	$strings['confirm_container'] = false;

	$post = new PostData;
	//insert post when user submit form
	if ( $post->isValid() && $_POST['action'] == 'save' ) {
		$post->insert($_POST);
		$strings['confirm'] = true;
	}

	//get itens from db
	$data = $post->getData();
	$strings = array_merge($strings, $data);

	$azure = AzureFactory::build();
	//create new container action
	if ( $post->isValid() && $_POST['action'] == 'new' ) {

		$azure->createContainer(trim($_POST['newcontainer']));
		$strings['confirm_container'] = true;
	}

	//get containers names
	$containers = $azure->listContainers();

	if (is_array($containers)) {
		$strings['containers'] = $containers;
	} else {
		$strings['containers'] = array();
	}

	//merge translate with strings
	$langs = LoadLanguages::getStrings();
	$strings = array_merge($strings, $langs);

	echo $twig->render('index.html', $strings);

}


//show media url correct
function rasu_azure_get_attachment_url($url, $post_id) {
	$url = Tools::changeUrl( $post_id, $url );
	return $url;
}
add_filter('wp_get_attachment_url', 'rasu_azure_get_attachment_url', 9, 2 );


// Filter the 'srcset' attribute in 'the_content' introduced in WP 4.4.
if ( function_exists( 'wp_calculate_image_srcset' ) ) {
	add_filter( 'wp_calculate_image_srcset', 'rasu_azure_wp_calculate_image_srcset', 9, 5 );
}

function rasu_azure_wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id )
{
	return Tools::getNewSources( $attachment_id, $sources );
}


//upload files
function rasu_azure_simple_upload_wp_update_attachment_metadata( $data, $post_id ) {

	global $wpdb;

	/* 
	 * Patterns utilized:
	 *
	 * Chain of Responsibility
	 * https://sourcemaking.com/design_patterns/chain_of_responsibility
	 * https://sourcemaking.com/design_patterns/chain_of_responsibility/php
	 * https://imasters.com.br/artigo/17645/php/design-patterns-e-o-desenvolvimento-em-php-chain-of-responsibility/
	 * https://www.sitepoint.com/introduction-to-chain-of-responsibility/
	 *
	 * Factory Method Design Pattern
	 * https://sourcemaking.com/design_patterns/factory_method
	 */

	//update and upload unique file (original file)
	$file = FileFactory::build( $wpdb, $post_id, $data );
	$data = $file->upload();

	//update and upload thumbs (only images are called here)
	$thumbs = ThumbsFactory::build( $wpdb, $post_id, $data );
	$data = $thumbs->upload();

	return $data;
}

add_filter(
	'wp_update_attachment_metadata',
	'rasu_azure_simple_upload_wp_update_attachment_metadata',
	9,
	2
);


//get correct url in wp.media 
add_filter( 'wp_handle_upload', 'rasu_azure_storage_wp_handle_upload' );

function rasu_azure_storage_wp_handle_upload( $uploads ) {
	
	$options = new WPAzureOptions;
	$baseUrl = $options->getCname() . '/' . $options->getContainer() . '/';

	$wp_upload_dir  = wp_upload_dir();
	$uploads['url'] = sprintf( '%1$s/%2$s/%3$s',
		untrailingslashit( $baseUrl ),
		ltrim( $wp_upload_dir['subdir'], '/' ),
		basename( $uploads['file'] )
	);

	return $uploads;
}

//get translate
//create po files: https://localise.biz/free/poeditor
function rasu_azure_load_plugin_textdomain() {
    
    load_plugin_textdomain( 'rafa-azure-simple-upload', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'rasu_azure_load_plugin_textdomain', 0 );
