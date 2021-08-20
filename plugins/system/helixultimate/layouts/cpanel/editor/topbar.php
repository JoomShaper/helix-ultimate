<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use HelixUltimate\Framework\Platform\Helper;
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

$sidebar = new Settings;

?>

<div class="hu-topbar">
	<div class="topbar-left">
		<div class="hu-logo">
			<div>
				<svg xmlns="http://www.w3.org/2000/svg" class="hu-logo" width="82" height="24" fill="none"><path fill="#0345BF" fill-rule="evenodd" d="M6.976 15.448c-.039-.814.233-3.568 4.732-5.779 4.5-2.21 4.965-3.995 4.965-3.995-.465 2.444-1.241 3.143-5.508 5.43-4.066 2.182-4.189 4.344-4.189 4.344zM6.976 20.144s.123-2.162 4.19-4.344c4.266-2.288 5.042-2.987 5.507-5.43 0 0-.465 1.785-4.965 3.995-4.499 2.21-4.77 4.964-4.732 5.78z" clip-rule="evenodd"/><mask id="a" width="25" height="24" x="0" y="0" maskUnits="userSpaceOnUse"><path fill="#fff" d="M0 0h24.002v24H0V0z"/></mask><g mask="url(#a)"><path fill="#0345BF" fill-rule="evenodd" d="M12 23.999C5.385 23.999 0 18.616 0 12 0 5.382 5.384 0 12 0c6.618 0 12.002 5.383 12.002 12.001C24.002 18.616 18.617 24 12 24zm0-22.782C6.057 1.217 1.219 6.053 1.219 12c0 5.944 4.838 10.782 10.783 10.782 5.945 0 10.782-4.838 10.782-10.782 0-5.947-4.837-10.783-10.782-10.783z" clip-rule="evenodd"/></g><path fill="#000" fill-rule="evenodd" d="M40.96 13.988V9.403h-4.89v4.585h-3.004V2.182h3.005V7.04h4.89V2.182h2.997v11.806h-2.997zM48.475 4.544v2.347h5.631v2.362h-5.631v2.372h6.422v2.363H45.47V2.182h9.246v2.362h-6.241zM64 11.476v2.511h-8.01V2.183h3.005v9.294H64zM65.075 13.988h3.004V2.182h-3.004v11.806zM81.168 13.988h-3.59l-2.552-3.763-2.544 3.763h-3.49l4.215-6.035-3.944-5.771h3.491l2.272 3.44 2.28-3.44h3.524l-3.893 5.63 4.231 6.176z" clip-rule="evenodd"/><path fill="#525252" d="M37.02 18.318v3.717a1.883 1.883 0 01-.242.957c-.16.272-.386.481-.677.628-.29.145-.622.217-.999.217-.573 0-1.033-.155-1.38-.467-.343-.314-.522-.748-.534-1.301v-3.75h.456V22c0 .46.131.816.393 1.07.261.252.617.377 1.065.377.45 0 .803-.127 1.062-.38.262-.255.393-.61.393-1.063v-3.687h.464zM40.911 23.374h2.688v.389h-3.152v-5.445h.464v5.056zM49.292 18.711h-1.866v5.052h-.46V18.71h-1.862v-.393h4.188v.393zM52.88 23.763h-.46v-5.445h.46v5.445zM57.114 18.318l2.008 4.805 2.015-4.805h.614v5.445h-.46v-2.371l.037-2.43-2.027 4.8h-.355l-2.019-4.782.038 2.397v2.386h-.46v-5.445h.609zM68.28 22.237h-2.47l-.562 1.526h-.482l2.06-5.445h.438l2.06 5.445h-.478l-.565-1.526zm-2.329-.393h2.184l-1.092-2.965-1.092 2.965zM75.48 18.711h-1.865v5.052h-.46V18.71h-1.862v-.393h4.187v.393zM81.513 21.153h-2.546v2.22h2.928v.39h-3.388v-5.445h3.369v.393h-2.91v2.053h2.547v.389z"/></svg>
			</div>
			<span class="hu-version"><?php echo Helper::getVersion(); ?></span>
		</div>
	</div>
	<div class="topbar-middle">
		<div class="hu-devices">
			<button class="hu-device active" data-device="desktop" title="Desktop">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="15" fill="none"><path fill="#020B53" d="M17.46 12.07c1.196 0 1.79-.578 1.79-1.789V1.79C19.25.57 18.656 0 17.46 0H1.79C.601 0 0 .57 0 1.79v8.491c0 1.211.602 1.79 1.79 1.79h15.67zm-.023-1.023H1.813c-.546 0-.789-.227-.789-.79V1.806c0-.555.243-.79.79-.79h15.624c.547 0 .797.235.797.79v8.453c0 .562-.25.789-.797.789zm-3.125 3.797c.352 0 .633-.29.633-.649a.639.639 0 00-.633-.648H4.915a.645.645 0 00-.64.648c0 .36.288.649.64.649h9.399z"/></svg>
			</button>
			<button class="hu-device" data-device="tablet" title="Tablet">
				<svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M12.8056 0C13.7316 0 14.4722 0.761229 14.4722 1.68889V16.0889C14.4722 17.0165 13.7316 17.7778 12.8056 17.7778H1.91667C0.990595 17.7778 0.25 17.0165 0.25 16.0889V1.68889C0.25 0.761229 0.990595 0 1.91667 0H12.8056ZM12.8056 0.888889H1.91667L1.81915 0.895113C1.43579 0.944406 1.13889 1.28052 1.13889 1.68889V16.0889L1.14495 16.1894C1.19297 16.5842 1.52036 16.8889 1.91667 16.8889H12.8056L12.9031 16.8827C13.2864 16.8334 13.5833 16.4973 13.5833 16.0889V1.68889L13.5773 1.58843C13.5292 1.19359 13.2019 0.888889 12.8056 0.888889ZM7.36111 14.2222C7.85178 14.2222 8.25 14.6196 8.25 15.1111C8.25 15.6027 7.85178 16 7.36111 16C6.87044 16 6.47222 15.6027 6.47222 15.1111C6.47222 14.6196 6.87044 14.2222 7.36111 14.2222ZM12.2497 12.4444C12.4909 12.4444 12.6944 12.6434 12.6944 12.8889L12.6873 12.9669C12.6496 13.1704 12.468 13.3333 12.2497 13.3333H2.47253C2.23133 13.3333 2.02778 13.1343 2.02778 12.8889L2.03494 12.8109C2.0726 12.6073 2.25419 12.4444 2.47253 12.4444H12.2497Z"/>
				</svg>
			</button>
			<button class="hu-device" data-device="mobile" title="Mobile">
				<svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M9.99995 0C10.7861 0 11.3611 0.689442 11.3611 1.47778V14.0778C11.3611 14.8661 10.7861 15.5556 9.99995 15.5556H1.83328C1.0471 15.5556 0.472168 14.8661 0.472168 14.0778V1.47778C0.472168 0.689442 1.0471 0 1.83328 0H9.99995ZM9.99995 0.777778H1.83328L1.76014 0.783224C1.47262 0.826355 1.24995 1.12045 1.24995 1.47778V14.0778L1.25449 14.1657C1.29051 14.5112 1.53605 14.7778 1.83328 14.7778H9.99995L10.0731 14.7723C10.3606 14.7292 10.5833 14.4351 10.5833 14.0778V1.47778L10.5787 1.38987C10.5427 1.04439 10.2972 0.777778 9.99995 0.777778ZM5.91661 11.6667C6.34595 11.6667 6.69439 12.0143 6.69439 12.4444C6.69439 12.8746 6.34595 13.2222 5.91661 13.2222C5.48728 13.2222 5.13883 12.8746 5.13883 12.4444C5.13883 12.0143 5.48728 11.6667 5.91661 11.6667ZM7.09068 2.33333C7.3049 2.33333 7.47217 2.50744 7.47217 2.72222L7.46442 2.79879C7.42885 2.973 7.27504 3.11111 7.09068 3.11111H4.74254C4.52832 3.11111 4.36106 2.937 4.36106 2.72222L4.36881 2.64565C4.40438 2.47145 4.55819 2.33333 4.74254 2.33333H7.09068Z"/>
				</svg>
			</button>
		</div>
	</div>

	<div class="topbar-right">

		<div class="hu-response">
			<div class="hu-loading-msg">
				<div class="spinner-border spinner-border-sm" role="status">
					<span class="visually-hidden">Drafting...</span>
				</div>
				<span class="hu-response-msg"><?php echo Text::_('HELIX_ULTIMATE_TOPBAR_MSG_DRAFTING'); ?></span>
			</div>

			<div class="hu-done-msg">
				<span class="fas fa-check-circle" aria-hidden="true" style="color: green;"></span> <span class="hu-response-msg"><span class="hu-msg"><?php echo Text::_('HELIX_ULTIMATE_TOPBAR_MSG_DRAFTED'); ?></span></span>
			</div>

			<button type="button" role="button" class="hu-btn hu-btn-reset action-reset-drafts">
				<span class="fas fa-history" aria-hidden="true"></span> <?php echo Text::_('HELIX_ULTIMATE_TOPBAR_MSG_RESET_DRAFT'); ?>
			</button>
		</div>

		<button class="hu-btn hu-btn-primary action-save-template" data-id="<?php echo $id; ?>" data-view="<?php echo $view; ?>">
			<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1.05 1.30792e-06C0.476083 1.30792e-06 0 0.476085 0 1.05V10.95C0 11.5239 0.476083 12 1.05 12H10.95C11.5239 12 12 11.5239 12 10.95V2.85C12.0001 2.79085 11.9886 2.73225 11.9661 2.67756C11.9436 2.62287 11.9105 2.57315 11.8687 2.53125L9.46875 0.131251C9.42685 0.0895008 9.37713 0.0564192 9.32244 0.0338983C9.26774 0.0113773 9.20914 -0.000141288 9.15 1.30792e-06H1.05ZM1.05 0.900001H2.25V4.65C2.25001 4.76934 2.29743 4.88379 2.38181 4.96818C2.4662 5.05257 2.58066 5.09999 2.7 5.1H8.7C8.81934 5.09999 8.93379 5.05257 9.01818 4.96818C9.10257 4.88379 9.14998 4.76934 9.15 4.65V1.0875L11.1 3.0375V10.95C11.1 11.0409 11.0409 11.1 10.95 11.1H1.05C0.959116 11.1 0.9 11.0409 0.9 10.95V1.05C0.9 0.959117 0.959116 0.900001 1.05 0.900001ZM3.15 0.900001H8.25V4.2H3.15V0.900001ZM6 6.15C4.92837 6.15 4.05 7.02837 4.05 8.1C4.05 9.17161 4.92837 10.05 6 10.05C7.07162 10.05 7.95 9.17161 7.95 8.1C7.95 7.02837 7.07162 6.15 6 6.15ZM6 7.05C6.58523 7.05 7.05 7.51477 7.05 8.1C7.05 8.68522 6.58523 9.15 6 9.15C5.41477 9.15 4.95 8.68522 4.95 8.1C4.95 7.51477 5.41477 7.05 6 7.05Z" fill="white"/>
			</svg>
			<span class="helix-topbar-save-text hu-ml-1">
				<div class="hu-topbar-save-spinner hidden spinner-border spinner-border-sm" role="status">
					<span class="visually-hidden">Saving...</span>
				</div>
				<?php echo Text::_('HELIX_ULTIMATE_SAVE_CHANGES'); ?>
			</span>
		</button>
		
		<a class="hu-btn hu-btn-round" href="<?php echo Route::_('index.php?option=com_templates'); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"><path d="M11.414 11.411a.566.566 0 000-.797L6.586 5.786 11.414.958a.566.566 0 000-.797.58.58 0 00-.797 0L5.79 4.99.961.161a.573.573 0 00-.797 0 .566.566 0 000 .797l4.828 4.828-4.828 4.828a.566.566 0 000 .797.566.566 0 00.797 0l4.828-4.828 4.828 4.828a.559.559 0 00.797 0z"/></svg>
		</a>
	</div>
</div>