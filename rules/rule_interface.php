<?php
/**
*
* Board Notices Manager
*
* @version 1.0.0
* @copyright (c) Fred Quointeau
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace fq\boardnotices\rules;

interface rule_interface {
	public function getDisplayName();
	public function getDisplayExplain();
	public function getDisplayUnit();
	public function getType();
	public function getDefault();
	public function getPossibleValues();
	public function validateValues($values);
	public function isTrue($conditions);
	public function getAvailableVars();
	public function getTemplateVars();
}
