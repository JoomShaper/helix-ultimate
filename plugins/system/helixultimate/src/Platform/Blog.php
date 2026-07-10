<?php
/**
 * @package	Helix_Ultimate_Framework
 * @author	JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Path;
use HelixUltimate\Framework\Platform\Classes\Image;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

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
		$index = htmlspecialchars($input->post->get('index', '', 'STRING') ?? "");
		$gallery = $input->post->get('gallery', false, 'BOOLEAN');

		$tplRegistry = new Registry;
		$tplParams = $tplRegistry->loadString(self::getTemplate()->params);

		// User is not authorised
		if (!Factory::getApplication()->getIdentity()->authorise('core.create', 'com_media'))
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
				$image_path = $params->get('image_path', 'images');

				$contentLength 	= (int) $_SERVER['CONTENT_LENGTH'];
				$mediaHelper 	= new MediaHelper;
				$postMaxSize 	= $mediaHelper->toBytes(ini_get('post_max_size'));
				$memoryLimit 	= $mediaHelper->toBytes(ini_get('memory_limit'));

				if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit > 0 && $contentLength > $memoryLimit)) 
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
					$acceptedImageFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
					$file_ext = strtolower(File::getExt($image['name']));

					if (!in_array($file_ext, $acceptedImageFormats, true))
					{
						$report['output'] = Text::_('COM_SPPAGEBUILDER_MEDIA_MANAGER_FILE_NOT_SUPPORTED');
						die(json_encode($report));
					}

					$date = Factory::getDate();
					$folder = HTMLHelper::_('date', $date, 'Y') . '/' . HTMLHelper::_('date', $date, 'm') . '/' . HTMLHelper::_('date', $date, 'd');

					$target_folder = Path::clean(JPATH_ROOT . '/' . $image_path . '/' . $folder);

					if (!file_exists($target_folder))
					{
						try
						{
							Folder::create($target_folder, 0755);
						}
						catch (\Throwable $e)
						{
							// Fallback to native mkdir
							if (!file_exists($target_folder))
							{
								if (!@mkdir($target_folder, 0755, true))
								{
									$report['status'] = false;
									$report['output'] = Text::_('Failed to create directory.');
									echo json_encode($report);
									die();
								}
							}
						}
					}

					$safeBaseName = File::stripExt(File::makeSafe(basename(strtolower($image['name']))));
					$ext = $file_ext;
					$i = 0;

					do
					{
						$base_name  = $safeBaseName . ($i ? (string) $i : '');
						$image_name = $base_name . '.' . $ext;
						$i++;
						$dest = Path::clean(JPATH_ROOT . '/' . $image_path . '/' . $folder . '/' . $image_name);
						$src = Path::clean($image_path . '/' . $folder . '/' . $image_name, '/');
						$data_src = $src;
					}
					while (file_exists($dest));

					if (File::upload($image['tmp_name'], $dest))
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

						if (\file_exists(Path::clean(JPATH_ROOT . '/' . $image_path . '/' . $folder . '/' . $base_name . '_thumbnail.' . $ext)))
						{
							$src = Path::clean($image_path . '/' . $folder . '/' . $base_name . '_thumbnail.' . $ext, '/');
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
	
	    if (!Factory::getApplication()->getIdentity()->authorise('core.delete', 'com_media'))
	    {
	        $report['status'] = false;
	        $report['output'] = Text::_('You are not authorised to delete file.');
	        echo json_encode($report);
	        die();
	    }
	
	    $input = Factory::getApplication()->input;
	    $src = $input->post->get('src', '', 'STRING');
	    $articleId = (int) $input->get('id', 0, 'INT');

	    if (!Helper::canEditArticle($articleId))
	    {
	        $report['output'] = Text::_('JERROR_ALERTNOAUTHOR');
	        echo json_encode($report);
	        die();
	    }

	    if ($src === '' || Helper::resolveMediaPath($src) === null)
	    {
	        $report['output'] = Text::_('HELIX_ULTIMATE_DELETE_FAILED');
	        die(json_encode($report));
	    }

	    $db = Factory::getContainer()->get(DatabaseInterface::class);

	    $query = $db->getQuery(true)
	        ->select($db->quoteName('attribs'))
	        ->from($db->quoteName('#__content'))
	        ->where($db->quoteName('id') . ' = :articleId')
			->bind(':articleId', $articleId, ParameterType::INTEGER);

	    $db->setQuery($query);
	    $attribs = $db->loadResult();

	    $attribsDecoded = json_decode($attribs ?? '', true);

	    if (!\is_array($attribsDecoded))
	    {
	        $attribsDecoded = [];
	    }

	    if (($attribsDecoded['helix_ultimate_image'] ?? '') === $src)
	    {
	        $attribsDecoded['helix_ultimate_image'] = '';
	    }

	    if (!empty($attribsDecoded['helix_ultimate_gallery']))
	    {
	        $galleryImages = json_decode($attribsDecoded['helix_ultimate_gallery'], true);

	        if (\is_array($galleryImages) && \is_array($galleryImages['helix_ultimate_gallery_images'] ?? null))
	        {
	            foreach ($galleryImages['helix_ultimate_gallery_images'] as $key => $image)
	            {
	                if ($image === $src)
	                {
	                    unset($galleryImages['helix_ultimate_gallery_images'][$key]);
	                }
	            }

	            $galleryImages['helix_ultimate_gallery_images'] = array_values($galleryImages['helix_ultimate_gallery_images']);
	            $attribsDecoded['helix_ultimate_gallery'] = json_encode($galleryImages);
	        }
	    }

	    $attribsJson = json_encode($attribsDecoded);

	    $updateQuery = $db->getQuery(true)
	        ->update($db->quoteName('#__content'))
	        ->set($db->quoteName('attribs') . ' = :attribs')
	        ->where($db->quoteName('id') . ' = :articleId')
			->bind(':attribs', $attribsJson, ParameterType::STRING)
			->bind(':articleId', $articleId, ParameterType::INTEGER);

	    $db->setQuery($updateQuery);

	    if ($db->execute()) {
	        $report['status'] = true;
	    } else {
	        $report['output'] = Text::_('Database update failed');
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

		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('template', 'params')));
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = ' . $db->quote(0));
		$query->where($db->quoteName('home') . ' = ' . $db->quote('1', false));

		$db->setQuery($query);

		return $db->loadObject();

	}
}

