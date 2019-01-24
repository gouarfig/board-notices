<?php

namespace fq\boardnotices\tests\rules;

class rule_test_base extends \phpbb_test_case
{
	protected function getRootFolder()
	{
		return dirname(__FILE__) . '/../../../../../';
	}

	protected function getUser()
	{
		$phpbb_root_path = $this->getRootFolder();
		$language_file_loader = new \phpbb\language\language_file_loader($phpbb_root_path, 'php');
		$language = new \phpbb\language\language($language_file_loader);
		$user = new \phpbb\user($language, '\phpbb\datetime');
		return $user;
	}

	/**
	 * @return \fq\boardnotices\service\serializer $serializer
	 */
	protected function getSerializer()
	{
		static $serializer;
		if (is_null($serializer))
		{
			$serializer = new \fq\boardnotices\service\serializer();
		}
		return $serializer;
	}

}
