<?php
/**
 * Plugin Name: Windows Azure Simple Upload
 * Plugin URI: https://wordpress.org/plugins/windows-azure-storage/
 * Description: Use the Windows Azure to host your website's media files.
 * Version: 1.0.0
 * Author: Rafael Paulino
 * Author URI: http://rafaacademy.com/
 * License: BSD 2-Clause
 * License URI: http://www.opensource.org/licenses/bsd-license.php
 */
define( 'CURRENT_DIR', dirname(__FILE__) . '/' );
define( 'VIEW', CURRENT_DIR . 'view' );

require_once CURRENT_DIR . 'vendor/autoload.php';
require_once CURRENT_DIR . 'Azure.php';
require_once CURRENT_DIR . 'WPAzureOptions.php';
require_once CURRENT_DIR . 'CorrectFileName.php';
require_once CURRENT_DIR . 'AzureFactory.php';
require_once CURRENT_DIR . 'WPUpload.php';
require_once CURRENT_DIR . 'WPFile.php';
require_once CURRENT_DIR . 'File.php';
require_once CURRENT_DIR . 'FileFactory.php';
require_once CURRENT_DIR . 'Thumbs.php';
require_once CURRENT_DIR . 'ThumbsFactory.php';
require_once CURRENT_DIR . 'PostData.php';
require_once CURRENT_DIR . 'LoadLanguages.php';
require_once CURRENT_DIR . 'Tools.php';


$loader = new Twig_Loader_Filesystem(VIEW);
$twig = new Twig_Environment($loader, array(
    'cache' => false,
));

//create item in menu (Settings -> Windows Azure)
add_action( 'admin_menu', 'windows_azure_simple_upload_plugin_menu' );

function windows_azure_simple_upload_plugin_menu() {
	if ( current_user_can( 'manage_options' ) ) {

		add_options_page(
			__( 'Windows Azure Simple Upload Plugin Settings', 'windows-azure-simple-upload' ),
			__( 'Windows Azure Simple Upload', 'windows-azure-simple-upload' ),
			'manage_options',
			'windows-azure-simple-upload-plugin-options',
			'windows_azure_simple_upload_plugin_options_page'
		);
	}
}

//display page settings and save options
function windows_azure_simple_upload_plugin_options_page()
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
function azure_get_attachment_url($url, $post_id) {
	$url = Tools::changeUrl( $post_id, $url );
	return $url;
}
add_filter('wp_get_attachment_url', 'azure_get_attachment_url', 9, 2 );


// Filter the 'srcset' attribute in 'the_content' introduced in WP 4.4.
if ( function_exists( 'wp_calculate_image_srcset' ) ) {
	add_filter( 'wp_calculate_image_srcset', 'windows_azure_wp_calculate_image_srcset', 9, 5 );
}

function windows_azure_wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id )
{
	return Tools::getNewSources( $attachment_id );
}



function windows_azure_storage_wp_update_attachment_metadata( $data, $post_id ) {

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
	'windows_azure_storage_wp_update_attachment_metadata',
	9,
	2
);

//get translate
function azure_load_plugin_textdomain() {
    
    load_plugin_textdomain( 'windows-azure-simple-upload', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'azure_load_plugin_textdomain', 0 );
