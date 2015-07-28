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

namespace fq\boardnotices\rules;

interface rule {
	public function getDisplayName();
	public function getType();
	public function getPossibleValues();
	public function isTrue($conditions);
	public function getAvailableVars();
	public function getTemplateVars();
}
