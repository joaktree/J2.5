<?php
/**
 * Joomla! component Joaktree
 * file		administrator jt_settings view - view.html.php
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

class JoaktreeViewJt_settings extends JView {
	function display($tpl = null) {

		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::script( JoaktreeHelper::jsfile() );		
		
		// what is the layout
		$this->layout = JRequest::getCmd('layout');
		$this->addToolbar($this->layout);
		
		// Get data from the model
		if ($this->layout == 'personevent' ) {
			$this->items		= & $this->get( 'DataPersEvent' );
			$this->pagination	= & $this->get( 'personPagination' );
		} else if ($this->layout == 'personname' ) {
			$this->items		= & $this->get( 'DataPersName' );
			$this->pagination	= & $this->get( 'namePagination' );
		} else if ($this->layout == 'relationevent' ) {
			$this->items		= & $this->get( 'DataRelaEvent' );
			$this->pagination	= & $this->get( 'relationPagination' );
		} else {
			$this->items		= & $this->get( 'DataPersEvent' );
			$this->pagination	= & $this->get( 'personPagination' );
		}
		
		//Filter
		$context		= 'com_joaktree.jt_settings.list.';
		
		$html = $this->getHtml();
		
		$this->assignRef( 'html', $html );
		
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar($layout)
	{
		$canDo	= JoaktreeHelper::getActions();
		
		// Get data from the model
		if ($this->layout == 'personevent' ) {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_PERSONEVENTS' ), 'display1' );
		} else if ($this->layout == 'personname' ) {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_NAMES' ), 'display2' );
		} else if ($this->layout == 'relationevent' ) {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_RELATIONEVENTS' ), 'display3' );
		} else {
			JToolBarHelper::title(   '&nbsp;&nbsp;' .JText::_( 'JTSETTINGS_TITLE_PERSONEVENTS' ), 'display1' );
		}
		
		if ($canDo->get('core.edit')) {
			JToolBarHelper::save('save', JText::_( 'JTSETTINGS_HEADING_SAVE' ), 'title');
		}
		
		if ($layout == 'personevent' ) {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		} else if ($layout == 'personname' ) {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		} else if ($layout == 'relationevent' ) {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		} else {
			JToolBarHelper::help('JoaktreeManuel', true, 'http://joaktree.com/index.php/en/joaktree/manual');		
		}
	}	
	
	private function getHtml() {
		$html = '';
		
		$html .= '<form action="index.php?option=com_joaktree" method="post" id="adminForm" name="adminForm">';
		$html .= JHTML::_( 'form.token' ); 

		$html .= '<div id="editcell">';
		$html .= '<table class="adminlist">';
		
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th colspan="7">&nbsp;</th>';
		$html .= '<th colspan="2">'.JText::_( 'JTSETTINGS_HEADING_LIVINGPERSON' ).'</th>';
		$html .= '<th colspan="2">&nbsp;</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th width="5">'.JText::_( 'JT_HEADING_NUMBER' ).'</th>';
		$html .= '<th width="20">';
		$html .= '<input type="checkbox" name="toggle" value="" onclick="checkAll('.count( $this->items ).');" />';
		$html .= '</th>';
		$html .= '<th>'.JText::_( 'JTSETTINGS_HEADING_FIELD' ).'</th>';
		$html .= '<th width="4%">'.JText::_( 'JTSETTINGS_HEADING_ORDER' ).'</th>';
		$html .= '<th width="1%">'.JHTML::_('grid.order',  $this->items ).'</th>';
		$html .= '<th width="4%">'.JText::_( 'JT_HEADING_PUBLISHED' ).'</th>';
		$html .= '<th width="8%">'.JText::_( 'JT_HEADING_ACCESS' ).'</th>';
		$html .= '<th width="8%">'.JText::_( 'JTSETTINGS_HEADING_ACCESS_LIVPERSON' ).'</th>';
		$html .= '<th width="8%">'.JText::_( 'JTSETTINGS_HEADING_ALT_LIVPERSON' ).'</th>';
		$html .= '<th width="1%">'.JText::_( 'JTSETTINGS_HEADING_SAVE' ).'</th>';
		$html .= '<th>'.JText::_( 'JTSETTINGS_HEADING_EXPLANATION' ).'</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		
		$html .= '<tbody>';
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
			$row 		= &$this->items[$i];
			$checked 	= JHTML::_('grid.id',   $i, $row->id );
			$published 	= JHTML::_('grid.published', $row, $i );
			
			$gedcomLabel  = '<input type="hidden" id="code'.$row->id.'" name="code'.$row->id.'" value="'.JText::_( $row->code ).'" />';
			$gedcomLabel .= JText::_( $row->code );
			 
			$access		 = '<select id="access'.$row->id.'" name="access'.$row->id.'" class="inputbox" onchange="javascript:changeAccessLevel(\'cb'.$i.'\')">';
			$access		.= JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $row->access);
			$access		.= '</select>';	
			
			$accessLiving = '<select id="accessLiving'.$row->id.'" name="accessLiving'.$row->id.'" class="inputbox" onchange="javascript:changeAccessLevel(\'cb'.$i.'\')">';
			$accessLiving .= '<option  value="">'.JText::_('JTSETTINGS_LISTVALUE_NOBODY').'</option>';
			$accessLiving .= JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $row->accessLiving);
			$accessLiving .= '</select>';	
			
			$altLiving  = '<select id="altLiving'.$row->id.'" name="altLiving'.$row->id.'" class="inputbox" onchange="javascript:changeAccessLevel(\'cb'.$i.'\')">';
			$altLiving .= '<option  value="">'.JText::_('JTSETTINGS_LISTVALUE_NOBODY').'</option>';
			$altLiving .= JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $row->altLiving);
			$altLiving .= '</select>';	
			
			$saveAccess = '<a href="javascript:jtsaveaccess(\'cb'.$i.'\')" class="saveorder" title="'.JText::_('JTSETTINGS_TOOLTIP_SAVEACCESS').'"></a>';
			
			$explanation	= $this->showExplanation($row, $i);
			
			$html .= '<tr class="row'.$k.'">';
			$html .= '<td>'.$this->pagination->getRowOffset( $i ).'</td>';
			$html .= '<td>'.$checked.'</td>';
			$html .= '<td>'.$gedcomLabel.'</td>';
			
			$html .= '<td class="order" colspan="2">';
			$html .= '<span>'.$this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', true ).'</span>';
			$html .= '<span>'.$this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', true ).'</span>';
			$html .= '<input type="text" name="order[]" size="5" value="'.$row->ordering.'" class="text_area" style="text-align: center" />';
			$html .= '</td>';
			
			$html .= '<td align="center">'.$published.'</td>';
			$html .= '<td align="center">'.$access.'</td>';
			$html .= '<td align="center">'.$accessLiving.'</td>';
			$html .= '<td align="center">'.$altLiving.'</td>';
			$html .= '<td align="center">'.$saveAccess.'</td>';
						
			$html .= '<td>'.$explanation.'</td>';
			$html .= '</tr>';
			
			$k = 1 - $k;
		}
		$html .= '</tbody>';
		
		$html .= '</table>';
		$html .= '</div>';
		
		$html .= '<input type="hidden" name="option" value="com_joaktree" />';
		$html .= '<input type="hidden" name="layout" value="'.$this->layout.'" />';
		$html .= '<input type="hidden" name="task" value="" />';
		$html .= '<input type="hidden" name="boxchecked" value="0" />';
		$html .= '<input type="hidden" name="controller" value="jt_settings" />';
		
		$html .= '</form>';
		
		return $html;
	}
	
	private function showExplanation($row, $i) {
		$html = '';
		$color 		= 'blue';
		
		$value 		= '<strong>'.strtoupper( JText::_( $row->code ) ).'</strong>';
		$txtPerson		= JText::_('JTSETTINGS_EXPTEXT_PERSON').'&nbsp;';
		$personNotLiv	= $txtPerson.'<em>'.JText::_('JTSETTINGS_EXPTEXT_NOT_LIVPERSON').'</em>:&nbsp;';
		$personLiving	= $txtPerson.'<em>'.JText::_('JTSETTINGS_EXPTEXT_LIVPERSON').'</em>:&nbsp;';
		
		if (!$row->published) {
			// nothing is published
			$html .= $value.'&nbsp;'.JText::_('JTSETTINGS_EXPTEXT_FULLYHIDDEN');
		} else {
			// something is published
			$html .= $personNotLiv.$value.'&nbsp;';
			$html .= JText::_('JTSETTINGS_EXPTEXT_ACCESSLEVELS').'&nbsp;';
			$html .= '<span style="color: '.$color.';">'.$row->access_level .'</span>.';
			
			if (  (($row->accessLiving != null) and ($row->accessLiving != 0)) 
			   or (($row->altLiving    != null) and ($row->altLiving    != 0)) 
			   ) {			
			
				if (($row->accessLiving != null) and ($row->accessLiving != 0)) {
					$html .= '<br/>'.$personLiving.$value.'&nbsp;';
					$html .= JText::_('JTSETTINGS_EXPTEXT_ACCESSLEVELS').'&nbsp;';
					$html .= '<span style="color: '.$color.';">'.$row->access_level_living.'</span>';
				}
					
				if (   ($row->altLiving != null) 
				   and ($row->altLiving != 0)
				   and ($row->altLiving != $row->accessLiving)
				   ) {
					$html .= '<br/>'.$personLiving;
					$html .= JText::_('JTSETTINGS_EXPTEXT_ALTTEXT').'&nbsp;';
					$html .= '<span style="color: '.$color.';">'.$row->access_level_alttext .'</span>';		

					if (($row->accessLiving != null) and ($row->accessLiving != 0)) {
						$html .= JText::_('JTSETTINGS_EXPTEXT_ALTTEXT2').'&nbsp;';
						$html .= '<span style="color: '.$color.';">'.$row->access_level_living.'</span>';
					}
				}
				
				$html .= '.';
							
			} else {
				$html .= '<br/>'.$personLiving.$value.'&nbsp;';
				$html .= JText::_('JTSETTINGS_EXPTEXT_FULLYHIDDEN');
			}
		}
		
		return $html;
	}
	
}
?>