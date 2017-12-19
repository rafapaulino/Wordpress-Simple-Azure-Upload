<?php
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

class RASU_Azure
{
	private $_WPObject;
	private $_CorrectNameObject;
	private $_connectionString;
	private $_blobRestProxy;

	public function __construct( $wp, $name )
	{
		$this->_WPObject = $wp;
		$this->_CorrectNameObject = $name;

		if (trim($this->_WPObject->getAccount()) !== "") {
			$this->_connectionString = "DefaultEndpointsProtocol=http;AccountName=".$this->_WPObject->getAccount().";AccountKey=".$this->_WPObject->getKey();

			$this->_blobRestProxy = ServicesBuilder::getInstance()->createBlobService($this->_connectionString);
		} else {
			$this->_connectionString = "";
			$this->_blobRestProxy = null;
		}
	}

	public function listContainers()
	{
	    $containers = array();

	    if (trim($this->_WPObject->getAccount()) !== "") {
		    
		    try {

		        $listContainersOptions = new ListContainersOptions;
		        $listContainersResult = $this->_blobRestProxy->listContainers($listContainersOptions);

		        foreach ($listContainersResult->getContainers() as $container)
	    		{
	    			$containers[] = $container->getName();
	    		}

		    } catch (ServiceException $e) {
		        return $e;
		    }
		    
		}

	    return $containers;
	}

	public function createContainer($name)
	{
		$createContainerOptions = new CreateContainerOptions();
		$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
		try {
			$name = $this->_CorrectNameObject->getName($name);
	        // Create container.
	        $this->_blobRestProxy->createContainer($name, $createContainerOptions);
	    } catch (ServiceException $e) {
	        return $e;
	    }
	}

	public function upload($localFileName, $subdir, $name)
	{
		$content = fopen($localFileName, "r");

		try {
		
			$subdir = date("Y/m/");
			$fileName = $name; //$this->_CorrectNameObject->getName($name);
			$name = $subdir.$fileName;
    
		    //Upload blob
		    $blb = $this->_blobRestProxy->createBlockBlob($this->_WPObject->getContainer(), $name, $content);

		    $file = $this->setUrlFile($name);
		    
		    $message = array(
		    	'status' => 'success',
		    	'fileBlobUrl' => $file['blob'],
		    	'fileUrl' => $file['file'],
		    	'name' => $fileName,
		    	'subdir' => $subdir
		    );
		    return $message;

		}
		catch(ServiceException $e) {
		    return $e;
		}
	}

	private function setUrlFile($name)
	{
		return array(
			'blob' => 'http://'.$this->_WPObject->getAccount().'.blob.core.windows.net/'.$this->_WPObject->getContainer().'/'.$name,
			'file' => $this->_WPObject->getCname().'/'.$this->_WPObject->getContainer().'/'.$name
		);
	}
}