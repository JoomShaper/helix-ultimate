<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

$layout_path_carea  = JPATH_ROOT .'/plugins/system/helixultimate/layouts';
$layout_path_module = JPATH_ROOT .'/plugins/system/helixultimate/layouts';

$data = $displayData;

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

			if ($column->settings->column_type)
			{
				echo (new JLayoutFile('frontend.conponentarea', $layout_path_carea))->render($column);
			}
			else
			{
				echo (new JLayoutFile('frontend.modules', $layout_path_module))->render($column);
			}
		}
	?>
</div>