<?php

class Thumbs extends WPUpload 
{
	private $_azure;
	private $_file;
	private $_wp;

	public function __construct( $azure, $file, $wp )
	{
		$this->_azure = $azure;
		$this->_file = $file;
		$this->_wp = $wp;
	}

	public function upload()
	{
		$data = $this->_file->getMetaData();

		if( $this->_file->verifyIfMetaDataSizesExists() ) {
			
			//upload to azure metadata files
			foreach( $this->_file->getMetaDataSizes() as $key => $value ) {
				
				$this->sendAndUpdateMetaDataFile( $value, $key );

				$data = $this->_file->getMetaData();
			}
		}

		return $data;
	}

	protected function sendAndUpdateMetaDataFile( $file, $key )
	{
		//if file exists, send upload and update metadata
		if ( file_exists( $file['path'] ) ) {
		
			$response = $this->_azure->upload( 
				$file['path'], 
				$this->_file->getSubDir(),
				$file['name']
			);

			if($response['status'] == 'success') {
				
				$this->_file->updateMetaDataFileName( $key, $response );

				$this->deleteOriginalFile( $file['path'] );
			}
		}
	}

	protected function deleteOriginalFile( $file )
	{
		@unlink( $file );
	}
}