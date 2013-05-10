<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_familytree view - view.html.php
 *
 * @version	1.4.1
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
JLoader::register('JButtonStandard', JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'toolbar'.DS.'button'.DS.'standard.php');

class JoaktreeViewJt_familytree extends JView {
	function display($tpl = null) {
	
		$app = JFactory::getApplication();				
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::script( JoaktreeHelper::jsfile() );
		$this->addToolbar();
		
		// Get data from the model
		$items			= & $this->get( 'Data' );
		$applications 	= & $this->get( 'Applications' );
		$pagination		= & $this->get( 'Pagination' );
		
		//Filter
		$context		= 'com_joaktree.jt_familytree.list.';
		
		$filter_state		= $app->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',	'word' );
		$filter_apptitle	= $app->getUserStateFromRequest( $context.'filter_apptitle',	'filter_apptitle',	'',	'int' );
		$filter_gendex		= $app->getUserStateFromRequest( $context.'filter_gendex',		'filter_gendex',	'',	'int' );
		$filter_language	= $app->getUserStateFromRequest( $context.'filter_language',	'filter_language',	'',	'string' );
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jte.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
		$search			= $app->getUserStateFromRequest( $context.'search',			'search',	'',	'string' );
		$search			= JString::strtolower( $search );
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		
		// search filter
		$lists['search']= $search;
		
		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );
		
		// generic filter attributes
		$select_attr = array();
		$select_attr['class'] = 'inputbox';
		$select_attr['size'] = '1';
		$select_attr['onchange'] = 'submitform( );';
		$value = 'value';
		$text = 'text';
				
		// application filter
		$appTitle = array();
		$appTitle_attr = array();
		$appTitle[0][$value] = 0;
		$appTitle[0][$text]  = JText::_('JT_FILTER_APPLICATION');  
		
		for ($i = 1; $i <= count($applications); $i++) {
			$appTitle[$i][$value] = $applications[$i-1]->id;
			$appTitle[$i][$text]  = $applications[$i-1]->title;
		}
		
		$lists['appTitle']	= JHTMLSelect::genericlist($appTitle, 'filter_apptitle', $select_attr, $value, $text, $filter_apptitle );
		
		// gendex filter
		$gendex = array();
		$gendex_attr = array();
		$gendex[0][$value] = 0;
		$gendex[0][$text]  = JText::_('JT_FILTER_GENDEX');  
		$gendex[1][$value] = 1;
		$gendex[1][$text]  = JText::_('JNO'); 
		$gendex[2][$value] = 2;
		$gendex[2][$text]  = JText::_('JYES'); 
		
		$lists['gendex']	= JHTMLSelect::genericlist($gendex, 'filter_gendex', $select_attr, $value, $text, $filter_gendex );
		
		// language filter
		$lists['language']  = '<select id="filter_language" name="filter_language" class="inputbox" onchange="submitform()">';
		$lists['language'] .= '  <option value="">'.JText::_('JOPTION_SELECT_LANGUAGE').'</option>';
		$lists['language'] .= JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), $value, $text, $filter_language );
		$lists['language'] .= '</select>';
		
		$lists['jsscript'] 	= $this->getJTscript();
		$lists['action']	= $this->get('action');
		if ($lists['action'] == 'assign') {
			$lists['act_treeId']= $this->get('treeId');
		}
		
		$this->assignRef( 'items',  $items );
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('lists',		$lists);
		
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
		
		JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTFAMTREE_TITLE' ), 'familytree' );

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew();
			//JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
		}
		
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList();
			//JToolBarHelper::editList('edit','JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('JT_CONFIRMDELETE');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::divider();
			$bar = JToolBar::getInstance('toolbar');
			// explanation: $bar->appendButton('Standard', $icon, $alt, $task, $listSelect);
			$bar->appendButton('AssignFT', 'assignfamilytree', 'JTFAMTREE_TASK', 'assignFamilyTree', true);
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
	}	
	
	private function getJTscript() {
		$script  = array();
		$title1  = addslashes(JText::_('JTFAMTREE_TASK'));
		$title2  = addslashes(JText::_('JTPROCESS_MSG'));
		$start   = addslashes(JText::_('JTPROCESS_START'));
		$current = addslashes(JText::_('JTPROCESS_CURRENT'));
		$end     = addslashes(JText::_('JTPROCESS_END'));
		$button  = addslashes(JText::_('JTPROCESS_DONE'));
		
		$script[] = "function assignFTInit(trid) { ";
		$script[] = "  var form = document.adminForm; ";
		$script[] = "  var treeid = ''; ";
		
		$script[] = "  if (trid) { ";
		$script[] = "    treeid = trid + '!'; ";
		$script[] = "  } else { ";
		$script[] = "    for (var i = 0; true; i++) { ";
		$script[] = "      var cbx = form['cb'+i]; ";
		$script[] = "      if (!cbx) break; ";
		$script[] = "      if (cbx.checked == true) { ";
		$script[] = "        treeid = treeid + cbx.value + '!'; ";
		$script[] = "      } ";
		$script[] = "    } ";
		$script[] = "  } ";
		$script[] = "   ";
		$script[] = "   ";
		
		$script[] = "  var container = document.getElementById('system-message-container'); ";
		$script[] = "  var lft = new Element('div', {'class': 'width-40 fltrt'});";
		$script[] = "  var fldlft = new Element('fieldset', {'class': 'adminform'}); ";
		$script[] = "  var leglft = new Element('legend', {html: '$title1'}); ";
		$script[] = "  var ullft  = new Element('ul', {'class': 'adminformlist'}); ";
		$script[] = "  var lista  = new Element('li'); ";
		$script[] = "  var licur  = new Element('li'); ";
		$script[] = "  var liend  = new Element('li'); ";
		$script[] = "  var labst  = new Element('label', {html: '$start'}); ";
		$script[] = "  var labcur = new Element('label', {html: '$current'}); ";
		$script[] = "  var labend = new Element('label', {html: '$end'}); ";
		$script[] = "  var inpst  = new Element('input', {id: 'start', type: 'text', 'class': 'readonly'}); ";
		$script[] = "  var inpcur = new Element('input', {id: 'current', type: 'text', 'class': 'readonly'}); ";
		$script[] = "  var inpend = new Element('input', {id: 'end', type: 'text', 'class': 'readonly'}); ";
		$script[] = "  lft.inject(container); ";
		$script[] = "  fldlft.inject(lft); ";
		$script[] = "  leglft.inject(fldlft); ";
		$script[] = "  ullft.inject(fldlft); ";
		$script[] = "  lista.inject(ullft); ";
		$script[] = "  labst.inject(lista); ";	
		$script[] = "  inpst.inject(lista); ";
		$script[] = "  licur.inject(ullft); ";
		$script[] = "  labcur.inject(licur); ";
		$script[] = "  inpcur.inject(licur); ";
		$script[] = "  liend.inject(ullft); ";
		$script[] = "  labend.inject(liend); ";
		$script[] = "  inpend.inject(liend); ";
		
		$script[] = "  var rht = new Element('div', {'class': 'width-50'}); ";
		$script[] = "  var fldrht = new Element('fieldset', {'class': 'adminform', style: 'min-height: 92px;'}); ";
		$script[] = "  var legrht = new Element('legend', {html: '$title2'}); ";
		$script[] = "  var divrht = new Element('div', {id: 'procmsg'}); ";
		$script[] = "  var butrht = new Element('button', {id: 'butprocmsg', html: '$button', disabled: '1', onclick: 'submitform();', style: 'margin-left: 10px;'}); ";
		$script[] = "  rht.inject(container); ";
		$script[] = "  fldrht.inject(rht); ";
		$script[] = "  legrht.inject(fldrht); ";
		$script[] = "  divrht.inject(fldrht); ";
		$script[] = "  butrht.inject(rht); ";
		
		$script[] = "  var url = 'index.php?option=com_joaktree&view=jt_familytree&format=raw&tmpl=component&init=1&treeId=' + treeid; ";
		$script[] = "  assignFT(url); ";
		$script[] = "} ";
		$script[] = " ";	
	
		return implode("\n", $script);
	}
}


class JButtonAssignFT extends JButtonStandard {
	protected function _getCommand($name, $task, $list)
	{
		JHtml::_('behavior.framework');
		$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$message = addslashes($message);

		if ($list)
		{
			//$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
			$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ assignFTInit(); }";
		}
		else
		{
			//$cmd = "Joomla.submitbutton1('$task')";
			$cmd = "assignFTInit();";
		}

		return $cmd;
	}
}
?>