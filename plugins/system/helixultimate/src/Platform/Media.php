<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Platform;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Media helper class.
 *
 * @since 1.0.0
 */
class Media
{
	/**
	 * Get Folders.
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public static function getFolders()
	{
		$media = array();
		$media['status'] = false;
		$media['output'] = Text::_('JINVALID_TOKEN');

		Session::checkToken() or die(json_encode($media));

		$input 	= Factory::getApplication()->input;
		$path 	= $input->post->get('path', '/images', 'PATH');

		$images 	= Folder::files(JPATH_ROOT . $path, '.png|.jpg|.jpeg|.gif|.svg|.ico', false, true);
		$folders 	= Folder::folders(JPATH_ROOT . $path, '.', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', '_spmedia_thumbs'));

		$crumbs = explode('/', ltrim($path, '/'));
		$crumb_url = '';

		$breadcrumb = '<ul class="hu-media-breadcrumb">';

		foreach ($crumbs as $key => $crumb)
		{
			$crumb_url .= '/' . $crumb;

			if (count($crumbs) === ($key + 1))
			{
				$breadcrumb .= '<li class="hu-media-breadcrumb-item active" data-path="' . $crumb_url . '">' . preg_replace('/[-_]+/', ' ', $crumb) . '</li>';
			}
			else
			{
				$breadcrumb .= '<li class="hu-media-breadcrumb-item" data-path="' . $crumb_url . '"><a href="#" data-path="' . $crumb_url . '">' . preg_replace('/[-_]+/', ' ', $crumb) . '</a></li>';
			}
		}

		$breadcrumb .= '</ul>';

		$media['breadcrumbs'] = $breadcrumb;
		$media['path'] = $path;
		$files = array();

		$media['images'] = $images;
		$media['folders'] = $folders;

		$output = '<div id="hu-media-manager">';
		$output .= '<ul class="hu-media clearfix">';

		if (!empty($folders))
		{
			foreach ($folders as $folder)
			{
				$files[$folder] = array(
					'type' => 'folder',
					'folder' => $path . '/' . $folder,
					'name' => $folder
				);
			}
		}

		if (!empty($images))
		{
			foreach ($images as $image)
			{
				$image 			= str_replace('\\', '/', $image);
				$root_path 		= str_replace('\\', '/', JPATH_ROOT);
				$path 			= str_replace($root_path . '/', '', $image);

				$files[basename($path)] = array(
					'type' => 'image',
					'path' => $path,
					'name' => basename($path),
					'preview' => Uri::root() . $path
				);
			}
		}

		if (!empty($files))
		{
			ksort($files);

			foreach ($files as $key => $file)
			{
				if ($file['type'] === 'folder')
				{
					$output .= '<li class="hu-media-folder" data-path="' . $file['folder'] . '">';
					$output .= '<div class="hu-media-thumb">';
					$output .= '<svg width="160" height="160" viewBox="0 0 160 160"><g fill="none" fill-rule="evenodd"><path d="M77.955 53h50.04A3.002 3.002 0 0 1 131 56.007v58.988a4.008 4.008 0 0 1-4.003 4.005H39.003A4.002 4.002 0 0 1 35 114.995V45.99c0-2.206 1.79-3.99 3.997-3.99h26.002c1.666 0 3.667 1.166 4.49 2.605l3.341 5.848s1.281 2.544 5.12 2.544l.005.003z" fill="#71B9F4"></path><path d="M77.955 52h50.04A3.002 3.002 0 0 1 131 55.007v58.988a4.008 4.008 0 0 1-4.003 4.005H39.003A4.002 4.002 0 0 1 35 113.995V44.99c0-2.206 1.79-3.99 3.997-3.99h26.002c1.666 0 3.667 1.166 4.49 2.605l3.341 5.848s1.281 2.544 5.12 2.544l.005.003z" fill="#92CEFF"></path></g></svg>';
					$output .= '</div>';
					$output .= '<span class="hu-media-select"><span class="fas fa-check" aria-hidden="true"></span></span>';
					$output .= '<div class="hu-media-label">' . $file['name'] . '</div>';
					$output .= '</li>';
				}
				else
				{
					$output .= '<li class="hu-media-image" data-path="' . $file['path'] . '" data-preview="' . $file['preview'] . '">';
					$output .= '<div class="hu-media-thumb">';
					$output .= '<img src="' . $file['preview'] . '" alt="">';
					$output .= '</div>';
					$output .= '<span class="hu-media-select"><span class="fas fa-check" aria-hidden="true"></span></span>';
					$output .= '<div class="hu-media-label">' . $file['name'] . '</div>';
					$output .= '</li>';
				}
			}
		}
		else
		{
			// $output .= '<li class="hu-media-folder-empty"></li>';
		}

		$output .= '</ul>';
		$output .= '</div>';

		$media['status'] = true;
		$media['output'] = $output;

		die(json_encode($media));
	}

	public static function deleteMedia()
	{
		$output = array();
		$output['status'] = false;
		$output['message'] = Text::_('JINVALID_TOKEN');

		Session::checkToken() or die(json_encode($output));

		$input 	= Factory::getApplication()->input;
		$path 	= $input->post->get('path', '/images', 'PATH');
		$type	= $input->post->get('type', 'file', 'STRING');

		if ($type === 'file')
		{
			if (File::delete(JPATH_ROOT . '/' . $path))
			{
				$output['status'] = true;
			}
			else
			{
				$output['message'] = "Unable to delete file";
				$output['status'] = false;
			}
		}
		else
		{
			if (Folder::delete(JPATH_ROOT . '/' . $path))
			{
				$output['status'] = true;
			}
			else
			{
				$output['message'] = "Unable to delete folder";
				$output['status'] = false;
			}
		}

		die(json_encode($output));
	}

	public static function createFolder()
	{
		$output = array();
		$output['status'] = false;
		$output['message'] = Text::_('JINVALID_TOKEN');

		Session::checkToken() or die(json_encode($output));

		$input 	= Factory::getApplication()->input;
		$path 	= $input->post->get('path', '/images', 'PATH');
		$folder_name 	= $input->post->get('folder_name', '', 'STRING');

		$absolute_path = JPATH_ROOT . $path . '/' . preg_replace('/\s+/', '-', $folder_name);

		if (Folder::exists($absolute_path))
		{
			$output['message'] = "Folder is already exists.";
			$output['status'] = false;
		}
		else
		{
			if (Folder::create($absolute_path, 0755))
			{
				$output['output'] = self::getFolders();
				$output['status'] = true;
			}
			else
			{
				$output['message'] = "Unable to create folder.";
				$output['status'] = false;
			}
		}

		die(json_encode($output));
	}

	public static function uploadMedia()
	{
		$user   = Factory::getUser();
		$input 	= Factory::getApplication()->input;
		$dir 	= $input->post->get('path', '/images', 'PATH');
		$index 	= $input->post->get('index', '', 'STRING');
		$file 	= $input->files->get('file');
		$authorised = $user->authorise('core.edit', 'com_templates');

		$report = array();
		$report['status'] = false;
		$report['message'] = Text::_('JINVALID_TOKEN');
		$report['index'] = $index;

		Session::checkToken() or die(json_encode($report));

		if ($authorised !== true)
		{
			$report['status'] = false;
			$report['message'] = Text::_('JERROR_ALERTNOAUTHOR');
			echo json_encode($report);
			die();
		}

		if (!empty($file))
		{
			if ($file['error'] === UPLOAD_ERR_OK)
			{
				$error = false;
				$params 		= ComponentHelper::getParams('com_media');
				$contentLength 	= (int) $_SERVER['CONTENT_LENGTH'];

				$mediaHelper = new \JHelperMedia;
				$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));
				$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

				// Check for the total size of post back data.
				if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit !== -1 && $contentLength > $memoryLimit))
				{
					$report['status'] = false;
					$report['message'] = Text::_('COM_SPPAGEBUILDER_MEDIA_MANAGER_MEDIA_TOTAL_SIZE_EXCEEDS');
					$error = true;
					echo json_encode($report);
					die();
				}

				$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
				$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

				if (($file['error'] === 1) || ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize) || ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
				{
					$report['status'] = false;
					$report['message'] = Text::_('COM_SPPAGEBUILDER_MEDIA_MANAGER_MEDIA_LARGE');
					$error = true;
				}

				// File formats
				$accepted_file_formats = array('jpg', 'jpeg', 'png', 'gif', 'svg', 'ico');

				// Upload if no error found
				if (!$error)
				{
					$file_ext = strtolower(File::getExt($file['name']));

					if (in_array($file_ext, $accepted_file_formats))
					{
						$name = $file['name'];
						$source_path = $file['tmp_name'];
						$folder = ltrim($dir, '/');

						// Do no override existing file
						$media_file = preg_replace('#\s+#', "-", \JFile::makeSafe(basename(strtolower($name))));
						$i = 0;

						do
						{
							$base_name  = \JFile::stripExt($media_file) . ($i ? "$i" : "");
							$ext        = \JFile::getExt($media_file);
							$media_name = $base_name . '.' . $ext;
							$i++;
							$dest       = \JPATH_ROOT . '/' . $folder . '/' . $media_name;
							$src        = $folder . '/' . $media_name;
						}
						while (file_exists($dest));

						// End Do not override
						if (File::upload($source_path, $dest, false, true))
						{
							$report['src'] = Uri::root(true) . '/' . $src;
							$report['status'] = true;
							$report['title'] = $media_name;
							$report['path'] = $src;

							$output = '<div class="hu-media-thumb">';
							$output .= '<img src="' . $report['src'] . '" alt="">';
							$output .= '</div>';
							$output .= '<span class="hu-media-select"><span class="fas fa-check" aria-hidden="true"></span></span>';
							$output .= '<div class="hu-media-label">' . $report['title'] . '</div>';

							$report['output'] = $output;
						}
						else
						{
							$report['status'] = false;
							$report['message'] = Text::_('COM_SPPAGEBUILDER_MEDIA_MANAGER_UPLOAD_FAILED');
						}
					}
					else
					{
						$report['status'] = false;
						$report['message'] = Text::_('COM_SPPAGEBUILDER_MEDIA_MANAGER_FILE_NOT_SUPPORTED');
					}
				}
			}
		}
		else
		{
			$report['status'] = false;
			$report['message'] = Text::_('File not found');
		}

		die(json_encode($report));
	}
}
