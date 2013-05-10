<?php
/**
 * Joomla! component Joaktree
 * file		jt_familytree
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

class JoaktreeControllerJt_familytree extends JoaktreeController {
	function __construct() {
		// check token first
		JRequest::checkToken() or die( 'Invalid Token' );
		
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'jt_familytree');
		}
		
		parent::__construct();
		
		$this->registerTask( 'saveassign'      , 'saveassign' );
		$this->registerTask( 'unpublish'       , 'publish' );
		$this->registerTask( 'add'             , 'edit' );
		$this->registerTask( 'remove'          , 'delete' );		
		$this->registerTask( 'apply'           , 'save');
	}

	public function saveassign() {
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );

		$model = $this->getModel('jt_tree');
		$msg = $model->save($form);
				
		$link = 'index.php?option=com_joaktree&view=jt_familytree&action=assign&treeId='.$form['id'];
		$this->setRedirect($link, $msg);		
	}

	public function publish() {
		$model = $this->getModel('jt_familytree');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=jt_familytree';
		$this->setRedirect($link, $msg);
	}

	public function edit() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid  = (int) $cids[0];
		JRequest::setVar( 'id', $cid  );
		
		JRequest::setVar( 'view', 'jt_tree' );
		JRequest::setVar( 'layout', 'form'  );
				
		parent::display();
	}

	public function delete() {
		$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$model = $this->getModel('jt_tree');
		
		$msg 	= $model->delete($cids);
		
		$link = 'index.php?option=com_joaktree&view=jt_familytree';
		$this->setRedirect($link, $msg);
	}

	public function save() {
		$form   = JRequest::getVar( 'jform', array(0), 'post', 'array' );
		
		$model = $this->getModel('jt_tree');
		$msg = $model->save($form);
		
		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$cids	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
				$cid  = (int) $cids[0];
				$link = 'index.php?option=com_joaktree&view=jt_tree&layout=form&id='.$cid;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_joaktree&view=jt_familytree';
				break;
		}

		$this->setRedirect($link, $msg);
	}

}
?>