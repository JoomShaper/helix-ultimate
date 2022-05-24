<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Uri\Uri;

extract($displayData);

// Load the modal behavior script.
JHtml::_('behavior.modal');

// Include jQuery
JHtml::_('jquery.framework');
JHtml::_('script', 'media/mediafield-mootools.min.js', array('version' => 'auto', 'relative' => true, 'framework' => true));

// Tooltip for INPUT showing whole image path
$options = array(
	'onShow' => 'jMediaRefreshImgpathTip',
);

JHtml::_('behavior.tooltip', '.hasTipImgpath', $options);

if (!empty($class))
{
	$class .= ' form-control hasTipImgpath';
}
else
{
	$class = 'form-control hasTipImgpath';
}

$attr = '';

$attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small field-media-input ' . $class . '"' : ' class="input-small"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

// The text field.
echo '<div class="input-group">';

// The Preview.
$showPreview = true;
$showAsTooltip = false;

switch ($preview)
{
	case 'no': // Deprecated parameter value
	case 'false':
	case 'none':
		$showPreview = false;
		break;

	case 'yes': // Deprecated parameter value
	case 'true':
	case 'show':
		break;
	case 'tooltip':
	default:
		$showAsTooltip = true;
		$options = array(
				'onShow' => 'jMediaRefreshPreviewTip',
		);
		JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
		break;
}

// Pre fill the contents of the popover
if ($showPreview)
{
	if ($value && file_exists(JPATH_ROOT . '/' . $value))
	{
		$src = Uri::root() . $value;
	}
	else
	{
		$src = '';
	}

	$width = $previewWidth;
	$height = $previewHeight;
	$style = '';
	$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
	$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

	$imgattr = array(
		'id' => $id . '_preview',
		'class' => 'media-preview',
		'style' => $style,
	);

	$img = JHtml::_('image', $src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
	$previewImg = '<div id="' . $id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
	$previewImgEmpty = '<div id="' . $id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
		. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

	if ($showAsTooltip)
	{
		echo '<div class="media-preview input-group-text">';
		$tooltip = $previewImgEmpty . $previewImg;
		$options = array(
			'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
					'text' => '<span class="icon-eye" aria-hidden="true"></span>',
					'class' => 'input-group-text hasTipPreview'
					);

		echo JHtml::_('tooltip', $tooltip, $options);
		echo '</div>';
	}
	else
	{
		echo '<div class="media-preview input-group-text" style="height:auto">';
		echo ' ' . $previewImgEmpty;
		echo ' ' . $previewImg;
		echo '</div>';
	}
}

echo '	<input type="text" name="' . $name . '" id="' . $id . '" value="'
	. htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' data-basepath="'
	. Uri::root() . '"/>';

?>

<?php
	$modalLink = '';

	if(!$readonly)
	{
		if(!$link)
		{
			$modalLink .= 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author=' . $authorField;
		}
		else
		{
			$modalLink .= $link;
		}

		$modalLink .= '&amp;fieldid=' . $id . '&amp;folder=' . $folder;
	}
?>

<?php
/**
 * Close the modal on selecting image
 * and clicking insert button
 */
JFactory::getDocument()->addScriptDeclaration(
	"
		jQuery(function($) {
			window.parent.jModalClose = function(e) {
				let bsModal = $('.modal.show');
				let mtModal = $('#sbox-window');
				let frameContents = $('#sbox-content iframe').contents(); 
				let isMediaModal = frameContents.find('body.com-media.view-images').length > 0;

				if (bsModal.length) {
					if (isMediaModal) {
						if ($('.img-preview.selected').length) {
							bsModal.modal('hide');
						}
					}
				} else if (mtModal.length) {
					if (isMediaModal) {

						let imageListFrame = frameContents.find('iframe').contents().find('body.com-media.view-imagesList');
						
						if (imageListFrame.find('.img-preview.selected').length) {
							SqueezeBox.close();
						}
					}
				}
			}
		});
	"
);
?>

	<div class="input-group-text bg-transparent border-0 ps-2">
		<a class="modal modal-btn btn btn-primary me-2" title="<?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?>" href="<?php echo $modalLink; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}" style="display: block;">
			<?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?>
		</a>

		<a class="btn btn-secondary"
			title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>"
			href="#"
			onclick="jInsertFieldValue('', '<?php echo $id; ?>'); return false;"
		>
			<span class="fas fa-times" aria-hidden="true"></span>
		</a>
	</div>
</div>