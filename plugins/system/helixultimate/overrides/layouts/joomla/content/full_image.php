<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

$params   = $displayData->params ?? null;
$images   = json_decode($displayData->images ?? '');
$attribs  = json_decode($displayData->attribs ?? '');

$tplParams = null;
if (class_exists('HelixUltimate\\Framework\\Platform\\Helper')) {
    $template  = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
    $tplParams = $template ? ($template->params ?? null) : null;
}

$og = isset($displayData->enableOpenGraph)
    ? (int) $displayData->enableOpenGraph
    : (int) ($tplParams ? $tplParams->get('og', 0) : 0);

$blogImageSize = $tplParams ? $tplParams->get('blog_details_image', 'large') : 'large';

$fullImage = '';

if (!empty($attribs->helix_ultimate_image)) {
    $fullImage = $attribs->helix_ultimate_image;

    if ($blogImageSize !== 'default') {
        $basename = basename($fullImage);
        $dirname  = trim(dirname($fullImage), '/\\');
        $ext      = pathinfo($basename, PATHINFO_EXTENSION);
        $name     = pathinfo($basename, PATHINFO_FILENAME);

        $variantFsPath = JPATH_ROOT . '/' . ($dirname ? $dirname . '/' : '') . $name . '_' . $blogImageSize . '.' . $ext;

        if (file_exists($variantFsPath)) {
            $fullImage = Uri::root(true) . '/' . ($dirname ? $dirname . '/' : '') . $name . '_' . $blogImageSize . '.' . $ext;
        }
    }
}

if (empty($fullImage) && !empty($images->image_fulltext)) {
    $fullImage = $images->image_fulltext;
}

if (empty($fullImage)) {
    return;
}

$toAbsolute = static function ($url) {
    if (!$url) return $url;
    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }
    return rtrim(Uri::root(), '/') . '/' . ltrim($url, '/');
};

$imgfloat = '';
if (!empty($images->float_fulltext)) {
    $imgfloat = 'float-' . $images->float_fulltext;
} elseif ($params) {
    $pf = $params->get('float_fulltext');
    $imgfloat = $pf ? 'float-' . $pf : '';
}

$altText = '';
if (!empty($attribs->helix_ultimate_image_alt_txt)) {
    $altText = $attribs->helix_ultimate_image_alt_txt;
} elseif (!empty($images->image_fulltext_alt)) {
    $altText = $images->image_fulltext_alt;
} elseif (empty($images->image_fulltext_alt_empty)) {
    $altText = $displayData->title ?? '';
}

$captionText = isset($images->image_fulltext_caption) ? $images->image_fulltext_caption : '';

$figureClass = trim('article-full-image item-image ' . $imgfloat);

?>
<figure class="<?php echo htmlspecialchars($figureClass, ENT_COMPAT, 'UTF-8'); ?>">
    <?php
    $layoutAttr = [
        'src'      => htmlspecialchars($fullImage, ENT_COMPAT, 'UTF-8'),
        'itemprop' => 'image',
        'alt'      => $altText !== '' ? htmlspecialchars($altText, ENT_COMPAT, 'UTF-8') : false,
    ];

    if ($captionText !== '') {
        $layoutAttr['class'] = 'caption';
        $layoutAttr['title'] = htmlspecialchars($captionText, ENT_COMPAT, 'UTF-8');
    }

    echo LayoutHelper::render('joomla.html.image', $layoutAttr);
    ?>

    <?php if ($captionText !== '') : ?>
        <figcaption class="caption"><?php echo htmlspecialchars($captionText, ENT_COMPAT, 'UTF-8'); ?></figcaption>
    <?php endif; ?>
</figure>

<?php if ($og) : ?>
    <?php
    $ogImage = $fullImage ?: (!empty($images->image_fulltext) ? $images->image_fulltext : ($images->image_intro ?? ''));
    $ogImage = $toAbsolute($ogImage);
    $ogImage = HTMLHelper::cleanImageURL($ogImage)->url;

    echo LayoutHelper::render('joomla.content.open_graph', [
        'image'        => $ogImage,
        'title'        => $displayData->title ?? '',
        'fb_app_id'    => $tplParams ? $tplParams->get('og_fb_id') : '',
        'twitter_site' => $tplParams ? $tplParams->get('og_twitter_site') : '',
        'content'      => $displayData->introtext ?? '',
    ]);
    ?>
<?php endif; ?>
