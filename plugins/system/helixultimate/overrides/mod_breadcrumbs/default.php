<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;

?>

<ol class="breadcrumb">
	<?php if ($params->get('showHere', 1)) : ?>
		<li class="float-start">
			<?php echo Text::_('MOD_BREADCRUMBS_HERE'); ?>&#160;
		</li>
	<?php else : ?>
		<li class="float-start">
			<span class="divider fas fa-map-marker-alt" aria-hidden="true"></span>
		</li>
	<?php endif; ?>

	<?php
	// Get rid of duplicated entries on trail including home page when using multilanguage
	for ($i = 0; $i < $count; $i++)
	{
		if ($i === 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link === $list[$i - 1]->link)
		{
			unset($list[$i]);
		}
	}

	// Find last and penultimate items in breadcrumbs list
	end($list);
	$last_item_key   = key($list);
	prev($list);
	$penult_item_key = key($list);

	// Make a link if not the last item in the breadcrumbs
	$show_last = $params->get('showLast', 1);

	// Generate the trail
	foreach ($list as $key => $item) :
		if ($key !== $last_item_key) :
			if (!empty($item->link)) :
				$breadcrumbItem = '<a href="' . $item->link . '" class="pathway"><span>' . $item->name . '</span></a>';
			else :
				$breadcrumbItem = '<span>' . $item->name . '</span>';
			endif;
			// Render all but last item - along with separator ?>
			<li class="breadcrumb-item"><?php echo $breadcrumbItem; ?></li>
		<?php elseif ($show_last) :
			$breadcrumbItem = '<span>' . $item->name . '</span>';
			// Render last item if reqd. ?>
			<li class="breadcrumb-item active"><?php echo $breadcrumbItem; ?></li>
		<?php endif;
	endforeach; ?>
</ol>

<?php

    // Structured data as JSON
    $data = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        '@id'             => Uri::root() . '#/schema/BreadcrumbList/' . (int) $module->id,
        'itemListElement' => []
    ];

    // Use an independent counter for positions. E.g. if Heading items in pathway.
    $itemsCounter = 0;

    // If showHome is disabled use the fallback $homeCrumb for startpage at first position.
    if (isset($homeCrumb)) {
        $data['itemListElement'][] = [
                '@type'    => 'ListItem',
                'position' => ++$itemsCounter,
                'item'     => [
                        '@id'  => Route::_($homeCrumb->link, true, Route::TLS_IGNORE, true),
                        'name' => $homeCrumb->name,
                ],
        ];
    }

    foreach ($list as $key => $item) {
        // Only add item to JSON if it has a valid link, otherwise skip it.
        if (!empty($item->link)) {
            $data['itemListElement'][] = [
                    '@type'    => 'ListItem',
                    'position' => ++$itemsCounter,
                    'item'     => [
                            '@id'  => Route::_($item->link, true, Route::TLS_IGNORE, true),
                            'name' => $item->name,
                    ],
            ];
        } elseif ($key === $last_item_key) {
            // Add the last item (current page) to JSON, but without a link.
            // Google accepts items without a URL only as the current page.
            $data['itemListElement'][] = [
                    '@type'    => 'ListItem',
                    'position' => ++$itemsCounter,
                    'item'     => [
                            'name' => $item->name,
                    ],
            ];
        }
    }

    if ($itemsCounter) {
        /** @var WebAssetManager $wa */
        $wa = $app->getDocument()->getWebAssetManager();
        $prettyPrint = JDEBUG ? JSON_PRETTY_PRINT : 0;
        $wa->addInline(
            'script',
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | $prettyPrint),
            ['name' => 'inline.mod_breadcrumbs-schemaorg'],
            ['type' => 'application/ld+json']
        );
    }
?>

