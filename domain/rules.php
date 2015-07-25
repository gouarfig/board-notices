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

class rules {
	private $root_path;

	public function __construct($root_path) {
		$this->root_path = $root_path;
	}

	public function getDefinedRules()
	{
		global $phpbb_container;

		$rules = array();
		$folder = $this->root_path . 'ext/fq/boardnotices/rules';
		if (is_dir($folder))
		{
			if ($handle = opendir($folder)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != ".." && $entry != "rule.php") {
						$entry = str_replace('.php', '', $entry);
						try
						{
							$rule = $phpbb_container->get("fq.boardnotices.rules.$entry");
							$rules[$entry] = $rule->getDisplayName();
						}
						catch (\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $exc)
						{
						}
					}
				}
				closedir($handle);
			}
		} else {
			$rules = array("Cannot list files under folder " . $folder);
		}
		return $rules;
	}
}
