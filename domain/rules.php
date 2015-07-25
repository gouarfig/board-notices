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

	public function getDefinedRules()
	{
		global $phpbb_container;

		$rules = array();
		$folder = $this->getRulesFolder();
		if (is_dir($folder))
		{
			$classes = $this->getRulesClassesList($folder);
			foreach ($classes as $entry)
			{
				try
				{
					$rule = $phpbb_container->get("fq.boardnotices.rules.$entry");
					$rules[$entry] = $rule->getDisplayName();
				} catch (\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $exc)
				{
					// The installation is obviously corrupted, but should we bother the user with it?
				}
			}
		} else
		{
			$rules = array("Cannot list files under folder " . $folder);
		}
		return $rules;
	}

}
