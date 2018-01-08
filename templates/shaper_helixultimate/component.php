<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');
$input = JFactory::getApplication()->input;
$option = $input->get('option');
$view = $input->get('view');
$layout = $input->get('layout');
$tmpl = $input->get('tmpl');
$edit = ($option == 'com_sppagebuilder' && $view == 'form' && $layout == 'edit' && $tmpl == 'component');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  $doc = JFactory::getDocument();

  if($favicon = $this->params->get('favicon')) {
    $doc->addFavicon( JURI::base(true) . '/' .  $favicon);
  } else {
    $doc->addFavicon( $this->baseurl . '/templates/'. $this->template .'/images/favicon.ico' );
  }
  ?>

  <jdoc:include type="head" />
  <?php if(!$edit) { ?>
    <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/media/jui/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/frontend-edit.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
  <?php } ?>
</head>
<body class="contentpane">
  <?php if(!$edit) { ?>
    <jdoc:include type="message" />
  <?php } ?>
  <jdoc:include type="component" />
</body>
</html>
