<?php

class RASU_LoadLanguages
{
	static public function getStrings()
	{
		return array(
			'page_title' => __( 'Rafa Azure Simple Upload Plugin Settings', 'rafa-azure-simple-upload' ),
			'menu_title' => __( 'Rafa Azure Simple Upload', 'rafa-azure-simple-upload' ),
			'rasu_twig_title' => __( 'Rafa Azure Simple Upload Plugin Settings', 'rafa-azure-simple-upload' ),
			'rasu_twig_description' => __( 'This WordPress plugin allows you to use Windows Azure Storage Service to host your media for your WordPress powered blog. Windows Azure provides storage in the cloud with authenticated access and triple replication to help keep your data safe. Applications work with data using REST conventions and standard HTTP operations to identify and expose data using URIs. This plugin allows you to easily upload, retrieve, and link to files stored on Windows Azure Storage service from within WordPress.', 'rafa-azure-simple-upload' ),
			'rasu_twig_register' => __( 'If you do not have Windows Azure Storage Account, please register for Windows Azure Services.', 'rafa-azure-simple-upload' ),
			'rasu_twig_account' => __( 'Store Account Name', 'rafa-azure-simple-upload' ),
			'rasu_twig_account_title' => __( 'Windows Azure Storage Account Name', 'rafa-azure-simple-upload' ),
			'rasu_twig_key' => __( 'Store Account Key', 'rafa-azure-simple-upload' ),
			'rasu_twig_key_title' => __( 'Windows Azure Storage Account Primary Access Key', 'rafa-azure-simple-upload' ),
			'rasu_twig_container' => __( 'Default Storage Container', 'rafa-azure-simple-upload' ),
			'rasu_twig_container_title' => __( 'Default container to be used for storing media files', 'rafa-azure-simple-upload' ),
			'rasu_twig_cname' => __( 'CNAME' ),
			'rasu_twig_cname_title' => __( 'Use CNAME instead of Windows Azure Blob URL', 'rafa-azure-simple-upload' ),
			'rasu_twig_cname_description' => __( 'Note: Use this option if you would like to display image URLs belonging to your domain like', 'rafa-azure-simple-upload' ),
			'rasu_twig_cname_description2' => __( 'instead of', 'rafa-azure-simple-upload' ),
			'rasu_twig_cname_description3' => __( 'This CNAME must start with', 'rafa-azure-simple-upload' ),
			'rasu_twig_cname_description4' => __( 'and the administrator will have to update', 'rafa-azure-simple-upload' ),
			'rasu_twig_cname_description5' => __( 'Domain Name System', 'rafa-azure-simple-upload' ),
			'rasu_twig_cname_description6' => __( 'entries accordingly', 'rafa-azure-simple-upload' ),
			'rasu_twig_submit' => __( 'Save Changes', 'rafa-azure-simple-upload' ),
			'rasu_twig_save' => __( 'Saved settings.'),
			'rasu_twig_dismiss' => __( 'Dismiss this warning.', 'rafa-azure-simple-upload'),
			'rasu_twig_newcontainer' => __( 'New container name:', 'rafa-azure-simple-upload' ),
			'rasu_twig_create_newcontainer' => __( 'Create New Container', 'rafa-azure-simple-upload' ),
			'rasu_twig_create_link' => __( 'Or create a new container by clicking here.', 'rafa-azure-simple-upload' ),
			'rasu_twig_save_container' => __( 'New container created!', 'rafa-azure-simple-upload' ),
			'rasu_twig_alert' => __( 'Add all information to be able to upload to Azure!', 'rafa-azure-simple-upload' )
		);
	}
}