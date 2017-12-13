<?php

class RASU_FileFactory
{
	public static function build( $wpdb, $attachment_id, $metadata )
	{
		//get azure uploader
		$azure = RASU_AzureFactory::build();

		//get data of file in wp tables
		$file = new RASU_WPFile( $wpdb, $attachment_id, $metadata );
		
		//create object for upload and update
		return new RASU_File( $azure, $file, $wpdb );
	}
}