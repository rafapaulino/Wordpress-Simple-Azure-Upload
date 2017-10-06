<?php

class WPAzureOptions
{
	private $_account;
	private $_key;
	private $_container;
	private $_cname;

	public function __construct() 
	{
		$this->setAccount();
		$this->setKey();
		$this->setContainer();
		$this->setCname();
	}

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->_account;
    }

    /**
     * @param mixed $_account
     *
     * @return self
     */
    private function setAccount()
    {
        $this->_account = get_option( 'azure_storage_account_name', false );

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * @param mixed $_key
     *
     * @return self
     */
    private function setKey()
    {
        $this->_key = get_option( 'azure_storage_account_primary_access_key', false );

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * @param mixed $_container
     *
     * @return self
     */
    private function setContainer()
    {
        $this->_container = get_option( 'default_azure_storage_account_container_name', false );

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCname()
    {
        return $this->_cname;
    }

    /**
     * @param mixed $_cname
     *
     * @return self
     */
    private function setCname()
    {
        $this->_cname = get_option( 'cname', false );

        return $this;
    }
}