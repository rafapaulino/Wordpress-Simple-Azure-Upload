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
require_once CURRENT_DIR . '/strings.php';
require_once CURRENT_DIR . '/PostData.php';


$loader = new Twig_Loader_Filesystem(VIEW);
$twig = new Twig_Environment($loader, array(
    'cache' => false,
));

//create item in menu (Settings -> Windows Azure)
add_action( 'admin_menu', 'windows_azure_simple_upload_plugin_menu' );

function windows_azure_simple_upload_plugin_menu() {
	if ( current_user_can( 'manage_options' ) ) {
		global $strings;
		add_options_page(
			$strings['page_title'],
			$strings['menu_title'],
			'manage_options',
			'windows-azure-simple-upload-plugin-options',
			'windows_azure_simple_upload_plugin_options_page'
		);
	}
}

//display page settings
function windows_azure_simple_upload_plugin_options_page()
{
/*	global $twig;
	global $strings;
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

	$azure = new AzureUpload;
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

	echo $twig->render('index.html', $strings);
*/
}


//show media url correct
function azure_get_attachment_url($url, $post_id) {
	
	$post = get_post( intval($post_id) );
	if (isset($post)) {
		$url = $post->guid;
	}
	return $url;
}
add_filter('wp_get_attachment_url', 'azure_get_attachment_url', 9, 2 );

// Filter the 'srcset' attribute in 'the_content' introduced in WP 4.4.
if ( function_exists( 'wp_calculate_image_srcset' ) ) {
	add_filter( 'wp_calculate_image_srcset', 'windows_azure_wp_calculate_image_srcset', 9, 5 );
}

function windows_azure_wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id )
{
	$newSources = array();
	$media_info = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );

	foreach($media_info['sizes'] as $key => $value) 
	{
		$width = intval($value['width']);
		$newSources[$width] = array(
			'url' => esc_url($value['url']),
			'descriptor' => 'w',
			'value' => $width
		);
	}
	return $newSources;
}



function windows_azure_storage_wp_update_attachment_metadata( $data, $post_id ) {

	global $wpdb;

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


//retirar os itens desnecessários do cadastro
//verifica se existe meta data - se existir atualiza a metadata tambem 