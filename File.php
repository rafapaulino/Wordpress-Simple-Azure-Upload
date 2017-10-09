<?php

class File extends WPUpload 
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

		if ( file_exists( $this->_file->getFilePath() ) )
		{
			$response = $this->_azure->upload( 
				$this->_file->getFilePath(), 
				$this->_file->getSubDir(),
				$this->_file->getFileName()
			);

			if($response['status'] == 'success') {
				
				$this->update( $response );

				$this->deleteOriginalFile();

				$data = $this->_file->getMetaData();
			}
		}

		return $data;
	}

	protected function update( $response )
	{
		$url = $response['fileUrl'];
		$id = intval($this->_file->getAttachamentId());
		$table_name = $this->_wp->prefix.'posts';
		
		$this->_wp->query("UPDATE $table_name SET guid='$url' WHERE ID = '$id'");

		$this->metadata( $response );
	}

	protected function metadata( $response )
	{
		$name = $response['name'];
		$url = $response['fileUrl'];

		$this->_file->setFileName( $name );
		$this->_file->setFileNameWithFolder( $name );

		if ( $this->_file->verifyIfMetaDataSizesExists() ) 
		{
			$id = intval($this->_file->getAttachamentId());
			$file = $this->_file->getFileNameWithFolder();

			if ( ! add_post_meta( $id, '_wp_attached_file', $file, true ) ) { 
			   update_post_meta( $id, '_wp_attached_file', $file );
			}

			//update post meta
			$this->_file->setFileNameMetaData( $file, $url );
		}
	}

	protected function deleteOriginalFile()
	{
		@unlink( $this->_file->getFilePath() );
	}
}