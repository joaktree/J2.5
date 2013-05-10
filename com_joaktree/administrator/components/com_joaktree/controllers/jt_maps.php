<?php
/**
 * Joomla! component Joaktree
 * file		jt_maps
 *  
 * @version	1.4.2
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JoaktreeControllerJt_maps extends JoaktreeController {
	function __construct() {
		// check token first
		JRequest::checkToken() or die( 'Invalid Token' );
		
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'jt_maps');
		}
		
		parent::__construct();
		
		$this->registerTask( 'add'             , 'edit' );
		$this->registerTask( 'remove'          , 'delete' );		
	}

	public function edit() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid  = (int) $cids[0];
		JRequest::setVar( 'id', $cid  );
		
		JRequest::setVar( 'view', 'jt_map' );
		JRequest::setVar( 'layout', 'form'  );
				
		parent::display();
	}

	public function delete() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$model = $this->getModel('jt_map');
		
		$msg 	= $model->delete($cids);
		
		$link = 'index.php?option=com_joaktree&view=jt_maps';
		$this->setRedirect($link, $msg);
	}

	public function apply() {
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		
		$model = $this->getModel('jt_map');
		$msg = $model->save($form);
		
		$link = 'index.php?option=com_joaktree&view=jt_map&layout=form&id='.$form['id'];
		$this->setRedirect($link, $msg);
	}
	
	public function save() {
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		
		$model = $this->getModel('jt_map');
		$msg = $model->save($form);
		
		$link = 'index.php?option=com_joaktree&view=jt_maps';
		$this->setRedirect($link, $msg);
	}

	public function cancel() {
		$link = 'index.php?option=com_joaktree&view=jt_maps';
		$this->setRedirect($link);
	}
	
	public function locations() {
		$link = 'index.php?option=com_joaktree&view=jt_locations';
		$this->setRedirect($link);
	}
}
?>