<?php 

/** Module showing last update of Joaktree Family
* mod_joaktree_show_update
*/
// no direct access
defined('_JEXEC') or die('Restricted access'); 

require_once (dirname(__FILE__).'/helper.php');

//$modHelper = new modJoaktreeShowUpdateHelper();

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_joaktree_show_update', $params->get('layout', 'default'));


//$update_date = modJoaktreeShowUpdateHelper::showlatestupdate();








?>
