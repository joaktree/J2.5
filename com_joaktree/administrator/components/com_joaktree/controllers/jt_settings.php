<?php
/**
 * Joomla! component Joaktree
 * file		jt_settings
 *
 * @version	1.4.0
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

class JoaktreeControllerJt_settings extends JoaktreeController {
	function __construct() {
		// first check token
		JRequest::checkToken() or die( 'Invalid Token' );

		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'jt_settings');
		}
		
		parent::__construct();
		
		$this->registerTask( 'unpublish'       , 'publish' );		
	}

	public function publish() {
		$layout = JRequest::getCmd('layout');
		$model = $this->getModel('jt_settings');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
	}
	
	public function save() {
		$layout = JRequest::getCmd('layout');
		$post	= JRequest::get('post');
		$model	= $this->getModel('jt_settings');
		$retmsg = '';
				
		// retrieve id
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		// We are only doing it when we have items to be updated
		if ((count( $cid ) > 0) and ($cid[0] > 0)) {			
			for ($i=0, $n=count( $cid ); $i < $n; $i++) {
				$post['id'] 	        = intval( $cid[$i] );
				
				$tmp = JRequest::getVar( 'access'.$post['id'], '', 'post', 'string' );
				$tmp = (int) substr($tmp, 0, 3);
				$post['access']	= $tmp;
				
				$tmp = JRequest::getVar( 'accessLiving'.$post['id'], '', 'post', 'string' );
				$tmp = (int) substr($tmp, 0, 3);
				$post['accessLiving']	= $tmp;
				
				$tmp = JRequest::getVar( 'altLiving'.$post['id'], '', 'post', 'string' );
				$tmp = (int) substr($tmp, 0, 3);
				$post['altLiving']	= $tmp;
							
				$code = JRequest::getVar( 'code'.$post['id'], '', 'post', 'string' );		
							
				$retmsg .= $model->store($post, $code).';&nbsp;';
			}
		}
		
		if (strlen($retmsg) > 0) {
			$msg = JText::_( 'JTSETTINGS_SAVE_ACCESSLEVEL' ).':&nbsp;'.$retmsg;
		} else {
			$msg = JText::_( 'JTSETTINGS_NO_SAVE_ACCESSLEVEL' );
		}
				
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link, $msg);
		
	}
	
	public function orderdown() {
		$this->setOrder(1);
	}

	public function orderup() {
		$this->setOrder(-1);
	}

	public function saveorder() {
		$this->setOrder(0);
	}
	
	private function setOrder($action) {
		$layout = JRequest::getCmd('layout');
		$model = $this->getModel('jt_settings');
		
		$msg = $model->setOrder($action, $layout);
		
		$link = 'index.php?option=com_joaktree&view=jt_settings&layout='.$layout;
		$this->setRedirect($link);
	}
}
?>