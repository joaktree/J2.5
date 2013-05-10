<?php
/**
 * Joomla! 1.6 module Joaktree related items
 * file		JoaktreeHelper - helper.php
 *
 * @version	1.3
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Module linking articles to persons in Joaktree component
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$modHelper = new modJoaktreeRelatedItemsHelper();

$arlist = $modHelper->getArticleList();
$jtlist = $modHelper->getJoaktreeList();

if ((count($arlist) + count($jtlist))== 0 ) {
	return;
}

$showDate = $params->get('showDate', 0);

require(JModuleHelper::getLayoutPath('mod_joaktree_related_items'));
