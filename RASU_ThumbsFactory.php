<?php

class RASU_ThumbsFactory
{
	public static function build( $wpdb, $attachment_id, $metadata )
	{
		//get azure uploader
		$azure = RASU_AzureFactory::build();

		//get data of file in wp tables
		$file = new RASU_WPFile( $wpdb, $attachment_id, $metadata );
		
		//create object for upload and update
		return new RASU_Thumbs( $azure, $file, $wpdb );
	}
}