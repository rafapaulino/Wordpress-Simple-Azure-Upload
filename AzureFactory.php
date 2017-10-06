<?php

class AzureFactory
{
	public static function build()
	{
		$wp = new WPAzureOptions;
		$name = new CorrectFileName;
		
		return new Azure( $wp, $name );
	}
}