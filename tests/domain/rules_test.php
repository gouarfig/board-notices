<?php

use fq\boardnotices\domain\rules;

class rules_test extends PHPUnit_Framework_TestCase
{
	private function getRootFolder()
	{
		return dirname(__FILE__) . '/../../../../../';
	}

    public function testInstance()
    {
        $rules = new rules($this->getRootFolder());
        $this->assertThat($rules, $this->logicalNot($this->equalTo(null)));

		return $rules;
    }

	/**
	 * @depends testInstance
	 * @param fq\boardnotices\domain\rules $rules
	 */
	public function testGetDefinedRules($rules)
	{
		global $phpbb_container;

		$phpbb_container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
		$this->assertThat($phpbb_container, $this->logicalNot($this->equalTo(null)), '$phpbb_container is null');

		$phpbb_container->method('get')->will($this->returnCallback(array($this, 'ContainerBuilderGet')));

		$anniversary = $phpbb_container->get("fq.boardnotices.rules.anniversary");
		$this->assertThat($anniversary, $this->logicalNot($this->equalTo(null)), '$anniversary is null');
		$this->assertThat($anniversary->getDisplayName(), $this->logicalNot($this->equalTo(null)));

		$definedRules = $rules->getDefinedRules();
	}

	public function ContainerBuilderGet($name)
	{
		$name = str_replace('.', '\\', $name);
//		$user = new \phpbb\user('\phpbb\datetime');
//		$get = new $name($user);
		$get = $this->getMockBuilder($name)->disableOriginalConstructor()->getMock();
		$get->method('getDisplayName')->willReturn('My Name');
		return $get;
	}
}
?>
