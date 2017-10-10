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

	static public function getNewSources( $attachment_id, $sources )
	{
		$newSources = array();
		
		foreach ($sources as $key => $value) {
			$width = intval($value['width']);
			$name = self::getFileName( $value['url'] );
			$url = self::getUrl( $name, $attachment_id, $value['url'] );
			
			$newSources[$key] = array(
				'url' => $url,
				'descriptor' => 'w',
				'value' => $width
			);
		}

		return $newSources;
	}

	protected function getFileName( $url )
	{
		$parts = explode('/',$url);
		return $parts[count($parts)-1];
	}

	protected function getUrl( $name, $attachment_id, $url )
	{
		$media_info = get_post_meta( intval($attachment_id), '_wp_attachment_metadata', true );

		if ( count($media_info) > 0 ) {
			$url = $media_info['file'];

			foreach($media_info['sizes'] as $key => $value) 
			{
				if ($value['file'] == $name) {
					$url = esc_url($value['url']);
					break;
				}
			}
		}

		return $url;
	}
}