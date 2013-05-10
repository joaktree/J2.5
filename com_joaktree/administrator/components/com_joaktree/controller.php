<?php
/**
 * Joomla! component Joaktree
 * file		Joaktree Controller - controller.php
 *
 * @version	1.4.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

// Submenu view
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_CONTROLPANEL'), 'index.php?option=com_joaktree');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_APPLICATIONS'), 'index.php?option=com_joaktree&view=jt_applications');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_FAMILYTREES'), 'index.php?option=com_joaktree&view=jt_familytree');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_PERSONS'), 'index.php?option=com_joaktree&view=jt_admin');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_MAPS'), 'index.php?option=com_joaktree&view=jt_maps');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_PERSON_NAMEDISPLAY'), 'index.php?option=com_joaktree&view=jt_settings&layout=personname');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_PERSON_EVENTDISPLAY'), 'index.php?option=com_joaktree&view=jt_settings&layout=personevent');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_RELATION_EVENTDISPLAY'), 'index.php?option=com_joaktree&view=jt_settings&layout=relationevent');
JSubMenuHelper::addEntry(JText::_('JT_SUBMENU_THEMES'), 'index.php?option=com_joaktree&view=jt_themes');

class JoaktreeController extends JController {

	function __construct() {
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'default');
		}	
		
		parent::__construct();		
	}

	function display() {
		parent::display();
	}
}
?>