<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.registry.registry');

require_once __DIR__ . '/classes/image.php';

class plgAjaxHelixultimate extends JPlugin
{

  function onAjaxHelixultimate()
  {

    $input = JFactory::getApplication()->input;
    $action = $input->post->get('action', '', 'STRING');

    if($action == 'upload_image')
    {
      $this->upload_image();
    }
    elseif ($action == 'remove_image')
    {
      $this->remove_image();
    }
    elseif ($action == 'rating')
    {
      $this->articlerating();
    }
  }

  private function articlerating()
  {
    $output = array();
      $output['status'] = false;
      $output['message'] = 'Invalid Token';
      \JSession::checkToken() or die(json_encode($output));
      
      $app = \JFactory::getApplication();
      $input = $app->input;
      $article_id = (int) $input->post->get('article_id', 0, 'INT');
      $rating = (int) $input->post->get('rating', 0, 'INT');

      $userIP = $_SERVER['REMOTE_ADDR'];
      $lastip = '';
      $last_rating = $this->getRating($article_id);
     
      if(isset($last_rating->lastip) && $last_rating->lastip)
      {
        $lastip = $last_rating->lastip;
      }
      
      if($userIP == $lastip)
      {
        $output['status'] = false;
        $output['message'] = 'You already rated this Article today!';
        $output['rating_count'] = (isset($last_rating->rating_count) && $last_rating->rating_count) ? $last_rating->rating_count : 0;
      }
      else
      {
        $newRatings = $this->addRating($article_id, $rating, $userIP);

        $output['status'] = true;
        $output['message'] = 'Thank You!';

        $rating = round($newRatings->rating_sum/$newRatings->rating_count);
        $output['rating_count'] = $newRatings->rating_count;

        $output['ratings'] = '';
        $j = 0;
        for($i = $rating; $i < 5; $i++)
        {
          $output['ratings'] .= '<span class="rating-star" data-number="'.(5-$j).'"></span>';
          $j = $j+1;
        }
        for ($i = 0; $i < $rating; $i++)
        {
          $output['ratings'] .= '<span class="rating-star active" data-number="'.($rating - $i).'"></span>';
        }
      }

      die(json_encode($output));
  }

  private function addRating($id, $rating, $ip)
  {
    $db = \JFactory::getDbo();
    $lastRating = $this->getRating($id);

    $userRating = new stdClass();
    $userRating->content_id = $id;
    $userRating->lastip = $ip;

    if(isset($lastRating->rating_count) && $lastRating->rating_count)
    {
      $userRating->rating_sum = ($lastRating->rating_sum + $rating);
      $userRating->rating_count = ($lastRating->rating_count + 1);
      $db->updateObject('#__content_rating', $userRating, 'content_id');
    }
    else
    {
      $userRating->rating_sum = $rating;
      $userRating->rating_count = 1;
      $db->insertObject('#__content_rating', $userRating);
    }

    return $this->getRating($id);
  }

  private function getRating($id)
  {
    $db = \JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*')
    ->from($db->quoteName('#__content_rating'))
    ->where($db->quoteName('content_id') . ' = ' . (int) $id);

    $db->setQuery($query);
    $data = $db->loadObject();

    return $data;
  }

  //Get template name
  private static function getTemplate() {

    $db = \JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('template', 'params')));
    $query->from($db->quoteName('#__template_styles'));
    $query->where($db->quoteName('client_id') . ' = '. $db->quote(0));
    $query->where($db->quoteName('home') . ' = '. $db->quote(1));
    $db->setQuery($query);

    return $db->loadObject();
  }

  // Upload File
  private function upload_image() {
    $input = \JFactory::getApplication()->input;
    $image = $input->files->get('image');
    $imageonly = $input->post->get('imageonly', false, 'BOOLEAN');

    $tplRegistry = new \JRegistry();
    $tplParams = $tplRegistry->loadString(self::getTemplate()->params);

    $report = array();

    // User is not authorised
    if (!\JFactory::getUser()->authorise('core.create', 'com_media'))
    {
      $report['status'] = false;
      $report['output'] = \JText::_('You are not authorised to upload file.');
      echo json_encode($report);
      die;
    }

    if(count($image)) {

      if ($image['error'] == UPLOAD_ERR_OK) {

        $error = false;

        $params = \JComponentHelper::getParams('com_media');

        // Total length of post back data in bytes.
        $contentLength = (int) $_SERVER['CONTENT_LENGTH'];

        // Instantiate the media helper
        $mediaHelper = new \JHelperMedia;

        // Maximum allowed size of post back data in MB.
        $postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

        // Maximum allowed size of script execution in MB.
        $memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

        // Check for the total size of post back data.
        if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit != -1 && $contentLength > $memoryLimit)) {
          $report['status'] = false;
          $report['output'] = \JText::_('Total size of upload exceeds the limit.');
          $error = true;
          echo json_encode($report);
          die;
        }

        $uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
        $uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

        if (($image['error'] == 1) || ($uploadMaxSize > 0 && $image['size'] > $uploadMaxSize) || ($uploadMaxFileSize > 0 && $image['size'] > $uploadMaxFileSize))
        {
          $report['status'] = false;
          $report['output'] = \JText::_('This file is too large to upload.');
          $error = true;
        }

        // Upload if no error found
        if(!$error) {
          // Organised folder structure
          $date = \JFactory::getDate();
          $folder = \JHtml::_('date', $date, 'Y') . '/' . \JHtml::_('date', $date, 'm') . '/' . \JHtml::_('date', $date, 'd');

          if(!file_exists( \JPATH_ROOT . '/images/' . $folder )) {
            \JFolder::create(\JPATH_ROOT . '/images/' . $folder, 0755);
          }

          $name = $image['name'];
          $path = $image['tmp_name'];

          // Do no override existing file
          $file = pathinfo($name);
          $i = 0;
          do {
            $base_name  = $file['filename'] . ($i ? "$i" : "");
            $ext        = $file['extension'];
            $image_name = $base_name . "." . $ext;
            $i++;
            $dest = \JPATH_ROOT . '/images/' . $folder . '/' . $image_name;
            $src = 'images/' . $folder . '/'  . $image_name;
            $data_src = 'images/' . $folder . '/'  . $image_name;
          } while(file_exists($dest));
          // End Do not override

          if(\JFile::upload($path, $dest)) {

            $image_quality = $tplParams->get('image_crop_quality', '100');

            if($tplParams->get('image_small', 0)) {
              $sizes['small'] = explode('x', strtolower($tplParams->get('image_small_size', '100X100')));
            }
            if($tplParams->get('image_thumbnail', 1)) {
              $sizes['thumbnail'] = explode('x', strtolower($tplParams->get('image_thumbnail_size', '200X200')));
            }
            if($tplParams->get('image_medium', 0)) {
              $sizes['medium'] = explode('x', strtolower($tplParams->get('image_medium_size', '300X300')));
            }
            if($tplParams->get('image_large', 0)) {
              $sizes['large']  = explode('x', strtolower($tplParams->get('image_large_size', '600X600')));
            }

            if(count($sizes)) {
              $sources = HelixUltimateImage::createThumbs($dest, $sizes, $folder, $base_name, $ext, $image_quality);
            }

            if(file_exists(\JPATH_ROOT . '/images/' . $folder . '/' . $base_name . '_thumbnail.' . $ext)) {
              $src = 'images/' . $folder . '/'  . $base_name . '_thumbnail.' . $ext;
            }

            $report['status'] = true;

            if($imageonly) {
              $report['output'] = '<img src="'. \JURI::root(true) . '/' . $src . '" data-src="'. $data_src .'" alt="">';
            } else {
              $report['output'] = '<li data-src="'. $data_src .'"><a href="#" class="btn btn-mini btn-danger btn-remove-image">Delete</a><img src="'. \JURI::root(true) . '/' . $src . '" alt=""></li>';
            }
          }
        }
      }
    } else {
      $report['status'] = false;
      $report['output'] = \JText::_('Upload Failed!');
    }

    echo json_encode($report);

    die;
  }

  // Delete File
  private function remove_image()
  {
    $report = array();

    if (!JFactory::getUser()->authorise('core.delete', 'com_media'))
    {
      $report['status'] = false;
      $report['output'] = JText::_('You are not authorised to delete file.');
      echo json_encode($report);
      die;
    }

    $input = JFactory::getApplication()->input;
    $src = $input->post->get('src', '', 'STRING');

    $path = JPATH_ROOT . '/' . $src;

    if(file_exists($path))
    {

      if(JFile::delete($path))
      {

        $basename = basename($src);
        $small = JPATH_ROOT . '/' . dirname($src) . '/' . JFile::stripExt($basename) . '_small.' . JFile::getExt($basename);
        $thumbnail = JPATH_ROOT . '/' . dirname($src) . '/' . JFile::stripExt($basename) . '_thumbnail.' . JFile::getExt($basename);
        $medium = JPATH_ROOT . '/' . dirname($src) . '/' . JFile::stripExt($basename) . '_medium.' . JFile::getExt($basename);
        $large = JPATH_ROOT . '/' . dirname($src) . '/' . JFile::stripExt($basename) . '_large.' . JFile::getExt($basename);

        if(file_exists($small))
        {
          JFile::delete($small);
        }

        if(file_exists($thumbnail))
        {
          JFile::delete($thumbnail);
        }

        if(file_exists($medium))
        {
          JFile::delete($medium);
        }

        if(file_exists($large))
        {
          JFile::delete($large);
        }

        $report['status'] = true;
      } else {
        $report['status'] = false;
        $report['output'] = JText::_('Delete failed');
      }
    }
    else 
    {
      $report['status'] = true;
    }

    echo json_encode($report);

    die;
  }

}
