<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) 2015 Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\domain;

class rules
{

	private $root_path;
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
		'has_never_posted',
		'has_not_posted_for',
		'has_posted_exactly',
		'has_posted_less',
		'has_posted_more',
		'has_never_posted_in_forum',
		'has_posted_in_forum',
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
		'has_not_visited_for',
	);

	public function __construct($root_path)
	{
		$this->root_path = $root_path;
	}

	private function getRulesFolder()
	{
		$folder = $this->root_path . 'ext/fq/boardnotices/rules';
		return $folder;
	}

	private function getRulesClassesList($folder)
	{
		$classes = array();
		if ($handle = opendir($folder))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if (!in_array($entry, $this->not_rule_files))
				{
					$entry = str_replace('.php', '', $entry);
					$classes[] = $entry;
				}
			}
			closedir($handle);
		}
		return $classes;
	}

	private function loadRules()
	{
		global $phpbb_container;

		$this->rules = array();
		$folder = $this->getRulesFolder();
		if (is_dir($folder))
		{
			$classes = $this->getRulesClassesList($folder);
			foreach ($classes as $entry)
			{
				if (!in_array($entry, $this->hide))
				{
					try
					{
						$rule = $phpbb_container->get("fq.boardnotices.rules.$entry");
						$this->rules[$entry] = $rule;
					} catch (\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $exc)
					{
						// The installation is obviously corrupted, but should we bother the user with it?
					}
				}
			}
			$this->rules_loaded = true;
		}
	}

	private function getRuleDisplayValues($rule_name)
	{
		$displayName = $this->rules[$rule_name]->getDisplayName();
		$displayUnit = $this->rules[$rule_name]->getDisplayUnit();

		if (empty($displayUnit))
		{
			return $displayName;
		}
		else
		{
			if (is_array($displayName))
			{
				$displayName['display_unit'] = $displayUnit;
				return $displayName;
			}
			else
			{
				return array(
					'display_name' => $displayName,
					'display_unit' => $displayUnit);
			}
		}
	}

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

	public function getRuleType($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		return isset($this->rules[$rule_name]) ? $this->rules[$rule_name]->getType() : '';
	}

	public function getRuleValues($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		return isset($this->rules[$rule_name]) ? $this->rules[$rule_name]->getPossibleValues() : '';
	}

	public function getRuleDefaultValue($rule_name)
	{
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		return isset($this->rules[$rule_name]) ? $this->rules[$rule_name]->getDefault() : '';
	}

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

}
