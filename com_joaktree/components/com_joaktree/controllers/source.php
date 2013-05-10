<?php
/**
 * Joomla! component Joaktree
 * file		front end source controller - source.php
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

class JoaktreeControllerSource extends JoaktreeController {
	function __construct() {
		// first check token
		JRequest::checkToken() or die( 'Invalid Token' );
		
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'source');
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
						.'&view=source'
						.'&layout=form_source'
						.'&appId='.$appId
						.'&sourceId='.$cids[0];

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
						.'&view=sources'
						.'&appId='.$appId;
						
		if ($action == 'select') {
			$link .= '&tmpl=component'
					.'&action='.$action;
		}
		
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function save() {
		$model = $this->getModel('source');
		
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		$appId 	= (int) JRequest::getInt('appId');
		$action = JRequest::getCmd( 'action' );
		
		$ret = $model->save($form);
		
		if ($ret) {
			$link =  'index.php?option=com_joaktree'
					.'&view=sources'
					.'&appId='.$appId
					.'&retId='.$ret;
			
			if ($action == 'select') {
				$link .= '&tmpl=component'
						.'&action='.$action;
			}
								
			$msg = '';
		} else {
			$link =  'index.php?option=com_joaktree'
					.'&view=sources'
					.'&appId='.$appId;
			$msg = JText::_('JT_NOTAUTHORISED');	
		}
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function delete() {
		$model = $this->getModel('source');
		
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		$appId 	= (int) JRequest::getInt('appId');
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		$ret = $model->delete($appId, $cids[0]);
		
		if ($ret) {
			$msg = JText::sprintf('JT_DELETED' , $ret);
		} else {
			$msg = JText::_('JT_NOTAUTHORISED');
		}
		
		$link =  'index.php?option=com_joaktree'
				.'&view=source'
				.'&appId='.$appId;
		$this->setRedirect(Jroute::_($link), $msg);
	}	
}
?>