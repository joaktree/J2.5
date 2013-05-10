<?php
/**
 * Joomla! component Joaktree
 * file		front end controller.pjp
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

jimport('joomla.application.component.controller');

/**
 * Joaktree Component Controller
 */
class JoaktreeController extends JController {
	function display() {
		// Make sure we have a default view
		if( !JRequest::getCmd( 'view' )) {
			    JRequest::setVar('view', 'joaktree' );
		}
		parent::display();
	}
}
?>