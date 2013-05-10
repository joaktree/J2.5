<?php
/**
 * Joomla! component Joaktree
 * file		jt_admin.php
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

class JoaktreeControllerJt_admin extends JoaktreeController {
	function __construct() {
		// check token first
		JRequest::checkToken() or die( 'Invalid Token' );
		
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'jt_admin');
		}
		
		parent::__construct();
		
		$this->registerTask( 'save', 'save' );
		
		// three tasks for publishing
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'publishAll', 'publishAll' );
		$this->registerTask( 'unpublishAll', 'unpublishAll' );
		// three tasks for living
		$this->registerTask( 'updateLiving', 'living' );
		$this->registerTask( 'livingAll', 'livingAll' );
		$this->registerTask( 'notLivingAll', 'notLivingAll' );
		// three tasks for page switch
		$this->registerTask( 'updatePage', 'page' );
		$this->registerTask( 'pageAll', 'pageAll' );
		$this->registerTask( 'noPageAll', 'noPageAll' );		
		// three tasks for map switch
		$this->registerTask( 'mapStatAll', 'mapStatAll' );
		$this->registerTask( 'mapDynAll', 'mapDynAll' );
		$this->registerTask( 'noMapAll', 'noMapAll' );		
	}

	public function save() {
		$post	= JRequest::get('post');
		$model = $this->getModel('jt_admin');
		
		$msg = $model->save($post);		
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function publish() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->publish();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function publishAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->publishAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function unpublishAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->unpublishAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function living() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->living();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function livingAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->livingAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function notLivingAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->notLivingAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function page() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->page();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function pageAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->pageAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function noPageAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->noPageAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function mapStatAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->mapStatAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function mapDynAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->mapDynAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}

	public function noMapAll() {
		$model = $this->getModel('jt_admin');
		
		$msg = $model->noMapAll();
		
		$link = 'index.php?option=com_joaktree&view=jt_admin';
		$this->setRedirect($link, $msg);
	}
}
?>