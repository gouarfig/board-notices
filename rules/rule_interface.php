<?php

namespace fq\boardnotices\rules;

interface rule_interface {
	/**
	 * @return boolean
	 */
	public function hasMultipleParameters();

	/**
	 * @return string
	 */
	public function getDisplayName();

	/**
	 * @return string
	 */
	public function getDisplayExplain();

	/**
	 * @return string|string[]
	 */
	public function getDisplayUnit();

	/**
	 * @return string|string[]
	 */
	public function getType();

	/**
	 * @return mixed
	 */
	public function getDefault();

	/**
	 * @return mixed
	 */
	public function getPossibleValues();

	/**
	 * @return boolean
	 */
	public function validateValues($values);

	/**
	 * @return boolean
	 */
	public function isTrue($conditions);

	/**
	 * @return array
	 */
	public function getAvailableVars();

	/**
	 * @return array
	 */
	public function getTemplateVars();
}
