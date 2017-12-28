<?php

defined ('_JEXEC') or die('resticted aceess');

SpAddonsConfig::addonConfig(
	array( 
		'type'=>'repeatable', 
		'addon_name'=>'sp_testimonialpro',
		'category'=>'Helix 3',
		'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO'),
		'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_DESC'),
		'attr'=>array(
			'autoplay'=>array(
				'type'=>'select', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_AUTOPLAY'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_AUTOPLAY_DESC'),
				'values'=>array(
					1=>JText::_('JYES'),
					0=>JText::_('JNO'),
					),
				'std'=>1,
				),
			'arrows'=>array(
				'type'=>'select', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_SHOW_ARROWS'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_SHOW_ARROWS_DESC'),
				'values'=>array(
					1=>JText::_('JYES'),
					0=>JText::_('JNO'),
					),
				'std'=>1,
				),
			'class'=>array(
				'type'=>'text', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS_DESC'),
				'std'=> ''
				),
			'repetable_item'=>array(
				'type'=>'repeatable',
				'addon_name' =>'sp_testimonialpro_item',
				'title'=> 'Repetable', 
				'attr'=>  array(
					'title'=>array(
						'type'=>'text', 
						'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_ITEM_TITLE'),
						'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_ITEM_TITLE_DESC'),
						'std'=>'John Doe',
						),
					'avatar'=>array(
						'type'=>'media', 
						'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_CLIENT_IMAGE'),
						'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_CLIENT_IMAGE_DESC'),
						),
					'avatar_style'=>array(
						'type'=>'select', 
						'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_SHOW_ARROWS'),
						'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_SHOW_ARROWS_DESC'),
						'values'=>array(
							''=>JText::_('COM_SPPAGEBUILDER_ADDON_GLOBAL_STANDARD'),
							'sppb-img-rounded'=>JText::_('COM_SPPAGEBUILDER_ADDON_GLOBAL_ROUNDED'),
							'sppb-img-circle'=>JText::_('COM_SPPAGEBUILDER_ADDON_GLOBAL_CIRCLE'),
							),
						'std'=>'sppb-img-circle',
						),
					'message'=>array(
						'type'=>'editor', 
						'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_ITEM_TEXT'),
						'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_ITEM_TEXT_DESC'),
						'std'=> 'Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.'
						),
					'url'=>array(
						'type'=>'text', 
						'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_CLIENT_URL'),
						'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TESTIMONIAL_PRO_CLIENT_URL_DESC'),
						),
					)
				),
			)
		)
	);

