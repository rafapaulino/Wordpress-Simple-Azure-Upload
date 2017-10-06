<?php

class PostData
{
	private $_fields;

	public function __construct()
	{
		$this->_fields = array(
			'azure_storage_account_name',
			'azure_storage_account_primary_access_key',
			'default_azure_storage_account_container_name',
			'cname',
			'azure_storage_use_for_default_upload',
			'azure_storage_keep_local_file'
		);
	}

	public function isValid()
	{
		if ($_SERVER["REQUEST_METHOD"] == "POST" && count($_POST) > 0)
			return true;
		else
			return false;
	}

	public function insert($formData)
	{
		foreach ($formData as $key => $value)
		{
			if (in_array($key,$this->_fields)) {
				$val = stripslashes(esc_attr($value));
				update_option($key, $val);
			}
		}
	}

	public function getData()
	{
		$data = array();

		foreach ($this->_fields as $field) {
			$data[$field.'_value'] = get_option( $field, false );
		}
		return $data;
	}
}