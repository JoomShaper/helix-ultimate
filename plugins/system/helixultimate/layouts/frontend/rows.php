<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Layout\FileLayout;

$layout_path_carea  = JPATH_ROOT .'/plugins/system/helixultimate/layouts';
$layout_path_module = JPATH_ROOT .'/plugins/system/helixultimate/layouts';

$data = $displayData;
$section_sematic = $data['sematic'];

extract($displayData);
?>

<div class="row">
	<?php
		foreach ($rowColumns as $key => $column)
		{
			if (isset($componentArea) && $componentArea)
			{
				$column->sematic = 'aside';
			}
			else
			{
				$column->sematic = 'div';
			}

			$column->hasFeature = $loadFeature;
			$column->section_sematic = $section_sematic;

			if ($column->settings->column_type)
			{
				echo (new FileLayout('frontend.conponentarea', $layout_path_carea))->render($column);
			}
			else
			{
				echo (new FileLayout('frontend.modules', $layout_path_module))->render($column);
			}
		}
	?>
</div>