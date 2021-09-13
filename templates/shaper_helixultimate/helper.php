<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class TplShaperHelixultimateHelper
{
    public static function getAjax()
    {
        self::articlerating();
    }

    public static function articlerating()
    {
        $output = array();
        $output['status'] = false;
        $output['message'] = 'Invalid Token';
        Session::checkToken() or die(json_encode($output));

        $app = Factory::getApplication();
        $input = $app->input;
        $article_id = (int) $input->post->get('article_id', 0, 'INT');
        $rating = (int) $input->post->get('rating', 0, 'INT');

        $userIP = $_SERVER['REMOTE_ADDR'];
        $lastip = '';
        $last_rating = self::getRating($article_id);

        if(isset($last_rating->lastip) && $last_rating->lastip)
        {
            $lastip = $last_rating->lastip;
        }

        if($userIP == $lastip)
        {
            $output['status'] = false;
            $output['message'] = Text::_('HELIX_ALREADY_RATED');
            $output['rating_count'] = (isset($last_rating->rating_count) && $last_rating->rating_count) ? $last_rating->rating_count : 0;
        }
        else
        {
            $newRatings = self::addRating($article_id, $rating, $userIP);

            $output['status'] = true;
            $output['message'] = Text::_('HELIX_THANK_YOU');

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

    private static function addRating($id, $rating, $ip)
    {
        $db = Factory::getDbo();
        $lastRating = self::getRating($id);

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

        return self::getRating($id);
    }

    private static function getRating($id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName('#__content_rating'))
            ->where($db->quoteName('content_id') . ' = ' . (int) $id);

        $db->setQuery($query);
        $data = $db->loadObject();

        return $data;
    }
}