<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;

// Get the mime type class.
$mime = !empty($this->result->mime) ? 'mime-' . $this->result->mime : null;

$show_description = $this->params->get('show_description', 1);

if ($show_description)
{
	// Calculate number of characters to display around the result
	$term_length = StringHelper::strlen($this->query->input);
	$desc_length = $this->params->get('description_length', 255);
	$pad_length  = $term_length < $desc_length ? (int) floor(($desc_length - $term_length) / 2) : 0;

	// Find the position of the search term
	$pos = $term_length ? StringHelper::strpos(StringHelper::strtolower($this->result->description), StringHelper::strtolower($this->query->input)) : false;

	// Find a potential start point
	$start = ($pos && $pos > $pad_length) ? $pos - $pad_length : 0;

	// Find a space between $start and $pos, start right after it.
	$space = StringHelper::strpos($this->result->description, ' ', $start > 0 ? $start - 1 : 0);
	$start = ($space && $space < $pos) ? $space + 1 : $start;

	$description = HTMLHelper::_('string.truncate', StringHelper::substr($this->result->description, $start), $desc_length, true);
}

$route = $this->result->route;

$showImage  = $this->params->get('show_image', 0);
$imageClass = $this->params->get('image_class', '');
$extraAttr  = [];

if ($showImage && !empty($this->result->imageUrl) && $imageClass !== '') {
    $extraAttr['class'] = $imageClass;
}

// Get the route with highlighting information.
if (!empty($this->query->highlight)
	&& empty($this->result->mime)
	&& $this->params->get('highlight_terms', 1)
	&& PluginHelper::isEnabled('system', 'highlight'))
{
	$route .= '&highlight=' . base64_encode(json_encode($this->query->highlight));
}

?>
<li>
	<?php if ($showImage && isset($this->result->imageUrl)) : ?>
		<?php 
			$imageUrl = $this->result->imageUrl;
			$imageAlt = $this->result->imageAlt;
			if (!empty($this->result->params->get('helix_ultimate_image'))) {
				$imageUrl = $this->result->params->get('helix_ultimate_image');
				$imageAlt = $this->result->title;
			}
		?>
        <figure class="<?php echo htmlspecialchars($imageClass ?? "", ENT_COMPAT, 'UTF-8'); ?> result__image">
            <?php if ($this->params->get('link_image') && $this->result->route) : ?>
                <a href="<?php echo Route::_($this->result->route); ?>">
                    <?php echo HTMLHelper::_('image', $imageUrl, $imageAlt, $extraAttr); ?>
                </a>
            <?php else : ?>
                <?php echo HTMLHelper::_('image', $imageUrl, $imageAlt, $extraAttr); ?>
            <?php endif; ?>
        </figure>
    <?php endif; ?>
	<h4 class="result-title <?php echo $mime; ?>">
		<a href="<?php echo Route::_($route); ?>">
			<?php echo $this->result->title; ?>
		</a>
	</h4>
	<?php if ($show_description && $description !== '') : ?>
		<p class="result-text">
			<?php echo $description; ?>
		</p>
	<?php endif; ?>
</li>
