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
	private $default_order = array(
		'not_logged_in',
		'birthday',
		'anniversary',
		'date',
		'has_never_posted',
		'has_not_posted_for',
		'in_forum',
		'language',
		'style',
		'rank',
		'in_default_usergroup',
		'in_usergroup',
		'not_in_usergroup',
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
				if ($entry != "." && $entry != ".." && $entry != "rule.php")
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
				try
				{
					$rule = $phpbb_container->get("fq.boardnotices.rules.$entry");
					$this->rules[$entry] = $rule;
				} catch (\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $exc)
				{
					// The installation is obviously corrupted, but should we bother the user with it?
				}
			}
			$this->rules_loaded = true;
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
			$rule_names[$rule_name] = $this->rules[$rule_name]->getDisplayName();
		}
		// Add the remaining ones not on the order list
		foreach ($this->rules as $name => $rule)
		{
			if (!isset($rule_names[$name]))
			{
				$rule_names[$name] = $rule->getDisplayName();
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
