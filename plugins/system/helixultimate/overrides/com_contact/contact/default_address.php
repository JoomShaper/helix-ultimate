<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;
?>
<div class="contact-address mb-4" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
	<?php if (($this->params->get('address_check') > 0) &&
		($this->item->address || $this->item->suburb  || $this->item->state || $this->item->country || $this->item->postcode)) : ?>
		<div class="d-flex">
			<div class="me-2">
				<?php if (!$this->params->get('marker_address')) : ?>
					<span class="fas fa-address-book fa-fw" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_ADDRESS'); ?></span>
				<?php else : ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_address'); ?>
					</span>
				<?php endif; ?>
			</div>

			<div>
				<?php if ($this->item->address && $this->params->get('show_street_address')) : ?>
					<div class="contact-street" itemprop="streetAddress">
						<?php echo nl2br($this->item->address, false); ?>
					</div>
				<?php endif; ?>

				<?php if ($this->item->suburb && $this->params->get('show_suburb')) : ?>
					<div class="contact-suburb" itemprop="addressLocality">
						<?php echo $this->item->suburb; ?>
					</div>
				<?php endif; ?>

				<?php if ($this->item->state && $this->params->get('show_state')) : ?>
					<div class="contact-state" itemprop="addressRegion">
						<?php echo $this->item->state; ?>
					</div>
				<?php endif; ?>

				<?php if ($this->item->postcode && $this->params->get('show_postcode')) : ?>
					<div class="contact-postcode" itemprop="postalCode">
						<?php echo $this->item->postcode; ?>
					</div>
				<?php endif; ?>

				<?php if ($this->item->country && $this->params->get('show_country')) : ?>
					<div class="contact-country" itemprop="addressCountry">
						<?php echo $this->item->country; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($this->item->email_to && $this->params->get('show_email')) : ?>
		<div class="d-flex mt-2">
			<div class="me-2">
				<?php if (!$this->params->get('marker_email')) : ?>
					<span class="fas fa-envelope fa-fw" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_EMAIL'); ?></span>
				<?php else : ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_email'); ?>
					</span>
				<?php endif; ?>
			</div>
			
			<div class="contact-emailto">
				<?php echo $this->item->email_to; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($this->item->telephone && $this->params->get('show_telephone')) : ?>
		<div class="d-flex mt-2">
			<div class="me-2">
				<?php if (!$this->params->get('marker_telephone')) : ?>
					<span class="fas fa-phone fa-fw" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_TELEPHONE'); ?></span>
				<?php else : ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_telephone'); ?>
					</span>
				<?php endif; ?>
			</div>
			
			<div class="contact-telephone" itemprop="telephone">
				<?php echo $this->item->telephone; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($this->item->fax && $this->params->get('show_fax')) : ?>
		<div class="d-flex mt-2">
			<div class="me-2">
				<?php if (!$this->params->get('marker_fax')) : ?>
					<span class="fas fa-fax fa-fw" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_FAX'); ?></span>
				<?php else : ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_fax'); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="contact-fax" itemprop="faxNumber">
				<?php echo $this->item->fax; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($this->item->mobile && $this->params->get('show_mobile')) : ?>
		<div class="d-flex mt-2">
			<div class="me-2">
				<?php if (!$this->params->get('marker_mobile')) : ?>
					<span class="fas fa-mobile fa-fw" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_MOBILE'); ?></span>
				<?php else : ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_mobile'); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="contact-mobile" itemprop="telephone">
				<?php echo $this->item->mobile; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($this->item->webpage && $this->params->get('show_webpage')) : ?>
		<div class="d-flex mt-2">
			<div class="me-2">
				<?php if (!$this->params->get('marker_webpage')) : ?>
					<span class="fas fa-globe fa-fw" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_WEBPAGE'); ?></span>
				<?php else : ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_webpage'); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="contact-webpage">
				<a href="<?php echo $this->item->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
					<?php echo PunycodeHelper::urlToUTF8($this->item->webpage); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>
</div>