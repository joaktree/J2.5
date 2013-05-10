<?php
/**
 * Joomla! component Joaktree
 * file		jt_themes
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

class JoaktreeControllerJt_themes extends JoaktreeController {
	function __construct() {
		// check token first
		JRequest::checkToken() or die( 'Invalid Token' );
		
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'jt_themes');
		}
		
		parent::__construct();
		
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'remove', 'delete' );		
		$this->registerTask( 'apply', 'save');
	}

	public function setDefault() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$model = $this->getModel('jt_themes');
		$msg   = JText::_('');
		
		$cid  = (int) $cids[0];
		$msg = $msg . $model->setDefault($cid);
		
		$link = 'index.php?option=com_joaktree&view=jt_themes';
		$this->setRedirect($link, $msg);
	}
		
	public function edit() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid  = (int) $cids[0];
		JRequest::setVar( 'id', $cid  );
		
		JRequest::setVar( 'view', 'jt_theme' );
		JRequest::setVar( 'layout', 'form'  );
				
		parent::display();
	}
	
	public function delete() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$model 	= $this->getModel('jt_theme');
		$msgdeleted = false;
		$msgnotdeleted = false;
		
		foreach ($cids as $cid_num => $cid) {
			$id  = (int) $cid;
			$ret = $model->delete($id);
			
			if (!$ret) {
				$msgnotdeleted = true;
			} else {
				$msgdeleted = true;
			}
		}
		
		if ($msgdeleted) {
			$msg .= JText::_('JTTHEME_MESSAGE_DELETED').'; ';
		}
		
		if ($msgnotdeleted) {
			$msg .= JText::_('JTTHEME_MESSAGE_NOTDELETED');
		}
		
		$link = 'index.php?option=com_joaktree&view=jt_themes';
		$this->setRedirect($link, $msg);
	}
	
	public function save() {
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		
		$model = $this->getModel('jt_theme');
		$msg = $model->save($form);
		
		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
				$cid  = (int) $cids[0];
				$link = 'index.php?option=com_joaktree&view=jt_theme&layout=form&id='.$cid;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_joaktree&view=jt_themes';
				break;
		}
		
		$this->setRedirect($link, $msg);
	}
	
	public function edit_css() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid  = (int) $cids[0];
		JRequest::setVar( 'id', $cid  );
		
		JRequest::setVar( 'view', 'jt_theme' );
		JRequest::setVar( 'layout', 'editcss'  );
				
		parent::display();
	}
	
}
?>