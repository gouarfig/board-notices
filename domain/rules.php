<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\domain;

use \fq\boardnotices\service\constants;

/**
 * Board Notices Manager
 * Rules management
 */
class rules
{

	/** @var string $root_path */
	private $root_path;
	/** @var \fq\boardnotices\rules\rule_interface[] $rules */
	private $rules;
	private $rules_loaded = false;
	private $not_rule_files = array(
		'.',
		'..',
		'rule.php',
		'rule_base.php',
		'rule_interface.php',
	);
	private $default_order = array(
		'logged_in',
		'birthday',
		'anniversary',
		'date',
		'has_never_visited',
		'has_not_visited_for',
		'has_never_posted',
		'has_not_posted_for',
		'has_posted_exactly',
		'has_posted_less',
		'has_posted_more',
		'has_never_posted_in_forum',
		'has_posted_in_forum',
		'on_board_index',
		'in_forum',
		'language',
		'style',
		'rank',
		'in_default_usergroup',
		'in_usergroup',
		'not_in_usergroup',
	);
	// Hide obsolete rules
	private $hide = array(
		'not_logged_in',
		// This one is not ready to be used yet
		// 'has_not_visited_for',
	);

	/**
	 * Constructor
	 *
	 * @param string $root_path
	 */
	public function __construct($root_path)
	{
		$this->root_path = $root_path;
	}

	/**
	 * Returns a list of defined rule names
	 *
	 * @return array
	 */
	public function getDefinedRules()
	{
		$rule_names = array();
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		foreach ($this->default_order as $rule_name)
		{
			if (isset($this->rules[$rule_name]))
			{
				$rule_names[$rule_name] = $this->getRuleDisplayValues($rule_name);
			}
		}
		// Add the remaining ones not on the order list
		foreach ($this->rules as $rule_name => $rule)
		{
			if (!isset($rule_names[$rule_name]))
			{
				$rule_names[$rule_name] = $this->getRuleDisplayValues($rule_name);
			}
		}

		return $rule_names;
	}

	/**
	 * Returns true if the rule has multiple parameters
	 *
	 * @param string $rule_name
	 * @return boolean
	 */
	public function ruleHasMultipleParameters($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		return isset($this->rules[$rule_name]) ? $this->rules[$rule_name]->hasMultipleParameters() : false;
	}

	/**
	 * Returns the type of the rule
	 *
	 * @param string $rule_name
	 * @return string|string[]
	 */
	public function getRuleType($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		return isset($this->rules[$rule_name]) ? $this->rules[$rule_name]->getType() : '';
	}

	/**
	 * Returns the possible values for the parameters of the rule
	 * @return mixed|mixed[]
	 */
	public function getRuleValues($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		return isset($this->rules[$rule_name]) ? $this->rules[$rule_name]->getPossibleValues() : '';
	}


	/**
	 * Returns the default values for the parameters of the rule
	 * @return mixed|mixed[]
	 */
	public function getRuleDefaultValue($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		return isset($this->rules[$rule_name]) ? $this->rules[$rule_name]->getDefault() : '';
	}

	/**
	 * Returns a list of variables defined by the rule
	 *
	 * @param string $rule_name
	 * @return string[]
	 */
	public function getAvailableVars($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		$available_vars = array();
		if (isset($this->rules[$rule_name]))
		{
			$vars = $this->rules[$rule_name]->getAvailableVars();
			if (!empty($vars))
			{
				foreach ($vars as $variable)
				{
					$available_vars[] = '{' . $variable . '}';
				}
			}
		}
		return $available_vars;
	}

	private function getRulesFolder()
	{
		$folder = $this->root_path . constants::$RULES_FOLDER;
		return $folder;
	}

	private function loadRules()
	{
		global $phpbb_container;

		$this->rules = array();
		$classes = $this->getRulesClassesList($this->getRulesFolder());
		if (empty($classes))
		{
			return;
		}
		foreach ($classes as $entry)
		{
			if (!in_array($entry, $this->hide))
			{
				try
				{
					$rule = $phpbb_container->get(constants::$RULES_CLASS_PREFIX . ".$entry");
					$this->rules[$entry] = $rule;
				} catch (\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $exc)
				{
					// The installation is obviously corrupted, but should we bother the user with it?
				}
			}
		}
		$this->rules_loaded = true;
	}

	/**
	 * Returns a list of rule files
	 *
	 * @param string $folder
	 * @return string[]
	 */
	private function getRulesClassesList($folder)
	{
		$classes = array();
		if ($rulesDirectory = dir($folder))
		{
			while (false !== ($entry = $rulesDirectory->read()))
			{
				if (!in_array($entry, $this->not_rule_files))
				{
					$entry = str_replace(constants::$RULES_FILE_EXTENSION, '', $entry);
					$classes[] = $entry;
				}
			}
			$rulesDirectory->close();
		}
		return $classes;
	}

	/**
	 * @param string $rule_name
	 * @return string|string[]
	 */
	private function getRuleDisplayValues($rule_name)
	{
		$displayName = $this->rules[$rule_name]->getDisplayName();
		$displayExplain = $this->rules[$rule_name]->getDisplayExplain();
		$displayUnit = $this->rules[$rule_name]->getDisplayUnit();

		if (empty($displayExplain) && empty($displayUnit))
		{
			return $displayName;
		}
		else
		{
			if (is_array($displayName))
			{
				$displayName[constants::$RULE_DISPLAY_EXPLAIN] = $displayExplain;
				$displayName[constants::$RULE_DISPLAY_UNIT] = $displayUnit;
				return $displayName;
			}
			else
			{
				return array(
					constants::$RULE_DISPLAY_NAME => $displayName,
					constants::$RULE_DISPLAY_EXPLAIN => $displayExplain,
					constants::$RULE_DISPLAY_UNIT => $displayUnit
				);
			}
		}
	}

}
