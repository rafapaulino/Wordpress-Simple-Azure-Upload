<?php

class RASU_AzureFactory
{
	public static function build()
	{
		$wp = new RASU_WPAzureOptions;
		$name = new RASU_CorrectFileName;
		
		return new RASU_Azure( $wp, $name );
	}
}