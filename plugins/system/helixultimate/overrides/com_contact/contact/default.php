<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

$cparams = ComponentHelper::getParams('com_media');
$tparams = $this->item->params;
$canDo   = ContentHelper::getActions('com_contact', 'category', $this->item->catid);
$canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by === Factory::getUser()->id);
$htag    = $tparams->get('show_page_heading') ? 'h2' : 'h1';
?>

<!-- for joomla3 -->
<?php if(JVERSION < 4) : ?>
	<div class="contact" itemscope itemtype="https://schema.org/Person">
		<?php if ($tparams->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($tparams->get('page_heading')); ?>
			</h1>
		<?php endif; ?>

		<?php if ($this->contact->name && $tparams->get('show_name')) : ?>
			<div class="page-header">
				<h2>
					<?php if ($this->item->published == 0) : ?>
						<span class="label label-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
					<?php endif; ?>
					<span class="contact-name" itemprop="name"><?php echo $this->contact->name; ?></span>
				</h2>
			</div>
		<?php endif; ?>

		<?php $show_contact_category = $tparams->get('show_contact_category'); ?>

		<?php if ($show_contact_category === 'show_no_link') : ?>
			<h3>
				<span class="contact-category"><?php echo $this->contact->category_title; ?></span>
			</h3>
		<?php elseif ($show_contact_category === 'show_with_link') : ?>
			<?php $contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid); ?>
			<h3>
				<span class="contact-category"><a href="<?php echo $contactLink; ?>">
					<?php echo $this->escape($this->contact->category_title); ?></a>
				</span>
			</h3>
		<?php endif; ?>

		<?php echo $this->item->event->afterDisplayTitle; ?>

		<?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
			<form action="#" method="get" name="selectForm" id="selectForm">
				<label for="select_contact"><?php echo Text::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
				<?php echo HTMLHelper::_('select.genericlist', $this->contacts, 'select_contact', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link); ?>
			</form>
		<?php endif; ?>

		<?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
			<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
			<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
		<?php endif; ?>

		<?php echo $this->item->event->beforeDisplayContent; ?>

		<?php $presentation_style = $tparams->get('presentation_style'); ?>
		<?php $accordionStarted = false; ?>
		<?php $tabSetStarted = false; ?>

		<?php if ($this->params->get('show_info', 1)) : ?>
			<?php if ($presentation_style === 'sliders') : ?>
				<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'basic-details')); ?>
				<?php $accordionStarted = true; ?>
				<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-contact', Text::_('COM_CONTACT_DETAILS'), 'basic-details'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'basic-details')); ?>
				<?php $tabSetStarted = true; ?>
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'basic-details', Text::_('COM_CONTACT_DETAILS')); ?>
			<?php endif; ?>

			<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
				<div class="thumbnail float-end">
					<?php echo HTMLHelper::_('image', $this->contact->image, $this->contact->name, array('itemprop' => 'image')); ?>
				</div>
			<?php endif; ?>

			<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
				<dl class="contact-position dl-horizontal">
					<dt><?php echo Text::_('COM_CONTACT_POSITION'); ?>:</dt>
					<dd itemprop="jobTitle">
						<?php echo $this->contact->con_position; ?>
					</dd>
				</dl>
			<?php endif; ?>

			<?php echo $this->loadTemplate('address'); ?>

			<?php if ($tparams->get('allow_vcard')) : ?>
				<?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
				<a href="<?php echo Route::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>">
				<?php echo Text::_('COM_CONTACT_VCARD'); ?></a>
			<?php endif; ?>

			<?php if ($presentation_style === 'sliders') : ?>
				<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
			<?php if ($presentation_style === 'sliders') : ?>
				<?php if (!$accordionStarted)
				{
					echo HTMLHelper::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-form'));
					$accordionStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-contact', Text::_('COM_CONTACT_EMAIL_FORM'), 'display-form'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php if (!$tabSetStarted)
				{
					echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-form'));
					$tabSetStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'display-form', Text::_('COM_CONTACT_EMAIL_FORM')); ?>
			<?php endif; ?>

			<?php echo $this->loadTemplate('form'); ?>

			<?php if ($presentation_style === 'sliders') : ?>
				<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_links')) : ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
			<?php if ($presentation_style === 'sliders') : ?>
				<?php if (!$accordionStarted)
				{
					echo HTMLHelper::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-articles'));
					$accordionStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-contact', Text::_('JGLOBAL_ARTICLES'), 'display-articles'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php if (!$tabSetStarted)
				{
					echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-articles'));
					$tabSetStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'display-articles', Text::_('JGLOBAL_ARTICLES')); ?>
			<?php elseif ($presentation_style === 'plain') : ?>
				<?php echo '<h3>' . Text::_('JGLOBAL_ARTICLES') . '</h3>'; ?>
			<?php endif; ?>

			<?php echo $this->loadTemplate('articles'); ?>

			<?php if ($presentation_style === 'sliders') : ?>
				<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_profile') && $this->contact->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
			<?php if ($presentation_style === 'sliders') : ?>
				<?php if (!$accordionStarted)
				{
					echo HTMLHelper::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-profile'));
					$accordionStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-contact', Text::_('COM_CONTACT_PROFILE'), 'display-profile'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php if (!$tabSetStarted)
				{
					echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-profile'));
					$tabSetStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'display-profile', Text::_('COM_CONTACT_PROFILE')); ?>
			<?php elseif ($presentation_style === 'plain') : ?>
				<?php echo '<h3>' . Text::_('COM_CONTACT_PROFILE') . '</h3>'; ?>
			<?php endif; ?>

			<?php echo $this->loadTemplate('profile'); ?>

			<?php if ($presentation_style === 'sliders') : ?>
				<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
			<?php echo $this->loadTemplate('user_custom_fields'); ?>
		<?php endif; ?>

		<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
			<?php if ($presentation_style === 'sliders') : ?>
				<?php if (!$accordionStarted)
				{
					echo HTMLHelper::_('bootstrap.startAccordion', 'slide-contact', array('active' => 'display-misc'));
					$accordionStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-contact', Text::_('COM_CONTACT_OTHER_INFORMATION'), 'display-misc'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php if (!$tabSetStarted)
				{
					echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'display-misc'));
					$tabSetStarted = true;
				}
				?>
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'display-misc', Text::_('COM_CONTACT_OTHER_INFORMATION')); ?>
			<?php elseif ($presentation_style === 'plain') : ?>
				<?php echo '<h3>' . Text::_('COM_CONTACT_OTHER_INFORMATION') . '</h3>'; ?>
			<?php endif; ?>

			<div class="contact-miscinfo">
				<dl class="dl-horizontal">
					<dt>
						<span class="<?php echo $tparams->get('marker_class'); ?>">
						<?php echo $tparams->get('marker_misc'); ?>
						</span>
					</dt>
					<dd>
						<span class="contact-misc">
							<?php echo $this->contact->misc; ?>
						</span>
					</dd>
				</dl>
			</div>

			<?php if ($presentation_style === 'sliders') : ?>
				<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
			<?php elseif ($presentation_style === 'tabs') : ?>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($accordionStarted) : ?>
			<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
		<?php elseif ($tabSetStarted) : ?>
			<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
		<?php endif; ?>

		<?php echo $this->item->event->afterDisplayContent; ?>
	</div>
<?php endif; ?>

<!-- for joomla4 -->
<?php if(JVERSION >= 4) : ?>
<div class="com-contact contact" itemscope itemtype="https://schema.org/Person">
	<?php if ($tparams->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($tparams->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<?php if ($this->item->name && $tparams->get('show_name')) : ?>
		<div class="page-header">
			<<?php echo $htag; ?>>
				<?php if ($this->item->published == 0) : ?>
					<span class="badge bg-warning text-light"><?php echo Text::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<span class="contact-name" itemprop="name"><?php echo $this->item->name; ?></span>
			</<?php echo $htag; ?>>
		</div>
	<?php endif; ?>

	<?php if ($canEdit) : ?>
		<?php echo HTMLHelper::_('contacticon.edit', $this->item, $tparams); ?>
	<?php endif; ?>

	<?php $show_contact_category = $tparams->get('show_contact_category'); ?>

	<?php if ($show_contact_category === 'show_no_link') : ?>
		<h3>
			<span class="contact-category"><?php echo $this->item->category_title; ?></span>
		</h3>
	<?php elseif ($show_contact_category === 'show_with_link') : ?>
		<?php $contactLink = Route::_(JVERSION < 4 ? ContactHelperRoute::getCategoryRoute($this->item) : Joomla\Component\Contact\Site\Helper\RouteHelper::getCategoryRoute($this->item->catid, $this->item->language)); ?>
		<h3>
			<span class="contact-category"><a href="<?php echo $contactLink; ?>">
				<?php echo $this->escape($this->item->category_title); ?></a>
			</span>
		</h3>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayTitle; ?>

	<?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
		<form action="#" method="get" name="selectForm" id="selectForm" class="mb-4">
			<label for="select_contact" class="form-label"><?php echo Text::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
			<?php echo HTMLHelper::_(
				'select.genericlist',
				$this->contacts,
				'select_contact',
				'class="form-select" onchange="document.location.href = this.value"', 'link', 'name', $this->item->link);
			?>
		</form>
	<?php endif; ?>

	<?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
		<div class="com-contact__tags">
			<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
			<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
		</div>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if ($this->params->get('show_info', 1)) : ?>
		<div class="row">
			<?php //echo '<h3>' . Text::_('COM_CONTACT_DETAILS') . '</h3>'; ?>
			<div class="col">
				<?php if ($this->item->con_position && $tparams->get('show_position')) : ?>
					<div class="contact-position d-flex mb-3">
						<div class="me-2">
							<strong><?php echo Text::_('COM_CONTACT_POSITION'); ?>:</strong>
						</div>
						<div itemprop="jobTitle">
							<?php echo $this->item->con_position; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="contact-info">
					<?php echo $this->loadTemplate('address'); ?>
					<?php if ($tparams->get('allow_vcard')) : ?>
						<div class="mb-4">
							<?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
							<a href="<?php echo Route::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->item->id . '&amp;format=vcf'); ?>">
								<?php echo Text::_('COM_CONTACT_VCARD'); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if ($this->item->image && $tparams->get('show_image')) : ?>
				<div class="col-lg-auto">
					<?php echo HTMLHelper::_(
						'image',
						$this->item->image,
						htmlspecialchars($this->item->name,  ENT_QUOTES, 'UTF-8'),
						array('itemprop' => 'image')
					); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ($tparams->get('show_email_form') && ($this->item->email_to || $this->item->user_id)) : ?>
		<?php echo '<h3>' . Text::_('COM_CONTACT_EMAIL_FORM') . '</h3>'; ?>
		<?php echo $this->loadTemplate('form'); ?>
	<?php endif; ?>

	<?php if ($tparams->get('show_links')) : ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php if ($tparams->get('show_articles') && $this->item->user_id && $this->item->articles) : ?>
		<?php echo '<h3>' . Text::_('JGLOBAL_ARTICLES') . '</h3>'; ?>
		<?php echo $this->loadTemplate('articles'); ?>
	<?php endif; ?>

	<?php if ($tparams->get('show_profile') && $this->item->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
		<?php echo '<h3>' . Text::_('COM_CONTACT_PROFILE') . '</h3>'; ?>
		<?php echo $this->loadTemplate('profile'); ?>
	<?php endif; ?>

	<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
		<?php echo $this->loadTemplate('user_custom_fields'); ?>
	<?php endif; ?>

	<?php if ($this->item->misc && $tparams->get('show_misc')) : ?>
		<div class="contact-miscinfo">
			<?php echo '<h3>' . Text::_('COM_CONTACT_OTHER_INFORMATION') . '</h3>'; ?>
			<div class="d-flex">
				<div class="me-2">
					<?php if (!$this->params->get('marker_misc')) : ?>
						<span class="fas fa-info-circle" aria-hidden="true"></span>
						<span class="visually-hidden"><?php echo Text::_('COM_CONTACT_OTHER_INFORMATION'); ?></span>
					<?php else : ?>
						<span class="<?php echo $this->params->get('marker_class'); ?>">
							<?php echo $this->params->get('marker_misc'); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="contact-misc">
					<?php echo $this->item->misc; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
</div>
<?php endif; ?>
