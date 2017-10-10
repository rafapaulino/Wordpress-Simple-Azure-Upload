<?php

class LoadLanguages
{
	static public function getStrings()
	{
		return array(
			'page_title' => __( 'Windows Azure Simple Upload Plugin Settings', 'windows-azure-simple-upload' ),
			'menu_title' => __( 'Windows Azure Simple Upload', 'windows-azure-simple-upload' ),
			'title' => __( 'Windows Azure Simple Upload Plugin Settings', 'windows-azure-simple-upload' ),
			'description' => __( 'This WordPress plugin allows you to use Windows Azure Storage Service to host your media for your WordPress powered blog. Windows Azure provides storage in the cloud with authenticated access and triple replication to help keep your data safe. Applications work with data using REST conventions and standard HTTP operations to identify and expose data using URIs. This plugin allows you to easily upload, retrieve, and link to files stored on Windows Azure Storage service from within WordPress.', 'windows-azure-simple-upload' ),
			'register' => __( 'If you do not have Windows Azure Storage Account, please register for Windows Azure Services.', 'windows-azure-simple-upload' ),
			'azure_storage_account_name' => __( 'Store Account Name', 'windows-azure-simple-upload' ),
			'azure_storage_account_name_title' => __( 'Windows Azure Storage Account Name', 'windows-azure-simple-upload' ),
			'azure_storage_account_primary_access_key' => __( 'Store Account Key', 'windows-azure-simple-upload' ),
			'azure_storage_account_primary_access_key_title' => __( 'Windows Azure Storage Account Primary Access Key', 'windows-azure-simple-upload' ),
			'default_azure_storage_account_container_name' => __( 'Default Storage Container', 'windows-azure-simple-upload' ),
			'default_azure_storage_account_container_name_title' => __( 'Default container to be used for storing media files', 'windows-azure-simple-upload' ),
			'cname' => __( 'CNAME' ),
			'cname_title' => __( 'Use CNAME instead of Windows Azure Blob URL', 'windows-azure-simple-upload' ),
			'cname_description' => __( 'Note: Use this option if you would like to display image URLs belonging to your domain like', 'windows-azure-simple-upload' ),
			'cname_description2' => __( 'instead of', 'windows-azure-simple-upload' ),
			'cname_description3' => __( 'This CNAME must start with', 'windows-azure-simple-upload' ),
			'cname_description4' => __( 'and the administrator will have to update', 'windows-azure-simple-upload' ),
			'cname_description5' => __( 'Domain Name System', 'windows-azure-simple-upload' ),
			'cname_description6' => __( 'entries accordingly', 'windows-azure-simple-upload' ),
			'submit' => __( 'Save Changes', 'windows-azure-simple-upload' ),
			'save' => __( 'Saved settings.'),
			'dismiss' => __( 'Dismiss this warning.', 'windows-azure-simple-upload'),
			'newcontainer' => __( 'New container name:', 'windows-azure-simple-upload' ),
			'create_newcontainer' => __( 'Create New Container', 'windows-azure-simple-upload' ),
			'create_link' => __( 'Or create a new container by clicking here.', 'windows-azure-simple-upload' ),
			'save_container' => __( 'New container created!', 'windows-azure-simple-upload' )
		);
	}
}