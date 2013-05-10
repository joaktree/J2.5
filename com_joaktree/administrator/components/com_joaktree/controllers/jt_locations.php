<?php
/**
 * Joomla! component Joaktree
 * file		jt_locations
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

class JoaktreeControllerJt_locations extends JoaktreeController {
	function __construct() {
		// check token first
		JRequest::checkToken() or die( 'Invalid Token' );

		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'jt_locations');
		}
		
		parent::__construct();
		
		$this->registerTask( 'apply'           , 'save');		
		$this->registerTask( 'purgelocations', 'purge' );
	}

	public function edit() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid  = (int) $cids[0];
		JRequest::setVar( 'id', $cid  );
		
		JRequest::setVar( 'view', 'jt_location' );
		JRequest::setVar( 'layout', 'form'  );
				
		parent::display();
	}
		
	public function save() {
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		
		$model = $this->getModel('jt_location');
		$msg = $model->save($form);
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	public function purge() {		
		$model 	= $this->getModel('jt_locations');

		$msg 	= $model->purgeLocations();
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	public function geocode() {		
		$model 	= $this->getModel('jt_locations');

		$msg 	= $model->geocode();
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	public function resetlocation() {		
		$model 	= $this->getModel('jt_locations');

		$msg 	= $model->resetlocation();
		$link 	= 'index.php?option=com_joaktree&view=jt_locations';			
		
		$this->setRedirect($link, $msg);
	}
	
	
}
?>