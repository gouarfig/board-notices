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
			ksort($this->rules);
		}
	}

	public function getDefinedRules()
	{
		$rule_names = array();
		if (!$this->rules_loaded)
		{
			$this->loadRules();
		}

		foreach ($this->rules as $name => $rule)
		{
			$rule_names[$name] = $rule->getDisplayName();
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

}
