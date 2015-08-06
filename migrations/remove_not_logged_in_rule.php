<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class remove_not_logged_in_rule extends \phpbb\db\migration\migration
{

	static public function depends_on()
	{
		return array('\fq\boardnotices\migrations\new_logged_in_rule');
	}

	private function getFileToRemove()
	{
		return $this->phpbb_root_path . "ext/fq/boardnotices/rules/not_logged_in.php";
	}

	public function effectively_installed()
	{
		return !file_exists($this->getFileToRemove());
	}

	public function update_data()
	{
		$file_to_remove = $this->getFileToRemove();
		if (is_writable($file_to_remove))
		{
			@unlink($file_to_remove);
		}
		return array();
	}

}
