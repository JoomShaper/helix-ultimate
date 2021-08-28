<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Document;

defined('_JEXEC') or die();

use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;

/**
 * Document parser class
 *
 * @since   1.0.0
 */
class Parser extends HtmlDocument
{
	/**
	 * Template Tags
	 *
	 * @var		string	$_template_tags
	 * @since	1.0.0
	 */
	protected $_template_tags;

	/**
	 * HTML Document object
	 *
	 * @var		object	$doc
	 * @since	1.0.0
	 */
	private $doc = null;

	/**
	 * Joomla! Application object
	 *
	 * @var		object	$app
	 * @since	1.0.0
	 */
	private $app = null;

	/**
	 * Constructor function
	 *
	 * @param	object	$params
	 * @param	array	$options
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function __construct($params, $options = array())
	{
		parent::__construct($options);
		parent::parse($params);

		$this->app = Factory::getApplication();
		$this->doc = Factory::getDocument();

		$this->flushToJS();
	}

	/**
	 * Parse Template
	 *
	 *
	 * @return	array
	 * @since	1.0.0
	 */
	public function parseTemplate()
	{
		$replace = array();
		$with = array();

		foreach ($this->_template_tags as $jdoc => $args)
		{
			$replace[] = $jdoc;
			$with[] = $this->getBuffer($args['type'], $args['name'], $args['attribs']);
		}

		return array
		(
			'replace' => $replace,
			'with' => $with
		);
	}

	/**
	 * Flush to JS
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function flushToJS()
	{
		$contents = $this->parseTemplate();

		// $this->doc->addScriptDeclaration("var templateReWi = '" . json_encode($contents) . "';");
	}
}
