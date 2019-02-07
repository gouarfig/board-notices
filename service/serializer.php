<?php

namespace fq\boardnotices\service;

/**
 * Use this class to avoid the use of serialize and unserialize functions (which are unsafe)
 */
class serializer
{
	private $lastJsonError = false;
	private $lastError = false;

	/**
	 * Encoding data into a JSON format (with 'json:' in front)
	 * @param mixed $data
	 * @return string
	 */
	public function encode($data)
	{
		$encoded = 'json:' . json_encode($data);
		$this->lastJsonError = json_last_error();
		return $encoded;
	}

	/**
	 * Data gets encoded in JSON for security reasons
	 * But this method also accepts serialized data for compatibility with boardnotices versions <0.4
	 * @param string $mixed
	 * @return mixed
	 */
	public function decode($string)
	{
		if (empty($string) || !is_string($string))
		{
			return $this->error();
		}
		if (substr($string, 0, 5) === 'json:')
		{
			$associativeArrays = true;
			$decoded = json_decode(substr($string, 5), $associativeArrays);
			$this->lastJsonError = json_last_error();
			return $decoded;
		}
		$decoded = $this->safeUnserialize($string);
		if (($decoded === false) && ($string !== 'b:0;'))
		{
			return $this->error();
		}
		return $decoded;
	}

	/**
	 * Indicates if the last encoding or decoding resulted in an error (and returned NULL)
	 *
	 * @return bool
	 */
	public function errorDetected()
	{
		return ($this->lastJsonError != JSON_ERROR_NONE) || ($this->lastError);
	}

	/**
	 * Always return null on error
	 */
	private function error()
	{
		$this->lastError = true;
		return null;
	}

	private function safeUnserialize($string)
	{
		$this->lastError = false;
		if (!is_string($string) || empty($string))
		{
			return $this->error();
		}
		try
		{
			$unserialized = $this->getSingleValue($string);
		}
		catch (\Exception $exception)
		{
			return $this->error();
		}
		return $unserialized;
	}

	/**
	 * @param string $string
	 * @param int &$pos = 0
	 */
	private function getSingleValue($string, &$pos = 0)
	{
		switch ($string[$pos])
		{
			case 'N':
				return $this->getNullValue($pos);

			case 'b':
				return $this->getBooleanValue($string, $pos);

			case 'i':
				return $this->getIntegerValue($string, $pos);

			case 's':
				return $this->getStringValue($string, $pos);

			case 'a':
				return $this->getArrayValue($string, $pos);

			default:
				throw new \Exception("Invalid serialized string: type code '{$string[$pos]}' unknown at position {$pos}.", 101);
		}
	}

	private function getSerializedValue($string, $start)
	{
		$end = strpos($string, ';', $start);
		if ($end > $start)
		{
			return substr($string, $start, $end - $start);
		}
		throw new \Exception("Invalid serialized string", 102);
	}

	/**
	 * @param string $string
	 * @param int $start
	 * @return int
	 */
	private function getSizeValue($string, $start)
	{
		$end = strpos($string, ':', $start);
		if ($end > $start)
		{
			return (int) substr($string, $start, $end - $start);
		}
		throw new \Exception("Invalid serialized string", 103);
	}

	/**
	 * null format is: 'N;'
	 * @param int $pos
	 */
	private function getNullValue(&$pos)
	{
		$pos += 2;
		return null;
	}

	/**
	 * boolean format is: 'b:0;' or 'b:1;'
	 * @param string $string
	 * @param int $pos
	 */
	private function getBooleanValue($string, &$pos)
	{
		$pos += 2;
		$rawValue = $this->getSerializedValue($string, $pos);
		$pos += 2;
		return $this->booleanValue($rawValue);
	}

	private function booleanValue($string)
	{
		if ($string == '0')
		{
			return false;
		}
		if ($string == '1')
		{
			return true;
		}
		throw new \Exception("Invalid serialized string", 111);
	}

	/**
	 * integer format is: 'i:10;'
	 * @param string $string
	 * @param int $pos
	 */
	private function getIntegerValue($string, &$pos)
	{
		$pos += 2;
		$rawValue = $this->getSerializedValue($string, $pos);
		$value = $this->integerValue($rawValue);
		$pos += strlen((string) $value) +1;
		return $value;
	}

	private function integerValue($string)
	{
		if (!is_numeric($string))
		{
			throw new \Exception("Invalid serialized string", 121);
		}
		return (int) $string;
	}

	/**
	 * string format is: 's:4:"toto";'
	 * @param string $string
	 * @param int $pos
	 */
	private function getStringValue($string, &$pos)
	{
		$pos += 2;
		$size = $this->getSizeValue($string, $pos);
		$pos += strlen((string) $size) +2;
		$value = substr($string, $pos, $size);
		$pos += (int) $size +2;
		return $value;
	}

	/**
	 * empty array format is: 'a:0:{}'
	 * simple array with only one int element: 'a:1:{i:0;i:10;}'
	 * array with 2 elements with indexes: 'a:2:{s:3:"int";i:0;s:3:"str";s:4:"toto";}'
	 * @param string $string
	 * @param int $pos
	 */
	private function getArrayValue($string, &$pos)
	{
		$array = array();
		$pos += 2;
		$size = $this->getSizeValue($string, $pos);
		if ($size == 0)
		{
			return array();
		}
		if ($size < 0)
		{
			throw new \Exception("Invalid serialized string", 131);
		}
		$pos += strlen((string) $size) +2;
		for ($element = 0; $element < $size; $element++)
		{
			$key = $this->getSingleValue($string, $pos);
			if (!is_scalar($key))
			{
				throw new \Exception("Invalid serialized string", 132);
			}
			$value = $this->getSingleValue($string, $pos);
			$array[$key] = $value;
		}
		$pos++;
		return $array;
	}
}
