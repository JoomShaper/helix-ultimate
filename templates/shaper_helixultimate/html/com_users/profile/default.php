<?php
/**
* @package     Joomla.Site
* @subpackage  com_users
*
* @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;
?>
<div class="profile<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-md-6">
			<?php echo $this->loadTemplate('core'); ?>
		</div>
		<div class="col-md-6">
			<?php echo $this->loadTemplate('params'); ?>
		</div>
		<div class="col-md-12">
			<div class="row">
				<?php echo $this->loadTemplate('custom'); ?>
			</div>
		</div>
	</div>
</div>
