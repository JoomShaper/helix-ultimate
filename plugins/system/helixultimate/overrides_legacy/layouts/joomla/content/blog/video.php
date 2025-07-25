<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('JPATH_BASE') or die();

extract($displayData);

if (isset($attribs->helix_ultimate_video) && $attribs->helix_ultimate_video) {
	$video_url = trim($attribs->helix_ultimate_video);
	$video_src = '';
	$embed_code = '';

	$video = parse_url($video_url);
	$host = isset($video['host']) ? strtolower($video['host']) : '';
	$ext = strtolower(pathinfo($video_url, PATHINFO_EXTENSION));

	switch ($host) {
		case 'youtu.be':
			$video_id = trim($video['path'], '/');
			$video_src = '//www.youtube.com/embed/' . $video_id;
			break;

		case 'www.youtube.com':
		case 'youtube.com':
		case 'www.youtube-nocookie.com':
		case 'youtube-nocookie.com':
			if (strpos($video['path'], '/embed/') === 0) {
				// Already an embed URL
				$video_src = '//www.youtube.com' . $video['path'];
				if (!empty($video['query'])) {
					$video_src .= '?' . $video['query'];
				}
			} else {
				// Handle standard YouTube watch URL
				parse_str($video['query'], $query);
				if (isset($query['v'])) {
					$video_id = $query['v'];
					$video_src = '//www.youtube.com/embed/' . $video_id;
				}
			}
			break;

		case 'vimeo.com':
		case 'www.vimeo.com':
		case 'player.vimeo.com':
			$path = trim($video['path'], '/');
			if (strpos($path, 'video/') === 0) {
				$path = substr($path, 6);
			}
			$video_id = explode('?', $path)[0];
			$video_src = '//player.vimeo.com/video/' . $video_id;
			break;

		case 'dailymotion.com':
		case 'www.dailymotion.com':
			$path = trim($video['path'], '/');
			if (strpos($path, 'video/') === 0) {
				$path = substr($path, 6);
			}
			$video_id = explode('_', $path)[0];
			$video_src = '//www.dailymotion.com/embed/video/' . $video_id;
			break;

		case 'dai.ly':
			$path = trim($video['path'], '/');
			if ($path) {
				$video_id = $path;
				$video_src = '//www.dailymotion.com/embed/video/' . $video_id;
			}
			break;

		default:
			if ($ext === 'mp4') {
				$embed_code = '
					<video controls width="100%">
						<source src="' . htmlspecialchars($video_url, ENT_QUOTES) . '" type="video/mp4">
						Your browser does not support the video tag.
					</video>';
			} else {
				// Treat as iframe-embeddable (e.g., Facebook embeds)
				$embed_code = '
					<iframe src="' . htmlspecialchars($video_url, ENT_QUOTES) . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="width:100%; height:400px;"></iframe>';
			}
			break;
	}
	// If we have a video source, create the embed code
	if (!$embed_code && $video_src) {
		$embed_code = '<iframe src="' . $video_src . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="width:100%; height:400px;"></iframe>';
	}

	// Final Output
	if ($embed_code) {
		echo '<div class="article-featured-video">';
		echo $embed_code;
		echo '</div>';
	}
}
