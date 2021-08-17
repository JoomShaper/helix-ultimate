<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

?>

<?php foreach ($groupData as $key => $data): ?>
	<?php if (\is_numeric($key)): ?>
		<?php echo LayoutHelper::render('cpanel.control-board.fieldset.field', ['field' => $data, 'group' => $group], HELIX_LAYOUTS_PATH); ?>
	<?php elseif (preg_match("#^subgroup-.+$#", $key)): ?>
		<?php
			$masterLabel = $data[0]->getAttribute('masterlabel');
			$masterLabel = isset($masterLabel) ? Text::_($masterLabel) : '';

			$masterDescription = $data[0]->getAttribute('masterdesc');
			$masterDescription = isset($masterDescription) ? Text::_($masterDescription) : '';

			$masterClass = $data[0]->getAttribute('masterclass');
			$masterClass = isset($masterClass) ? $masterClass : 'row hu-align-items-center';

			$masterHasSeparator = $data[0]->getAttribute('masterseparator');
			$masterHasSeparator = isset($masterHasSeparator) && ($masterHasSeparator === 'true' || $masterHasSeparator === 'on') ? ' hu-field-separator': '';
		?>
		<!-- if master label provider for the subgroup -->
		<?php if (!empty($masterLabel)): ?>
			<div class="control-group master-label-group">
				<div class="control-label">
					<label for=""><?php echo $masterLabel; ?></label>
					<?php if (!empty($masterDescription)): ?>
						<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>
						<p class="hu-control-help"><?php echo $masterDescription; ?></p>
					<?php endif ?>
				</div>
			</div>
		<?php endif ?>

		<div class="hu-subgroup <?php echo $masterClass . $masterHasSeparator; ?>">
			<?php foreach ($data as $subgroup => $field): ?>
				<?php
					$classes =  $field->getAttribute('subclasses');
					$classes = isset($classes) ? $classes : 'col';
				?>
				<div class="<?php echo $classes; ?>">
					<?php echo LayoutHelper::render('cpanel.control-board.fieldset.field', ['field' => $field, 'group' => $group], HELIX_LAYOUTS_PATH); ?>
				</div>
			<?php endforeach ?>
		</div>
	<?php endif  ?>
<?php endforeach ?>