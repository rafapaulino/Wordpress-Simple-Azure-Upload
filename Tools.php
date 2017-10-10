<?php

class Tools
{
	static public function changeUrl( $postId, $url )
	{
		$post = get_post( intval($postId) );
		if (isset($post)) {
			$url = $post->guid;
		}
		return $url;
	}

	static public function getNewSources( $attachment_id )
	{
		$newSources = array();
		$media_info = get_post_meta( intval($attachment_id), '_wp_attachment_metadata', true );

		foreach($media_info['sizes'] as $key => $value) 
		{
			$width = intval($value['width']);
			$newSources[$width] = array(
				'url' => esc_url($value['url']),
				'descriptor' => 'w',
				'value' => $width
			);
		}
		return $newSources;
	}
}