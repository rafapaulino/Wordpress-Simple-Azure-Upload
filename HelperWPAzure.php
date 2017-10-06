<?php

class HelperWPAzure 
{
	protected $_wpAzure;
	protected $_azure;

	public function __construct( $wpAzure, $azure )
	{
		$this->_wpAzure = $wpAzure;
		$this->_azure = $azure;
	}

	public function azureIsDefaultUpload()
	{
		return (bool) get_option( 'azure_storage_use_for_default_upload' );
	}

	public function sendPrimaryFile()
	{
		$response['status'] = 'error';

		if ( $this->azureIsDefaultUpload()  && file_exists($this->_wpAzure->getFilePath()) ) {
			
			$response = $this->_azure->sendFile( 
				$this->_wpAzure->getFilePath(), 
				$this->_wpAzure->getFileName(), 
				$this->_wpAzure->getSubDir() 
			);
		}
		return $response;
	}

	public function sendMetaDataFiles()
	{
		if( $this->_wpAzure->verifyIfMetaDataSizesExists() ) {
			//upload to azure metadata files
			foreach( $this->_wpAzure->getMetaDataSizes() as $key => $value ) {
				$this->sendAndUpdateMetaDataFile( $value, $key );
			}
		}
	}

	protected function sendAndUpdateMetaDataFile( $file, $key )
	{
		//if file exists, send upload and update metadata
		if ( file_exists($file['path']) ) {
			
			$resp = $this->_azure->sendFile( 
				$file['path'], 
				$file['name'], 
				$this->_wpAzure->getSubDir() 
			);

			if($resp['status'] == 'success') {
				$this->_wpAzure->updateMetaDataFileName($key,$resp['name'],$resp['fileUrl']);
			}
		}
	}

	public static function correctMetaDataFields($wpdb)
	{
		$table_name = $wpdb->prefix.'postmeta';
		
		$sql = "UPDATE $table_name SET `meta_key`='_wp_attachment_metadata' 
			WHERE `meta_key`='_wp_attachment_metadata_azure'";
		$query = $wpdb->query($sql);
	}
}