<?php

class WPAzurePostMeta
{
	protected $_attachmentId;
	protected $_metadata;
	protected $_filePath;
	protected $_fileName;
	protected $_year;
	protected $_month;
	protected $_uploadDir;
	protected $_fileNameWithFolder;
	protected $_wpdb;
	protected $_path;

	public function __construct( $metadata, $attachment_id, $wpdb )
	{
		$this->_attachmentId = $attachment_id;
		$this->_metadata = $metadata;
		$this->_filePath = get_attached_file( $attachment_id, true );
		$this->_uploadDir = wp_upload_dir();
		$this->_wpdb = $wpdb;
		$this->getPartsOfFile();
	}

	public function getFilePath()
	{
		return $this->_filePath;
	}

	public function getPath()
	{
		return $this->_uploadDir['basedir'].'/'.$this->getSubDir();
	}

	public function getSubDir()
	{
		return $this->_year.'/'.$this->_month.'/';
	}

	public function getFileName()
	{
		return $this->_fileName;
	}

	public function updateFileGUID($url,$name)
	{
		$table_name = $this->_wpdb->prefix.'posts';
		$this->_wpdb->query("UPDATE $table_name SET guid='$url' WHERE ID = '$this->_attachmentId'");

        $this->updateFileName($name);
	}

	public function verifyIfMetaDataSizesExists()
	{
		if ( count($this->_metadata) > 0 && isset($this->_metadata['sizes']) )
			return true;
		else
			return false;
	}

	public function getMetaDataSizes()
	{
		$sizes = array();
		foreach ( $this->_metadata['sizes'] as $key => $value ) {
			$sizes[$key] = array(
				'name' => $value['file'],
				'path' => $this->getPath().$value['file']
			);
		}
		return $sizes;
	}

	public function updateMetaDataFileName($key,$value,$url)
	{
		$this->_metadata['sizes'][$key]['file'] = $value;
		$this->_metadata['sizes'][$key]['url'] = $url;
	}

	protected function updateFileName($name)
	{
		$this->_fileName = $name;
		$this->_fileNameWithFolder = $this->getSubDir().$this->_fileName;

		if ( $this->verifyIfMetaDataSizesExists() ) {
			$this->_metadata['file'] = $this->_fileNameWithFolder;
		}
	}

	protected function getPartsOfFile()
	{
		$file = $this->_filePath;
		$parts = explode("/",$file);

		$this->_year = intval($parts[count($parts) -3]);
		$this->_month = $parts[count($parts) -2];
		$this->_fileName = basename($this->_filePath);
		$this->_fileNameWithFolder = $this->getSubDir().$this->_fileName;
	}

	public function __destruct()
	{
		if ( $this->verifyIfMetaDataSizesExists() ) {
			$this->updateMetadata();
		}

		if ( ! add_post_meta( $this->_attachmentId, '_wp_attached_file', $this->_fileNameWithFolder, true ) ) { 
		   update_post_meta( $this->_attachmentId, '_wp_attached_file', $this->_fileNameWithFolder );
		}
	}

	protected function updateMetadata()
	{
		if ( ! add_post_meta( $this->_attachmentId, '_wp_attachment_metadata_azure', $this->_metadata, true ) ) { 
		   update_post_meta( $this->_attachmentId, '_wp_attachment_metadata_azure', $this->_metadata );
		}
	}


}