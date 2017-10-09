<?php

class WPFile
{
	private $_attachmentId;
	private $_metadata;
	private $_filePath;
	private $_fileName;
	private $_year;
	private $_month;
	private $_uploadDir;
	private $_fileNameWithFolder;
	private $_wpdb;
	private $_path;

	public function __construct( $wpdb, $attachment_id, $metadata )
	{
		$this->_wpdb = $wpdb;
		$this->_attachmentId = $attachment_id;
		$this->_metadata = $metadata;
		$this->_filePath = get_attached_file( $attachment_id, true );
		$this->_uploadDir = wp_upload_dir();
		$this->getPartsOfFile();
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

	public function setFileNameMetaData( $name, $url )
	{
		$this->_metadata['file'] = $name;
		$this->_metadata['url'] = $url;
	}

	public function setFileName( $name )
	{
		$this->_fileName = $name;
	}

	public function setFileNameWithFolder( $name )
	{
		$this->_fileNameWithFolder = $this->getSubDir() . $name;
	}

	public function getFileNameWithFolder()
	{
		return $this->_fileNameWithFolder;
	}

	public function getMetaData()
	{
		return $this->_metadata;
	}

	public function getAttachamentId()
	{
		return $this->_attachmentId;
	}

	public function getSubDir()
	{
		return $this->_year.'/'.$this->_month.'/';
	}

	public function getFilePath()
	{
		return $this->_filePath;
	}

	public function getPath()
	{
		return $this->_uploadDir['basedir'].'/'.$this->getSubDir();
	}

	public function getFileName()
	{
		return $this->_fileName;
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

	public function verifyIfMetaDataSizesExists()
	{
		if ( count($this->_metadata) > 0 && isset($this->_metadata['sizes']) )
			return true;
		else
			return false;
	}

	public function updateMetaDataFileName( $key, $response )
	{
		$name = $response['name'];
		$url = $response['fileUrl'];

		$this->_metadata['sizes'][$key]['file'] = $name;
		$this->_metadata['sizes'][$key]['url'] = $url;
	}
}