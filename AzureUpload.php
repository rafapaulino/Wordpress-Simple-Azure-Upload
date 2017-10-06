<?php 
/*
 * This class use MICROSOFT WINDOWS AZURE PHP LIBRARY
 * https://packagist.org/packages/microsoft/windowsazure
 * https://azure.microsoft.com/en-us/develop/php/
 * https://docs.microsoft.com/pt-br/azure/storage/common/storage-use-emulator
 * https://github.com/Azure/azure-storage-php
*/
require_once "vendor/autoload.php";
require_once "CorrectFileName.php";
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;


class AzureUpload
{
	private $_connectionString;
	private $_azureAccountName;
	private $_azureAccountKey;
	private $_azureAccountContainer;
	private $_azureAccountUrl;
	private $_blobRestProxy;

	public function __construct()
	{
		$this->getAzureAccountName();
		$this->getAzureAccountKey();
		$this->getAzureAccountContainer();
		$this->getAzureAccountUrl();

		$this->_connectionString = "DefaultEndpointsProtocol=http;AccountName=".$this->_azureAccountName.";AccountKey=".$this->_azureAccountKey."";

		$this->_blobRestProxy = ServicesBuilder::getInstance()->createBlobService($this->_connectionString);
	}

	public function sendFile($localFileName,$name,$subdir)
	{
		$content = fopen($localFileName, "r");

		try {
			
			$fileName = $this->getName($name);
			$name = $subdir.$fileName;
		    
		    //Upload blob
		    $blb = $this->_blobRestProxy->createBlockBlob($this->_azureAccountContainer, $name, $content);

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
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here:
		    // http://msdn.microsoft.com/library/azure/dd179439.aspx
		    $code = $e->getCode();
		    $error_message = $e->getMessage();

		    $message = array(
		    	'status' => 'error',
		    	'code' => $code,
		    	'message' => $error_message
		    );
		    return $message;
		}
	}

	private function setUrlFile($name)
	{
		return array(
			'blob' => 'http://'.$this->_azureAccountName.'.blob.core.windows.net/'.$this->_azureAccountContainer.'/'.$name,
			'file' => $this->_azureAccountUrl.'/'.$this->_azureAccountContainer.'/'.$name
		);
	}

	private function getName($name)
	{
		$correct = new CorrectFileName;
		return $correct->getName($name);
	}

	private function getAzureAccountName()
	{
		$this->_azureAccountName = get_option( 'azure_storage_account_name', false );
	}

	private function getAzureAccountKey()
	{
		$this->_azureAccountKey = get_option( 'azure_storage_account_primary_access_key', false );
	}

	private function getAzureAccountContainer()
	{
		$this->_azureAccountContainer = get_option( 'default_azure_storage_account_container_name', false );
	}

	private function getAzureAccountUrl()
	{
		$this->_azureAccountUrl = get_option( 'cname', false );
	}

	public function listContainers()
	{
	    $containers = array();

	    try {

	        $listContainersOptions = new ListContainersOptions;
	        $listContainersResult = $this->_blobRestProxy->listContainers($listContainersOptions);

	        foreach ($listContainersResult->getContainers() as $container)
    		{
    			$containers[] = $container->getName();
    		}

	    } catch (ServiceException $e) {
	        $code = $e->getCode();
	        $error_message = $e->getMessage();
	        return $code.": ".$error_message.PHP_EOL;
	    }

	    return $containers;
	}

	public function createContainer($name)
	{
		$createContainerOptions = new CreateContainerOptions();
		$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
		try {
			$name = $this->getName($name);
	        // Create container.
	        $this->_blobRestProxy->createContainer($name, $createContainerOptions);
	    } catch (ServiceException $e) {
	        $code = $e->getCode();
	        $error_message = $e->getMessage();
	        return $code.": ".$error_message.PHP_EOL;
	    }
	}
}