<?php

class RASU_PostData
{
	private $_fields;

	public function __construct()
	{
		$this->_fields = array(
			'rasu_account',
			'rasu_key',
			'rasu_container',
			'rasu_cname'
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