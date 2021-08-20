<?php
/**
 * @package	Helix_Ultimate_Framework
 * @author	JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Uri\Uri;
use HelixUltimate\Framework\Platform\Classes\Image;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Blog class.
 *
 * @since	1.0.0
 */
class Blog
{
	public static function upload_image()
	{

		$report = array();
		$report['status'] = false;
		$report['output'] = 'Invalid Token';
		Session::checkToken() or die(json_encode($report));

		$input = Factory::getApplication()->input;
		$image = $input->files->get('image');
		$index = htmlspecialchars($input->post->get('index', '', 'STRING'));
		$gallery = $input->post->get('gallery', false, 'BOOLEAN');

		$tplRegistry = new Registry;
		$tplParams = $tplRegistry->loadString(self::getTemplate()->params);

		// User is not authorised
		if (!Factory::getUser()->authorise('core.create', 'com_media'))
		{
			$report['status'] = false;
			$report['output'] = Text::_('You are not authorised to upload file.');
			echo json_encode($report);
			die();
		}

		if (!empty($image))
		{
			if ($image['error'] === UPLOAD_ERR_OK)
			{
				$error = false;

				$params = ComponentHelper::getParams('com_media');

				$contentLength 	= (int) $_SERVER['CONTENT_LENGTH'];
				$mediaHelper 	= new \JHelperMedia;
				$postMaxSize 	= $mediaHelper->toBytes(ini_get('post_max_size'));
				$memoryLimit 	= $mediaHelper->toBytes(ini_get('memory_limit'));

				if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit !== -1 && $contentLength > $memoryLimit))
				{
					$report['status'] = false;
					$report['output'] = Text::_('Total size of upload exceeds the limit.');
					$error = true;
					die(json_encode($report));
				}

				$uploadMaxSize 		= $params->get('upload_maxsize', 0) * 1024 * 1024;
				$uploadMaxFileSize 	= $mediaHelper->toBytes(ini_get('upload_max_filesize'));

				if (($image['error'] === 1) || ($uploadMaxSize > 0 && $image['size'] > $uploadMaxSize) || ($uploadMaxFileSize > 0 && $image['size'] > $uploadMaxFileSize))
				{
					$report['status'] = false;
					$report['output'] = Text::_('This file is too large to upload.');
					$error = true;
				}

				if (!$error)
				{
					$date = Factory::getDate();
					$folder = HTMLHelper::_('date', $date, 'Y') . '/' . \JHtml::_('date', $date, 'm') . '/' . HTMLHelper::_('date', $date, 'd');

					if (!file_exists(JPATH_ROOT . '/images/' . $folder))
					{
						Folder::create(\JPATH_ROOT . '/images/' . $folder, 0755);
					}

					$name = $image['name'];
					$path = $image['tmp_name'];

					// Do no override existing file
					$file = pathinfo($name);
					$i = 0;

					do
					{
						$base_name  = $file['filename'] . ($i ? "$i" : "");
						$ext        = $file['extension'];
						$image_name = $base_name . "." . $ext;
						$i++;
						$dest = JPATH_ROOT . '/images/' . $folder . '/' . $image_name;
						$src = 'images/' . $folder . '/' . $image_name;
						$data_src = 'images/' . $folder . '/' . $image_name;
					}
					while (file_exists($dest));

					if (File::upload($path, $dest))
					{
						$image_quality = $tplParams->get('image_crop_quality', '100');

						if ($tplParams->get('image_small', 0))
						{
							$sizes['small'] = explode('x', strtolower($tplParams->get('image_small_size', '100X100')));
						}

						if ($tplParams->get('image_thumbnail', 1))
						{
							$sizes['thumbnail'] = explode('x', strtolower($tplParams->get('image_thumbnail_size', '200X200')));
						}

						if ($tplParams->get('image_medium', 0))
						{
							$sizes['medium'] = explode('x', strtolower($tplParams->get('image_medium_size', '300X300')));
						}

						if ($tplParams->get('image_large', 0))
						{
							$sizes['large']  = explode('x', strtolower($tplParams->get('image_large_size', '600X600')));
						}

						if (!empty($sizes))
						{
							$sources = Image::createThumbs($dest, $sizes, $folder, $base_name, $ext, $image_quality);
						}

						if (File::exists(JPATH_ROOT . '/images/' . $folder . '/' . $base_name . '_thumbnail.' . $ext))
						{
							$src = 'images/' . $folder . '/' . $base_name . '_thumbnail.' . $ext;
						}

						$report['status'] = true;
						$report['index'] = $index;

						if ($gallery)
						{
							$report['output'] = '<a href="#" class="btn btn-mini btn-danger btn-hu-remove-gallery-image"><span class="fas fa-times" aria-hidden="true"></span></a><img src="' . URI::root(true) . '/' . $src . '" alt="">';
							$report['data_src'] = $data_src;
						}
						else
						{
							$report['output'] = '<img src="' . Uri::root(true) . '/' . $src . '" data-src="' . $data_src . '" alt="">';
						}
					}
				}
			}
		}
		else
		{
			$report['status'] = false;
			$report['output'] = Text::_('Upload Failed!');
		}

		die(json_encode($report));
	}

	/**
	 * Delete file.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public static function remove_image()
	{
		$report = array();
		$report['status'] = false;
		$report['output'] = 'Invalid Token';
		Session::checkToken() or die(json_encode($report));

		if (!Factory::getUser()->authorise('core.delete', 'com_media'))
		{
			$report['status'] = false;
			$report['output'] = Text::_('You are not authorised to delete file.');
			echo json_encode($report);
			die();
		}

		$input = Factory::getApplication()->input;
		$src = $input->post->get('src', '', 'STRING');

		$path = JPATH_ROOT . '/' . $src;

		if (File::exists($path))
		{
			if (File::delete($path))
			{
				$basename 	= basename($src);
				$small 		= JPATH_ROOT . '/' . dirname($src) . '/' . File::stripExt($basename) . '_small.' . File::getExt($basename);
				$thumbnail 	= JPATH_ROOT . '/' . dirname($src) . '/' . File::stripExt($basename) . '_thumbnail.' . File::getExt($basename);
				$medium 	= JPATH_ROOT . '/' . dirname($src) . '/' . File::stripExt($basename) . '_medium.' . File::getExt($basename);
				$large 		= JPATH_ROOT . '/' . dirname($src) . '/' . File::stripExt($basename) . '_large.' . File::getExt($basename);

				if (File::exists($small))
				{
					File::delete($small);
				}

				if (File::exists($thumbnail))
				{
					File::delete($thumbnail);
				}

				if (File::exists($medium))
				{
					File::delete($medium);
				}

				if (File::exists($large))
				{
					File::delete($large);
				}

				$report['status'] = true;
			}
			else
			{
				$report['status'] = false;
				$report['output'] = Text::_('Delete failed');
			}
		}
		else
		{
			$report['status'] = true;
		}

		die(json_encode($report));
	}

	/**
	 * Get template.
	 *
	 * @return	object	Template information.
	 * @since	1.0.0
	 */
	private static function getTemplate()
	{

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('template', 'params')));
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = ' . $db->quote(0));
		$query->where($db->quoteName('home') . ' = ' . $db->quote(1));

		$db->setQuery($query);

		return $db->loadObject();

	}
}

