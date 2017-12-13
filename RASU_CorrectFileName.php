<?php

class RASU_CorrectFileName
{
	private $_characters;

	public function __construct()
	{
		$this->_characters = array(
			'á' => 'a',
			'à' => 'a',
			'ã' => 'a',
			'â' => 'a',
			'é' => 'e',
			'ê' => 'e',
			'í' => 'i',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ú' => 'u',
			'ü' => 'u',
			'ç' => 'c',
			'Á' => 'A',
			'À' => 'A',
			'Ã' => 'A',
			'Â' => 'A',
			'É' => 'E',
			'Ê' => 'E',
			'Í' => 'I',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ú' => 'U',
			'Ü' => 'U',
			'Ç' => 'C',
			'Š' => 'S', 
			'š' => 's', 
			'Ž' => 'Z', 
			'ž' => 'z', 
			'À' => 'A', 
			'Á' => 'A', 
			'Â' => 'A', 
			'Ã' => 'A', 
			'Ä' => 'A', 
			'Å' => 'A', 
			'Æ' => 'A', 
			'Ç' => 'C', 
			'È' => 'E', 
			'Ë' => 'E', 
			'Ì' => 'I', 
			'Í' => 'I', 
			'Î' => 'I', 
			'Ï' => 'I', 
			'Ñ' => 'N', 
			'Ò' => 'O', 
			'Ó' => 'O', 
			'Ô' => 'O', 
			'Õ' => 'O', 
			'Ö' => 'O', 
			'Ø' => 'O', 
			'Ù' => 'U',
            'Ú' => 'U', 
            'Û' => 'U', 
            'Ü' => 'U', 
            'Ý' => 'Y', 
            'Þ' => 'B', 
            'ß' => 'Ss', 
            'à' => 'a', 
            'á' => 'a', 
            'â' => 'a', 
            'ã' => 'a', 
            'ä' => 'a', 
            'å' => 'a', 
            'æ' => 'a', 
            'ç' => 'c',
            'è' => 'e', 
            'é' => 'e', 
            'ê' => 'e', 
            'ë' => 'e', 
            'ì' => 'i', 
            'í' => 'i', 
            'î' => 'i', 
            'ï' => 'i', 
            'ð' => 'o', 
            'ñ' => 'n', 
            'ò' => 'o', 
            'ó' => 'o', 
            'ô' => 'o', 
            'õ' => 'o',
            'ö' => 'o', 
            'ø' => 'o', 
            'ù' => 'u', 
            'ú' => 'u', 
            'û' => 'u', 
            'ý' => 'y', 
            'þ' => 'b', 
            'ÿ' => 'y'
		);
	}

	public function getName($str) 
	{
		$oldstr = $str;
		$ext = explode(".",$str);

		if (count($ext) > 1) {
			$str = $this->replace($ext[0]);
			$ext = strtolower($ext[count($ext)-1]);
			
			//return extension with name
			$str = 'file-'.date("YmdHis").'-'.trim($str).'.'.$ext;
		} else {
			$str = $this->replace($str);
		}    	
		return $str;
	}

	protected function replace($str)
	{
		$str = trim($str);
		//remove accents
		$str = strtr($str, $this->_characters);
		$pattern = '/[^0-9a-zA-Z- \s]/';
		$replacement = '';
		//remove others characters
		$name = preg_replace($pattern, $replacement, $str);
		//$name = $str;
		$name = trim(strtolower($name));
		//replace spaces
		$name = preg_replace('/[-]+/', '-', $name);
		//$name = preg_replace('!\s+!', ' ', $name);
		//$name = preg_replace("/[^a-z0-9-\s]/i", "-", $name);
		//limit name to 50 characters
		return trim(substr($name,0,50));
	}
}