<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Document & assets
$doc = Factory::getDocument();

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $doc->getWebAssetManager();
$wa->useScript('keepalive')
   ->useScript('form.validate')
   ->useScript('com_content.form-edit')
   ->useScript('bootstrap.modal');

// Helix frontend editor CSS
$doc->addStylesheet(Uri::base() . 'plugins/system/helixultimate/assets/css/frontend-editor.css');

// Tabs & form config
$this->tab_name          = 'com-content-form';
$this->ignore_fieldsets  = ['image-intro', 'image-full', 'jmetadata', 'item_associations'];
$this->useCoreUI         = true;

// Params
$params = $this->state->get('params');

if (!$params->exists('show_publishing_options')) {
    $params->set('show_urls_images_frontend', '0');
}

// Prefill Helix blog options into the form
$attribs = json_decode($this->item->attribs ?? '');
$this->form->setValue('helix_ultimate_image',           'attribs', !empty($attribs->helix_ultimate_image) ? $attribs->helix_ultimate_image : '');
$this->form->setValue('helix_ultimate_image_alt_txt',   'attribs', !empty($attribs->helix_ultimate_image_alt_txt) ? $attribs->helix_ultimate_image_alt_txt : '');
$this->form->setValue('helix_ultimate_article_format',  'attribs', !empty($attribs->helix_ultimate_article_format) ? $attribs->helix_ultimate_article_format : 'standard');
$this->form->setValue('helix_ultimate_audio',           'attribs', !empty($attribs->helix_ultimate_audio) ? $attribs->helix_ultimate_audio : '');
$this->form->setValue('helix_ultimate_gallery',         'attribs', !empty($attribs->helix_ultimate_gallery) ? $attribs->helix_ultimate_gallery : '');
$this->form->setValue('helix_ultimate_video',           'attribs', !empty($attribs->helix_ultimate_video) ? $attribs->helix_ultimate_video : '');

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
if (!$params->exists('show_publishing_options')) {
	$params->set('show_urls_images_frontend', '0');
}

?>
<div class="hu-content-edit edit item-page<?php echo $this->pageclass_sfx ? ' ' . $this->pageclass_sfx : ''; ?>">
    <?php if ($params->get('show_page_heading')): ?>
        <div class="page-header">
            <h1><?php echo $this->escape($params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <form action="<?php echo Route::_('index.php?option=com_content&a_id=' . (int) $this->item->id); ?>"
          method="post"
          name="adminForm"
          id="adminForm"
          class="form-validate form-vertical com-content-adminForm">

        <fieldset>
            <?php echo HTMLHelper::_('uitab.startTabSet', $this->tab_name, ['active' => 'editor', 'recall' => true, 'breakpoint' => 768]); ?>

            <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'editor', Text::_('COM_CONTENT_ARTICLE_CONTENT')); ?>
                <?php echo $this->form->renderField('title'); ?>

                <?php if (is_null($this->item->id)) : ?>
                    <?php echo $this->form->renderField('alias'); ?>
                <?php endif; ?>

                <?php echo $this->form->getInput('articletext'); ?>

                <?php if ($this->captchaEnabled) : ?>
                    <?php echo $this->form->renderField('captcha'); ?>
                <?php endif; ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php if ($params->get('show_urls_images_frontend')) : ?>
                <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'images', Text::_('COM_CONTENT_IMAGES_AND_URLS')); ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?php echo $this->form->renderField('image_intro', 'images'); ?>
                            <?php echo $this->form->renderField('image_intro_alt', 'images'); ?>
                            <?php echo $this->form->renderField('image_intro_alt_empty', 'images'); ?>
                            <?php echo $this->form->renderField('image_intro_caption', 'images'); ?>
                            <?php echo $this->form->renderField('float_intro', 'images'); ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <?php echo $this->form->renderField('image_fulltext', 'images'); ?>
                            <?php echo $this->form->renderField('image_fulltext_alt', 'images'); ?>
                            <?php echo $this->form->renderField('image_fulltext_alt_empty', 'images'); ?>
                            <?php echo $this->form->renderField('image_fulltext_caption', 'images'); ?>
                            <?php echo $this->form->renderField('float_fulltext', 'images'); ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <?php echo $this->form->renderField('urla', 'urls'); ?>
                            <?php echo $this->form->renderField('urlatext', 'urls'); ?>
                            <div class="mb-3">
                                <?php echo $this->form->getInput('targeta', 'urls'); ?>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <?php echo $this->form->renderField('urlb', 'urls'); ?>
                            <?php echo $this->form->renderField('urlbtext', 'urls'); ?>
                            <div class="mb-3">
                                <?php echo $this->form->getInput('targetb', 'urls'); ?>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <?php echo $this->form->renderField('urlc', 'urls'); ?>
                            <?php echo $this->form->renderField('urlctext', 'urls'); ?>
                            <div class="mb-3">
                                <?php echo $this->form->getInput('targetc', 'urls'); ?>
                            </div>
                        </div>
                    </div>

                <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php endif; ?>

            <?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

            <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'publishing', Text::_('COM_CONTENT_PUBLISHING')); ?>

                <?php echo $this->form->renderField('transition'); ?>
                <?php echo $this->form->renderField('catid'); ?>
                <?php echo $this->form->renderField('tags'); ?>
                <?php echo $this->form->renderField('note'); ?>
                <?php if ($params->get('save_history', 0)) : ?>
                    <?php echo $this->form->renderField('version_note'); ?>
                <?php endif; ?>
                <?php if ($params->get('show_publishing_options', 1) == 1) : ?>
                    <?php echo $this->form->renderField('created_by_alias'); ?>
                <?php endif; ?>

                <?php if ($this->item->params->get('access-change')) : ?>
                    <?php echo $this->form->renderField('state'); ?>
                    <?php echo $this->form->renderField('featured'); ?>

                    <?php if ($params->get('show_publishing_options', 1) == 1) : ?>
                        <?php echo $this->form->renderField('featured_up'); ?>
                        <?php echo $this->form->renderField('featured_down'); ?>
                        <?php echo $this->form->renderField('publish_up'); ?>
                        <?php echo $this->form->renderField('publish_down'); ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php echo $this->form->renderField('access'); ?>

                <?php if (is_null($this->item->id)) : ?>
                    <div class="form-text text-muted"><?php echo Text::_('COM_CONTENT_ORDERING'); ?></div>
                <?php endif; ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php if (Multilanguage::isEnabled()) : ?>
                <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'language', Text::_('JFIELD_LANGUAGE_LABEL')); ?>
                    <?php echo $this->form->renderField('language'); ?>
                <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php else : ?>
                <?php echo $this->form->renderField('language'); ?>
            <?php endif; ?>

            <?php if ($params->get('show_publishing_options', 1) == 1) : ?>
                <?php echo HTMLHelper::_('uitab.addTab', $this->tab_name, 'metadata', Text::_('COM_CONTENT_METADATA')); ?>
                    <?php echo $this->form->renderField('metadesc'); ?>
                    <?php echo $this->form->renderField('metakey'); ?>
                <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php endif; ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

            <input type="hidden" name="task" value="">
            <input type="hidden" name="return" value="<?php echo $this->return_page; ?>">
            <?php echo HTMLHelper::_('form.token'); ?>
        </fieldset>
        <div class="mb-2 mt-2">
            <button type="button" class="btn btn-primary" data-submit-task="article.apply">
                <span class="icon-check" aria-hidden="true"></span>
                <?php echo Text::_('JSAVE'); ?>
            </button>
            <button type="button" class="btn btn-primary" data-submit-task="article.save">
                <span class="icon-check" aria-hidden="true"></span>
                <?php echo Text::_('JSAVEANDCLOSE'); ?>
            </button>
            <?php if ($this->showSaveAsCopy) : ?>
                <button type="button" class="btn btn-primary" data-submit-task="article.save2copy">
                    <span class="icon-copy" aria-hidden="true"></span>
                    <?php echo Text::_('JSAVEASCOPY'); ?>
                </button>
            <?php endif; ?>
            <button type="button" class="btn btn-danger" data-submit-task="article.cancel">
                <span class="icon-times" aria-hidden="true"></span>
                <?php echo Text::_('JCANCEL'); ?>
            </button>
            <?php if ($params->get('save_history', 0) && $this->item->id) : ?>
                <?php echo $this->form->getInput('contenthistory'); ?>
            <?php endif; ?>
        </div>
    </form>
</div>
