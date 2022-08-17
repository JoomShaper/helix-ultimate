<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$twofactormethods	= [];
if(version_compare(JVERSION, '4.2.0', '<'))
{
	$twofactormethods 	= AuthenticationHelper::getTwoFactorMethods();
}
$doc 				= Factory::getDocument();
$app              	= Factory::getApplication();

ob_start();
?>	
<div class="coming-soon-login">
	<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login" class="mt-5">
		<div class="row gx-3 align-items-center">
			<div class="col-auto">
				<label class="visually-hidden" for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
				<div class="input-group mb-2">
					<div class="input-group-text"><span class="fas fa-user" aria-hidden="true"></span></div>
					<input name="username" type="text" class="form-control" id="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>">
				</div>
			</div>

			<div class="col-auto">
				<label class="visually-hidden" for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
				<div class="input-group mb-2">
					<div class="input-group-text"><span class="fas fa-key" aria-hidden="true"></span></div>
					<input name="password" type="password" class="form-control" id="password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
				</div>
			</div>
			
			<?php if (count($twofactormethods) > 1) : ?>
				<div class="col-auto">
					<label class="visually-hidden" for="secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
					<div class="input-group mb-2">
						<div class="input-group-text"><span class="fas fa-user-secret" aria-hidden="true"></span></div>
						<input name="secretkey" type="text" class="form-control" id="secretkey" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
					</div>
				</div>
			<?php endif; ?>

			<div class="col-auto">
				<input type="submit" name="Submit" class="btn btn-success mb-2 login" value="<?php echo Text::_('JLOGIN'); ?>" />
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="user.login" />
				<input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>" />
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>

		</div>
	</form>
</div>	
<?php
$login_form = ob_get_clean();
echo LayoutHelper::render('comingsoon', array('language' => $this->language, 'direction' => $this->direction, 'params' => $this->params, 'login' => true, 'login_form' => $login_form));