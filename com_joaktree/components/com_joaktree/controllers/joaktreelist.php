<?php
/**
 * Joomla! component Joaktree
 * file		front end joaktreelist controller - joaktreelist.php
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

class JoaktreeControllerJoaktreelist extends JoaktreeController {
	function __construct() {
		// first check token
		JRequest::checkToken() or die( 'Invalid Token' );
		
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'joaktreelist');
		}
		
		parent::__construct();
		
		$this->registerTask( 'save'  ,     'save' );
		$this->registerTask( 'unpublish'  ,     'publish' );
		$this->registerTask( 'updateLiving'  ,     'living' );
		$this->registerTask( 'updatePage'  ,     'page' );
	}
	
	function save() {
		$model = $this->getModel('joaktreelist');
		
		$link = 'index.php?option=com_joaktree&view=joaktreelist';
		$this->setRedirect(Jroute::_($link), $msg);
	}

	function publish() {
		$model = $this->getModel('joaktreelist');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=joaktreelist';
		$this->setRedirect(Jroute::_($link), $msg);
	}

	function living() {
		$model = $this->getModel('joaktreelist');
		
		$msg = $model->living();
		
		$link = 'index.php?option=com_joaktree&view=joaktreelist';
		$this->setRedirect(Jroute::_($link), $msg);
	}

	function page() {
		$model = $this->getModel('joaktreelist');
		
		$msg = $model->page();
		
		$link = 'index.php?option=com_joaktree&view=joaktreelist';
		$this->setRedirect(Jroute::_($link), $msg);
	}
}
?>