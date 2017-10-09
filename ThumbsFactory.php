<?php

class ThumbsFactory
{
	public static function build( $wpdb, $attachment_id, $metadata )
	{
		//get azure uploader
		$azure = AzureFactory::build();

		//get data of file in wp tables
		$file = new WPFile( $wpdb, $attachment_id, $metadata );
		
		//create object for upload and update
		return new Thumbs( $azure, $file, $wpdb );
	}
}