<?php
/**
 * Joomla! component Joaktree
 * file		front end repository controller - repository.php
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

class JoaktreeControllerRepository extends JoaktreeController {
	function __construct() {
		// first check token
		JRequest::checkToken() or die( 'Invalid Token' );
		
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'repository');
		}
		
		parent::__construct();
	}

	function display() {
		$action = JRequest::getCmd( 'action' );
		
		if ($action == 'select') {
			JRequest::setVar('tmpl', 'component');
			JRequest::setVar('action', $action);
		}

		parent::display();
	}
	
	function edit() {
		$appId 	= (int) JRequest::getInt('appId');
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$action = JRequest::getCmd( 'action' );
		
		$link =  'index.php?option=com_joaktree'
						.'&view=repository'
						.'&layout=form_repository'
						.'&appId='.$appId
						.'&repoId='.$cids[0];

		if ($action == 'select') {
			$link .= '&tmpl=component'
					.'&action='.$action;
		}
		
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function cancel() {
		$appId 	= (int) JRequest::getInt('appId');
		$action = JRequest::getCmd( 'action' );
		
		$link = 'index.php?option=com_joaktree'
						.'&view=repositories'
						.'&appId='.$appId;
						
		if ($action == 'select') {
			$link .= '&tmpl=component'
					.'&action='.$action;
		}
						
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function save() {
		$model = $this->getModel('repository');
		
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		$appId 	= (int) JRequest::getInt('appId');
		$action = JRequest::getCmd( 'action' );
				 
		$ret = $model->save($form);
		
		if ($ret) {
			$link =  'index.php?option=com_joaktree'
					.'&view=repositories'
					.'&appId='.$appId
					.'&retId='.$ret;
			
			if ($action == 'select') {
				$link .= '&tmpl=component'
						.'&action='.$action;
			}
								
			$msg = '';
		} else {
			$link =  'index.php?option=com_joaktree'
					.'&view=repositories'
					.'&appId='.$appId;
			$msg = JText::_('JT_NOTAUTHORISED');	
		}
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function delete() {
		$model = $this->getModel('repository');
		
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		$appId 	= (int) JRequest::getInt('appId');
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		$msg = $model->delete($appId, $cids[0]);
		
		$link =  'index.php?option=com_joaktree'
				.'&view=repositories'
				.'&appId='.$appId;
		$this->setRedirect(Jroute::_($link), $msg);
	}	
}
?>