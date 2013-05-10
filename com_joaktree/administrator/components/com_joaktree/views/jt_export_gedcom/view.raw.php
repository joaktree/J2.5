<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_export_gedcom view - view.raw.php
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

class JoaktreeViewJt_export_gedcom extends JView {

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{	
		$items			= $this->get( 'Gedcom' );
		$tpl			= 'raw';
		$this->assignRef( 'items',  $items );		
		parent::display($tpl);
	}

}
