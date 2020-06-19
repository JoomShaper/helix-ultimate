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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$app = Factory::getApplication();

// Gets the FrontEnd Main page Uri
$frontEndUri = Uri::getInstance(Uri::root());
$frontEndUri->setScheme(((int) $app->get('force_ssl', 0) === 2) ? 'https' : 'http');
$mainPageUri = $frontEndUri->toString();

?>

<div class="helix-ultimate-topbar">
	<div class="topbar-left">
		<div class="helix-ultimate-logo">
			<img class="logo" src="<?php echo Uri::root() . '/plugins/system/helixultimate/assets/images/helix-ultimate-logo.svg'; ?>" alt="Helix Ultimate by JoomShaper">
			<img class="hamburger" src="<?php echo Uri::root() . '/plugins/system/helixultimate/assets/images/helix-ultimate-logo-alt.svg'; ?>" alt="Helix Ultimate by JoomShaper">
		</div>
	</div>
	<div class="topbar-middle">
		<div class="helix-ultimate-display-variants d-flex">
			<button class="hu-device" data-device="mobile" title="Mobile">
				<svg width="11" height="19" viewBox="0 0 11 19" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M6.58001 15.2C6.58001 15.54 6.32001 15.8 5.98001 15.8H4.43999C4.09999 15.8 3.83999 15.54 3.83999 15.2C3.83999 14.86 4.09999 14.6 4.43999 14.6H5.98001C6.30001 14.6 6.58001 14.86 6.58001 15.2ZM10.4 16.88C10.4 17.72 9.72001 18.4 8.88001 18.4H1.52C0.679995 18.4 0 17.72 0 16.88V1.52002C0 0.68002 0.679995 0 1.52 0H8.88001C9.72001 0 10.4 0.68002 10.4 1.52002V16.88ZM1.6 1.6V12.2H8.8V1.6H1.6ZM8.8 16.8V13.4H1.6V16.8H8.8Z" fill="#C3C9CB"/>
				</svg>
			</button>
			<button class="hu-device" data-device="tablet" title="Tablet">
				<svg width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M9.31998 15.4C9.31998 15.74 9.05998 16 8.71998 16H7.67999C7.33999 16 7.07999 15.74 7.07999 15.4C7.07999 15.06 7.33999 14.8 7.67999 14.8H8.71998C9.05998 14.8 9.31998 15.06 9.31998 15.4ZM15.6 16.88C15.6 17.72 14.92 18.4 14.08 18.4H2.31998C1.47998 18.4 0.799988 17.72 0.799988 16.88V1.52002C0.799988 0.68002 1.47998 0 2.31998 0H14.08C14.92 0 15.6 0.68002 15.6 1.52002V16.88ZM2.39999 1.6V12.6H14V1.6H2.39999ZM14 16.8V13.8H2.39999V16.8H14Z" fill="#C3C9CB"/>
				</svg>
			</button>
			<button class="hu-device active" data-device="desktop" title="Desktop">
				<svg width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M19.477 0.200012H1.7539C0.784671 0.200012 6.10352e-05 0.98465 6.10352e-05 1.95388V12.1769C6.10352e-05 13.1461 0.784671 14.0462 1.7539 14.0462H7.61545V14.9923L5.8616 16.4C5.5616 16.6538 5.42314 17.1385 5.53852 17.5077C5.67698 17.8769 6.02315 18.2 6.41546 18.2H14.7231C15.1155 18.2 15.4847 17.8769 15.6231 17.5077C15.7616 17.1385 15.6462 16.6769 15.3462 16.4231L13.6154 14.9923V14.0462H19.477C20.4462 14.0462 21.2308 13.1461 21.2308 12.1769V1.95388C21.2308 0.98465 20.4462 0.200012 19.477 0.200012ZM12.277 16.0769L12.8308 16.5846H8.30775L8.90775 16.0538C9.09236 15.8923 9.23083 15.6154 9.23083 15.3615V14.0231H12.0001V15.3615C12.0001 15.6154 12.0924 15.9154 12.277 16.0769ZM19.3847 12.2H1.84621V2.04617H19.3847V12.2Z" fill="#C3C9CB"/>
				</svg>
			</button>
		</div>
	</div>

	<div class="topbar-right">

		<button type="button" role="button" class="btn helix-btn action-reset-drafts hide">
			<img src="<?php echo Uri::root() . 'plugins/system/helixultimate/assets/images/icons/eraser.svg'; ?>" alt="Reset to the last save point" width="25" title="<?php echo Text::_('HELIX_ULTIMATE_RESET_DRAFT'); ?>">
		</button>

		<button class="btn helix-btn reload-preview-iframe">
			<img src="<?php echo Uri::root() . 'plugins/system/helixultimate/assets/images/icons/reload.svg'; ?>" alt="Reset to the last save point" width="20" title="<?php echo Text::_('HELIX_ULTIMATE_RELOAD_IFRAME'); ?>">
		</button>
		
		<button class="btn helix-btn helix-btn-primary btn-rounded action-save-template" data-id="<?php echo $id; ?>" data-view="<?php echo $view; ?>">
			<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1.05 1.30792e-06C0.476083 1.30792e-06 0 0.476085 0 1.05V10.95C0 11.5239 0.476083 12 1.05 12H10.95C11.5239 12 12 11.5239 12 10.95V2.85C12.0001 2.79085 11.9886 2.73225 11.9661 2.67756C11.9436 2.62287 11.9105 2.57315 11.8687 2.53125L9.46875 0.131251C9.42685 0.0895008 9.37713 0.0564192 9.32244 0.0338983C9.26774 0.0113773 9.20914 -0.000141288 9.15 1.30792e-06H1.05ZM1.05 0.900001H2.25V4.65C2.25001 4.76934 2.29743 4.88379 2.38181 4.96818C2.4662 5.05257 2.58066 5.09999 2.7 5.1H8.7C8.81934 5.09999 8.93379 5.05257 9.01818 4.96818C9.10257 4.88379 9.14998 4.76934 9.15 4.65V1.0875L11.1 3.0375V10.95C11.1 11.0409 11.0409 11.1 10.95 11.1H1.05C0.959116 11.1 0.9 11.0409 0.9 10.95V1.05C0.9 0.959117 0.959116 0.900001 1.05 0.900001ZM3.15 0.900001H8.25V4.2H3.15V0.900001ZM6 6.15C4.92837 6.15 4.05 7.02837 4.05 8.1C4.05 9.17161 4.92837 10.05 6 10.05C7.07162 10.05 7.95 9.17161 7.95 8.1C7.95 7.02837 7.07162 6.15 6 6.15ZM6 7.05C6.58523 7.05 7.05 7.51477 7.05 8.1C7.05 8.68522 6.58523 9.15 6 9.15C5.41477 9.15 4.95 8.68522 4.95 8.1C4.95 7.51477 5.41477 7.05 6 7.05Z" fill="white"/>
			</svg>
			<span class="helix-topbar-save-text"><?php echo Text::_('HELIX_ULTIMATE_SAVE_CHANGES'); ?></span>
		</button>

		<a class="btn helix-btn helix-btn-secondary helix-preview-btn btn-rounded" href="<?php echo $mainPageUri; ?>" target="_blank" rel="noopener noreferrer">
			<svg width="20" height="13" viewBox="0 0 20 13" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M9.864 12.33C15.5006 12.33 19.728 7.70625 19.728 6.165C19.728 4.62375 15.5006 0 9.864 0C4.22743 0 0 4.62375 0 6.165C0 7.70625 4.22743 12.33 9.864 12.33ZM9.86396 9.86399C11.9069 9.86399 13.563 8.20789 13.563 6.16499C13.563 4.12209 11.9069 2.46599 9.86396 2.46599C7.82106 2.46599 6.16496 4.12209 6.16496 6.16499C6.16496 8.20789 7.82106 9.86399 9.86396 9.86399Z" fill="#A7B1C3"/>
			</svg>
			<span class="helix-topbar-show-preview-text"><?php echo Text::_('HELIX_ULTIMATE_SHOW_PREVIEW'); ?></span>
		</a>
		<!-- previous btn: action-helix-ultimate-exit  -->
		<a class="helix-close-btn btn-circle" href="<?php echo Route::_('index.php?option=com_templates'); ?>">
			<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M13.5934 11.88C14.0734 12.36 14.0734 13.1066 13.5934 13.5866C13.3534 13.8266 13.0601 13.9333 12.7401 13.9333C12.4201 13.9333 12.1267 13.8266 11.8867 13.5866L7.00673 8.70665L2.12671 13.5866C1.88671 13.8266 1.59339 13.9333 1.27339 13.9333C0.953392 13.9333 0.660072 13.8266 0.420072 13.5866C-0.0599284 13.1066 -0.0599284 12.36 0.420072 11.88L5.30005 6.99998L0.420072 2.11998C-0.0599284 1.63998 -0.0599284 0.893311 0.420072 0.413311C0.900072 -0.066689 1.64671 -0.066689 2.12671 0.413311L7.00673 5.29331L11.8867 0.413311C12.3667 -0.066689 13.1134 -0.066689 13.5934 0.413311C14.0734 0.893311 14.0734 1.63998 13.5934 2.11998L8.71337 6.99998L13.5934 11.88Z" fill="#A7B1C3"/>
			</svg>
		</a>
	</div>
</div>