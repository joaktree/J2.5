<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_admin view - view.html.php
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

class JoaktreeViewJt_admin extends JView {
	function display($tpl = null) {
		
		$app = JFactory::getApplication();
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::script( JoaktreeHelper::jsfile() );		
		$this->addToolbar();
		
		// add script 
		$document 		= &JFactory::getDocument();
		$document->addScriptDeclaration($this->addScript());
 		
		// Get data from the model
		$items	     = & $this->get( 'Persons' );
		$trees	     = & $this->get( 'Trees' );
		$applications = JoaktreeHelper::getApplications();
		$pagination  = & $this->get( 'Pagination' );
		$lists['patronym'] 	= $this->get( 'patronymShowing' );
		$columns	= $this->get( 'columnSettings' );
		
		//Filter
		$context			= 'com_joaktree.jt_admin.list.';
		
		$filter_state		= $app->getUserStateFromRequest( $context.'filter_state',	'filter_state',		'',		'word' );
		$filter_living		= $app->getUserStateFromRequest( $context.'filter_living',	'filter_living',	'',		'word' );
		$filter_page		= $app->getUserStateFromRequest( $context.'filter_page',	'filter_page',		'',		'word' );
		$filter_map			= $app->getUserStateFromRequest( $context.'filter_map',		'filter_map',		'',		'int' );
		$filter_tree		= $app->getUserStateFromRequest( $context.'filter_tree',	'filter_tree',		'',		'int' );
		$filter_apptitle	= $app->getUserStateFromRequest( $context.'filter_apptitle','filter_apptitle',	'',		'int' );
		$filter_robots		= $app->getUserStateFromRequest( $context.'filter_robots',	'filter_robots',	'',		'int' );
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',	'filter_order',		'jpn.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir',	'',		'word' );
		$search1			= $app->getUserStateFromRequest( $context.'search1',		'search1',		'',		'string' );
		$search1			= JString::strtolower( $search1 );
		$search2			= $app->getUserStateFromRequest( $context.'search2',		'search2',		'',		'string' );
		$search2			= JString::strtolower( $search2 );
		$search3			= $app->getUserStateFromRequest( $context.'search3',		'search3',		'',		'string' );
		$search3			= JString::strtolower( $search3 );
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search1']= $search1;
		$lists['search2']= $search2;
		$lists['search3']= $search3;

		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		$select_attr = array();
		$select_attr['class'] = 'inputbox';
		$select_attr['size'] = '1';
		$select_attr['onchange'] = 'submitform( );';
		$value = 'value';
		$text = 'text';

		// living filter
		$living = array();
		$living[0][$value] = 'S';
		$living[0][$text]  = JText::_('JT_FILTER_LIVING');       
		$living[1][$value] = 'L';
		$living[1][$text]  = JText::_('JT_FILTER_VAL_LIVING'); 
		$living[2][$value] = 'D';
		$living[2][$text]  = JText::_('JT_FILTER_VAL_NOTLIVING'); 
      
		$lists['living']	= JHTMLSelect::genericlist($living, 'filter_living', $select_attr, $value, $text, $filter_living );
	   
		// page filter
		$page = array();
		$page[0][$value] = 'S';
		$page[0][$text]  = JText::_('JT_FILTER_PAGE');      
		$page[1][$value] = 'Y';
		$page[1][$text]  = JText::_('JT_FILTER_VAL_PAGE');
		$page[2][$value] = 'N';
		$page[2][$text]  = JText::_('JT_FILTER_VAL_NOPAGE'); 
				
		$lists['page']	= JHTMLSelect::genericlist($page, 'filter_page', $select_attr, $value, $text, $filter_page );
		
		// map filter
		$map = array();
		$map[0][$value] = -1;
		$map[0][$text]  = JText::_('JT_FILTER_MAP');      
		$map[1][$value] = 1;
		$map[1][$text]  = JText::_('JT_FILTER_VAL_STATMAP');
		$map[2][$value] = 2;
		$map[2][$text]  = JText::_('JT_FILTER_VAL_DYNMAP');
		$map[3][$value] = 0;
		$map[3][$text]  = JText::_('JT_FILTER_VAL_NOMAP'); 
				
		$lists['map']	= JHTMLSelect::genericlist($map, 'filter_map', $select_attr, $value, $text, $filter_map );
		// remove one neutral setting for using it to modify settings 
		$tmp = array_shift($map);
		$this->assignRef( 'maps'    ,	$map);
		
		// default family tree filter
		$tree = array();
		$tree[0][$value] = 0;
		$tree[0][$text]  = JText::_('JT_FILTER_TREE');
		
		for ($i = 1; $i <= count($trees); $i++) {
			$tree[$i][$value] = $trees[$i-1]->id;
			$tree[$i][$text]  = $trees[$i-1]->name;
		}
		
		$lists['tree']	= JHTMLSelect::genericlist($tree, 'filter_tree', $select_attr, $value, $text, $filter_tree );
		
		// application filter
		$appTitle = array();
		$appTitle[0][$value] = 0;
		$appTitle[0][$text]  = JText::_('JT_FILTER_APPLICATION'); 
		
		for ($i = 1; $i <= count($applications); $i++) {
			$appTitle[$i][$value] = $applications[$i-1]->value;
			$appTitle[$i][$text]  = $applications[$i-1]->text;
		}
		
		$lists['appTitle']	= JHTMLSelect::genericlist($appTitle, 'filter_apptitle', $select_attr, $value, $text, $filter_apptitle );
		
		// robots filter
		$robots = array();
		$robots[0][$value] = -1;
		$robots[0][$text]  = JText::_('JT_FILTER_ROBOTS');      
		$robots[1][$value] = 0;
		$robots[1][$text]  = JText::_('JT_ROBOT_USE_TREE');      
		$robots[2][$value] = 1;
		$robots[2][$text]  = JText::_('JGLOBAL_INDEX_FOLLOW');
		$robots[3][$value] = 2;
		$robots[3][$text]  = JText::_('JGLOBAL_NOINDEX_FOLLOW'); 
		$robots[4][$value] = 3;
		$robots[4][$text]  = JText::_('JGLOBAL_INDEX_NOFOLLOW'); 
		$robots[5][$value] = 4;
		$robots[5][$text]  = JText::_('JGLOBAL_NOINDEX_NOFOLLOW'); 
		
		$lists['robots']   = JHTMLSelect::genericlist($robots, 'filter_robots', $select_attr, $value, $text, $filter_robots );
		// save these settings for the page
		
		// remove one neutral setting for using it to modify settings 
		$tmp = array_shift($robots);
		$this->assignRef( 'robots'    ,	$robots);
		// end of filters
		
		$this->assignRef( 'items'     , $items );
		$this->assignRef( 'pagination',	$pagination);
		$this->assignRef( 'lists'     ,	$lists);
		$this->assignRef( 'columns'   ,	$columns);
		
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
		
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTPERSONS_TITLE' ), 'person' );

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom( 'publishAll', 'publish', 'publish', JText::_( 'JTPERSONS_BUTTON_PUBLISHALL' ), true );
			JToolBarHelper::custom( 'unpublishAll', 'unpublish', 'unpublish', JText::_( 'JTPERSONS_BUTTON_UNPUBLISHALL' ), true );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'livingAll', 'living', 'living', JText::_( 'JTPERSONS_BUTTON_LIVINGALL' ), true );
			JToolBarHelper::custom( 'notLivingAll', 'notliving', 'notliving', JText::_( 'JTPERSONS_BUTTON_NOTLIVINGALL' ), true );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'pageAll', 'page', 'page', JText::_( 'JTPERSONS_BUTTON_PAGEALL' ), true );
			JToolBarHelper::custom( 'noPageAll', 'nopage', 'nopage', JText::_( 'JTPERSONS_BUTTON_NOPAGEALL' ), true );
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::custom( 'mapStatAll', 'statmap', 'statmap', JText::_( 'JTPERSONS_BUTTON_STATMAPALL' ), true );
			JToolBarHelper::custom( 'mapDynAll', 'dynmap', 'dynmap', JText::_( 'JTPERSONS_BUTTON_DYNMAPALL' ), true );
			JToolBarHelper::custom( 'noMapAll', 'nomap', 'nomap', JText::_( 'JTPERSONS_BUTTON_NOMAPALL' ), true );
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
	}
	
	protected function addScript() {
		$script = array();
		$params  	= JComponentHelper::getParams('com_joaktree') ;
		$indCookie	= $params->get('indCookies', true);
		
		$script[] = "function jt_toggle(tag,col) { ";
		$script[] = "  var oEl, i, elements, cEl, num; ";
		$script[] = ($indCookie) ? "  var myCookie; " : " ";
		$script[] = "  elements = document.getElementById('editcell').getElements(tag); ";
		$script[] = "  cEl =  document.getElementById('footer'); ";
		$script[] = "  num = (cEl.getProperty('colspan')).toInt(); ";
		$script[] = "  for (i=0; i < elements.length; i++ ) { ";
		$script[] = "    if($(elements[i])){ ";
		$script[] = "      oEl = $(elements[i]); ";
		$script[] = "      if (oEl.hasClass('jt-hide-'+col)) { ";
		$script[] = "        oEl.removeClass('jt-hide-'+col); ";
		$script[] = "        oEl.addClass('jt-show-'+col); ";
		$script[] = "        num = num + 1; ";
		$script[] = ($indCookie) ? "        myCookie = Cookie.write('jt_'+col, '1', {duration: 0}); " : " ";
		$script[] = "      } else if (oEl.hasClass('jt-show-'+col)) { ";
		$script[] = "        oEl.removeClass('jt-show-'+col); ";
		$script[] = "        oEl.addClass('jt-hide-'+col); ";
		$script[] = "        num = num - 1; ";
		$script[] = ($indCookie) ? "        myCookie = Cookie.read('jt_'+col); " : " ";
		$script[] = ($indCookie) ? "        if (myCookie == '1') { Cookie.dispose('jt_'+col); } " : " ";
		$script[] = "      } ";
		$script[] = "    } ";
		$script[] = "  } ";
		$script[] = "  if (tag == 'th') { ";
		$script[] = "    cEl.setProperty('colspan', num); ";
		$script[] = "    document.getElementById('header').setProperty('colspan', num); ";
		$script[] = "  } ";
		$script[] = "  return false; ";
		$script[] = "} ";
		$script[] = "";
		
		return implode("\n", $script);		
	}
}
?>