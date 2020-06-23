<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

extract($displayData);


$options = $field->options;

$fixedWidth = $field->getAttribute('fixedwidth');
$fixedWidth = isset($fixedWidth) && ($fixedWidth === 'true' || $fixedWidth === 'on');

$value = $field->value;
$value = empty($value) ? $field->getAttribute('default', '') : $value;

$textRule = $field->getAttribute('textrule', 'text');

?>

<?php if (!empty($options)): ?>
	<div class="hu-inline-group <?php echo $fixedWidth ? 'fixed-width': ''; ?>">
		<div class="hu-btn-group">
			<?php foreach ($options as $option): ?>
				<?php
					$optionClass = $option->class;
					$iconClass = '';
					$imageSrc = '';

					if (!empty($optionClass))
					{
						if (preg_match("#\[icon\](.+)\[\/icon\]#", $optionClass, $matches))
						{
							$iconClass = $matches[1];
							$optionClass = preg_replace("#\[icon\](.+)\[\/icon\]#", "", $optionClass);
						}

						if (preg_match("#\[src\](.+)\[\/src\]#", $optionClass, $matches))
						{
							$imageSrc = $matches[1];
							$optionClass = preg_replace("#\[src\](.+)\[\/src\]#", "", $optionClass);
						}
					}
				?>
				<button type="button" class="hu-btn <?php echo $value === $option->value ? 'active' : ''; ?> <?php echo $optionClass; ?>" value="<?php echo $option->value; ?>">
					<?php
						switch($textRule)
						{
							case 'text':
								echo $option->text;
							break;
							case 'icon':
								echo '<span class="' . $iconClass . '"></span>';
							break;
							case 'text-icon':
								echo $option->text . ' <span class="' . $iconClass . '"></span>';
							break;
							case 'icon-text':
								echo '<span class="' . $iconClass . '"></span> ' . $option->text;
							break;
							case 'image':
								echo '<div class="hu-inline-group-img-wrapper"><img class="group-img" src="' . Uri::root() . $imageSrc . '" /></div>';
							break;
							case 'image-text':
								echo '<div class="hu-inline-group-img-wrapper">'.
										'<img class="group-img" src="' . Uri::root() . $imageSrc . '" />'.
										'<p class="img-text">' . $option->text . '</p>'.
									'</div>';
							break;
							case 'text-image':
								echo '<div class="hu-inline-group-img-wrapper">'.
										'<p class="img-text">' . $option->text . '</p>'.
										'<img class="group-img" src="' . Uri::root() . $imageSrc . '" />'.
									'</div>';
							break;
							default:
								echo $option->text;
							break;
						}
					?>
				</button>
			<?php endforeach ?>
		</div>
		<input type="hidden" name="<?php echo $field->name; ?>" id="<?php echo $field->id; ?>" value="<?php echo $value; ?>">
	</div>
<?php endif ?>