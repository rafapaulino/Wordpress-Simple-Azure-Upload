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
define( 'RASU_CURRENT_DIR', dirname(__FILE__) . '/' );
define( 'RASU_VIEW', RASU_CURRENT_DIR . 'view' );

if ( ! function_exists( 'wp_handle_upload' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

require_once RASU_CURRENT_DIR . 'vendor/autoload.php';
require_once RASU_CURRENT_DIR . 'RASU_Azure.php';
require_once RASU_CURRENT_DIR . 'RASU_WPAzureOptions.php';
require_once RASU_CURRENT_DIR . 'RASU_CorrectFileName.php';
require_once RASU_CURRENT_DIR . 'RASU_AzureFactory.php';
require_once RASU_CURRENT_DIR . 'RASU_WPUpload.php';
require_once RASU_CURRENT_DIR . 'RASU_WPFile.php';
require_once RASU_CURRENT_DIR . 'RASU_File.php';
require_once RASU_CURRENT_DIR . 'RASU_FileFactory.php';
require_once RASU_CURRENT_DIR . 'RASU_Thumbs.php';
require_once RASU_CURRENT_DIR . 'RASU_ThumbsFactory.php';
require_once RASU_CURRENT_DIR . 'RASU_PostData.php';
require_once RASU_CURRENT_DIR . 'RASU_LoadLanguages.php';
require_once RASU_CURRENT_DIR . 'RASU_Tools.php';


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
	
	$strings['rasu_confirm'] = false;
	$strings['rasu_confirm_container'] = false;

	$post = new RASU_PostData;
	//insert post when user submit form
	if ( $post->isValid() && $_POST['rasu_action'] == 'save' ) {
		$post->insert($_POST);
		$strings['rasu_confirm'] = true;
	}

	//get itens from db
	$data = $post->getData();
	$strings = array_merge($strings, $data);

	$azure = RASU_AzureFactory::build();
	//create new container action
	if ( $post->isValid() && $_POST['rasu_action'] == 'new' ) {

		$azure->createContainer(trim($_POST['rasu_twig_newcontainer']));
		$strings['rasu_twig_rasu_confirm_container'] = true;
	}

	//get containers names
	$containers = $azure->listContainers();

	if (is_array($containers)) {
		$strings['rasu_containers'] = $containers;
	} else {
		$strings['rasu_containers'] = array();
	}

	//merge translate with strings
	$langs = RASU_LoadLanguages::getStrings();
	$strings = array_merge($strings, $langs);

	echo $twig->render('index.html', $strings);

}


//show media url correct
function rasu_azure_get_attachment_url($url, $post_id) {
	$url = RASU_Tools::changeUrl( $post_id, $url );
	return $url;
}
add_filter('wp_get_attachment_url', 'rasu_azure_get_attachment_url', 9, 2 );


// Filter the 'srcset' attribute in 'the_content' introduced in WP 4.4.
if ( function_exists( 'wp_calculate_image_srcset' ) ) {
	add_filter( 'wp_calculate_image_srcset', 'rasu_azure_wp_calculate_image_srcset', 9, 5 );
}

function rasu_azure_wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id )
{
	return RASU_Tools::getNewSources( $attachment_id, $sources );
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
	$file = RASU_FileFactory::build( $wpdb, $post_id, $data );
	$data = $file->upload();

	//update and upload thumbs (only images are called here)
	$thumbs = RASU_ThumbsFactory::build( $wpdb, $post_id, $data );
	$data = $thumbs->upload();

	return $data;
}

add_filter(
	'wp_update_attachment_metadata',
	'rasu_azure_simple_upload_wp_update_attachment_metadata',
	9,
	2
);

add_filter('wp_handle_upload_prefilter', 'rasu_azure_custom_upload_filter' );

function rasu_azure_custom_upload_filter( $file ) {
	$obj = new RASU_CorrectFileName;
	$file['name'] = $obj->getName($file['name']);
	return $file;
}


//get correct url in wp.media 
add_filter( 'wp_handle_upload', 'rasu_azure_storage_wp_handle_upload' );

function rasu_azure_storage_wp_handle_upload( $uploads ) {
	
	$options = new RASU_WPAzureOptions;
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


//alert if fields is empty
function rasu_empty_notice() {

	$azure = new RASU_WPAzureOptions;
	$account = $azure->getAccount();
	$key = $azure->getKey();
	$container = $azure->getContainer();
	$cname = $azure->getCname();

	if (trim($account) == "" || trim($key) == "" && trim($container) == "" || trim($cname) == ""):
?>
    <div class="error notice">
        <p><?php _e( 'Add all information to be able to upload to Azure!', 'rafa-azure-simple-upload' ); ?></p>
    </div>
<?php
	endif; 
}
add_action( 'admin_notices', 'rasu_empty_notice' );