<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree jt_locations - view.html.php
 *
 * @version	1.4.2
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

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.html.select' );

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewJT_locations extends JView {
	function display($tpl = null) {
	
		$app = JFactory::getApplication();				
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		$this->canDo	= JoaktreeHelper::getActions();
				
		// Get data from the model
		$this->items		= & $this->get( 'Data' );
		$this->pagination	= & $this->get( 'Pagination' );
		$this->mapSettings	= & $this->get( 'mapSettings' );
		$this->addToolbar();
		
		//Filter
		$context		= 'com_joaktree.jt_locations.list.';
		
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jln.value',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
		$filter_server		= $app->getUserStateFromRequest( $context.'filter_server',	'filter_server',	'',		'word' );
		$filter_status		= $app->getUserStateFromRequest( $context.'filter_status',	'filter_status',	'',		'word' );
		$search				= $app->getUserStateFromRequest( $context.'search',			'search',	'',	'string' );
		$search				= JString::strtolower( $search );
		
		// table ordering
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order'] = $filter_order;
		
		// search filter
		$this->lists['search']= $search;
				
		$select_attr = array();
		$select_attr['class'] = 'inputbox';
		$select_attr['size'] = '1';
		$select_attr['onchange'] = 'submitform( );';
		
		// server filter
		$server = array();
		$server[0]['value'] = 'A';
		$server[0]['text']  = JText::_('JT_FILTER_SERVER');       
		$server[1]['value'] = 'Y';
		$server[1]['text']  = JText::_('JT_FILTER_SERVER_YES'); 
		$server[2]['value'] = 'N';
		$server[2]['text']  = JText::_('JT_FILTER_SERVER_NO'); 
		$dummy	= JHTML::_('grid.state',  0 );
		$this->lists['server']	= JHTMLSelect::genericlist($server, 'filter_server', $select_attr, 'value', 'text', $filter_server );
		
		// geocoding status filter
		$status = array();
		$status[0]['value'] = 'A';
		$status[0]['text']  = JText::_('JT_FILTER_STATUS_ALL');       
		$status[1]['value'] = 'N';
		$status[1]['text']  = JText::_('JT_FILTER_STATUS_NO'); 
		$status[2]['value'] = 'Y';
		$status[2]['text']  = JText::_('JT_FILTER_STATUS_YES'); 
		$this->lists['status']	= JHTMLSelect::genericlist($status, 'filter_status', $select_attr, 'value', 'text', $filter_status );
				
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= JoaktreeHelper::getActions();
		
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTLOCATIONS_TITLE' ), 'location' );

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList();
			if (!empty($this->mapSettings->geocode)) {
				JToolBarHelper::custom( 'resetlocation', 'resetlocation', 'resetlocation', JText::_( 'JTLOCATIONS_BUTTON_RESET' ), true );
				JToolBarHelper::divider();
				JToolBarHelper::custom( 'geocode', 'geocode', 'geocode', JText::sprintf( 'JTLOCATIONS_BUTTON_GEOCODE', ucfirst($this->mapSettings->geocode) ), false );
			}
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::divider();
			//$bar = JToolBar::getInstance('toolbar');
			// explanation: $bar->appendButton('Standard', $icon, $alt, $task, $listSelect);
			//$bar->appendButton('purge', 'location', 'JTFAMTREE_TASK', 'purgeLocation', false);
			JToolBarHelper::custom( 'purgelocations', 'purgelocations', 'purgelocations', JText::_( 'JTLOCATIONS_BUTTON_PURGE' ), false );			
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
	}	

}
?>