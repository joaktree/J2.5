<?php
/**
 * Joomla! component Joaktree
 * file		administrator personform view - view.html.php
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

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

class JoaktreeViewPersonform extends JView {

	protected $form;
	protected $item;
	//protected $state;

	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{	
		// First load the treeId!
		$this->lists['treeId']		= $this->get( 'treeId' );
		
		// Load the parameters.
		$this->params	= JoaktreeHelper::getJTParams(true);
		if ($this->params->get('siteedit')) {	
			$this->canDo	= JoaktreeHelper::getActions();	
		} else {
			$this->canDo	= null;
		}

		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($this->params->get('theme')) );
		$document		= &JFactory::getDocument();
		$document->addScript( JoaktreeHelper::joaktreejs('toggle.js'));
		$this->lists['lineEnd'] = $document->_getLineEnd();
		
		// Initialiase variables.
		$this->form					= $this->get( 'Form' );
		$this->lists['appId']		= $this->get( 'applicationId' );
		$this->lists['appName']		= $this->get( 'applicationName' );
		
		$relationId					= $this->get( 'relationId' );
		if (isset($relationId)) {
			$this->relation					= $this->get( 'relation' );
			$this->lists['action']			= $this->get( 'action' );
		} 
		$this->lists['action'] = (isset($this->lists['action'])) ? $this->lists['action'] : 'new';				
		
		
		$this->picture			= $this->get( 'picture' );
		
		$personId = $this->get( 'personId' );
		if (isset($personId)) {				
			$this->lists['userAccess'] 	= $this->get( 'access' );
			$this->item					= $this->get('Item');
			$this->lists['indLiving']	= $this->item->living;				
		} else {			
			$this->lists['userAccess'] 	= true;
			$this->lists['indLiving']	= false;				
		}

		$patronym = $this->params->get('patronym', 0);
		$this->lists['indPatronym'] = ($patronym == 0) ? false : true;
		$familyname = $this->params->get('familyname', 0);
		$this->lists['indNamePreposition'] = ($familyname == 1) ? true : false;
			
		$this->lists[ 'CR' ] = JoaktreeHelper::getJoaktreeCR();
			
		if ($this->lists['userAccess']) {
			// set title
			if (isset($personId)) {
				$title = $this->item->firstName.' '.$this->item->familyName;
				$document->setTitle($title);
			}			
		}
		
		parent::display($tpl);
	}
}
