<?php

namespace fq\boardnotices\tests\rules;

use \fq\boardnotices\rules\rule_base;

/**
 * Simple rule to test rule_base
 */
class test_rule extends rule_base
{
	public function __construct(\fq\boardnotices\service\serializer $serializer)
	{
		$this->serializer = $serializer;
	}

	public function getType()
	{
		return null;
	}

	public function protectedGetArrayOfConditionsForMultipleIntegers($input)
	{
		return $this->getArrayOfConditionsForMultipleIntegers($input);
	}
}
