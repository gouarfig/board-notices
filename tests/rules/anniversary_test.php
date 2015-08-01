<?php

namespace fq\boardnotices\tests\rules;

class anniversary_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('fq/boardnotices');
	}

	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$rule = new \fq\boardnotices\rules\anniversary($user);
		$this->assertThat($rule, $this->logicalNot($this->equalTo(null)));
	}
}
