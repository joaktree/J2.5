<?php 
/**
 * Joomla! component Joaktree
 * file		front end personform model - personform.php
 *
 * @version	1.4.5
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
jimport('joomla.application.component.modelform');

// import component libraries
JLoader::import('helper.person', JPATH_COMPONENT);
JLoader::import('components.com_joaktree.helpers.jt_relations', JPATH_ADMINISTRATOR);

class JoaktreeModelPersonform extends JModelForm {
	
	function __construct() {
		parent::__construct();            		
	} 
	
 	public function getApplicationId() {
		return JoaktreeHelper::getApplicationId();
 	}
	
 	public function getApplicationName() {
		return JoaktreeHelper::getApplicationName();
 	}
	
 	public function getTreeId() {
		return JoaktreeHelper::getTreeId();
 	}
	
	public function getPersonId() {
		return JoaktreeHelper::getPersonId(false, true);	
	}

	public function getRelationId() {
		return JoaktreeHelper::getRelationId();	
	}

	public function getAction() {
		return JoaktreeHelper::getAction();	
	}
	
	public function getAccess() {
		return JoaktreeHelper::getAccess();
	}
	
	public function getPerson() {
		static $person;
		if (!isset($person)) {
			$id = array();
			$id[ 'person_id' ]	= $this->getPersonId();
			if (isset($id[ 'person_id' ])) { 
				$id[ 'app_id' ]		= $this->getApplicationId(); 
				$id[ 'tree_id' ]	= $this->getTreeId(); 
				$person	=  new Person($id, 'full');
			}
		}
		
		return $person;
	}
	
	public function getRelation() {
		static $relation;
		if (!isset($relation)) {
			$id = array();
			$id[ 'app_id' ]		= $this->getApplicationId(); 
			$id[ 'person_id' ] 	= $this->getRelationId();
			$id[ 'tree_id' ]	= $this->getTreeId();				 

			$relation	=  new Person($id, 'basic');
		}
		return $relation;
	}
		
	public function getPicture() {
		static $picture;
		if (!isset($picture)) {
			$tmp = JRequest::getVar('picture');
			if ($tmp) {
				$picture =  json_decode(base64_decode($tmp));
			} else {
				$picture = 1;
			}
		}
		return $picture;
	}
			
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joaktree.joaktree', 'personform', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {		
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.joaktree.data', array());

		if (empty($data)) {
			$data = $this->getItem();		
		}

		return $data;
	}
		
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk	The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 * @since   11.1
	 */
	public function getItem($pk = null)
	{
		return $this->getPerson();
	}
	
	
	public function delete($id) {
		$canDo	= JoaktreeHelper::getActions();
		
		// Edit changes
		if ( (is_object($canDo)) && $canDo->get('core.delete') ) {	
			$tmp = explode("!", $id);
			
			$ret = $this->delete_person($tmp[0], $tmp[1]);
					
		} else {
			$ret = false;
		}
	
		// Deal with errors
		$errors = $this->getErrors();
		if (count($errors) > 0) {
			$text = JText::_(implode(' ##### ', $errors ));
		} else {
			$text = ($ret) ? JText::sprintf('JT_DELETED',$ret) : JText::_('JT_NOTAUTHORISED');
		}
					
		return $text;		
	}
	
	public function save(&$form) {
		$canDo	= JoaktreeHelper::getActions();
		$ret    = false;  
		
		// Edit changes
		if (is_object($canDo)) {
			// in case we need to set relation indicators
			$this->relationIds = array();
			
			if ($canDo->get('core.create')) {
				// in case of a new person the id is empty and we are filling it now
				if ($form['person']['status'] == 'new') {		
					// retreive ID and double check that it is not used
					$continue = true;
					$i = 0;
					while ($continue) {
						$tmpId = JoaktreeHelper::generateJTId();
						$i++;
						
						if ($this->check($tmpId, 'person')) {
							$form['person']['id'] = $tmpId;
							$continue = false;
							break;
						}
						if ($i > 100) {
							$continue = false;
							return JText::_('JTAPPS_MESSAGE_NOSAVE');
						}
					}
				}
				
				switch ($form['type']) {
					case "newperson": 
									$ret = $this->save_events($form, 'person');
									break;
					case "addparent":
					case "addpartner":
					case "addchild":
									$ret = $this->add_person_relations($form);
									if ($ret) { $ret = $this->save_events($form, 'person'); };
									break;
					default:		// continue
									break;
				}				
			}
			
			if ($canDo->get('core.edit')) {
				switch ($form['type']) {
					case "pevents": $ret = $this->save_events($form, 'person');
									break;
					case "revents": $ret = $this->save_events($form, 'relation');
									break;
					case "names": 	$ret = $this->save_names($form, 'all');
									break;
					case "notes": 	$ret = $this->save_notes($form, 'person', null);
									break;
					case "references": 	
									$ret = $this->save_references($form, 'person');
									break;
					case "media": 	$ret = $this->save_media($form);
									break;
					case "medialist": 	
									$ret = $this->save_medialist($form);
									break;
					case "parents": 
					case "partners":
					case "children":
									$ret = $this->update_person_relations($form);
									break;
					default:		// continue
									break;
				}
			}
			
			if ($canDo->get('core.edit.state')) {
				// State changes
				switch ($form['type']) {
					case "state": $ret = $this->save_state($form);
									break;
					default:		// continue
									break;
				}					
			}
			
			// always save / update the person
			if ($ret) {
				$ret = $this->save_person($form);
			}
			
			// set relation indicators - if applicable
			if (($ret) && (count($this->relationIds) > 0)) {
				$ret = jt_relations::setRelationIndicators($form['person']['app_id'], $this->relationIds);
			}
		}
		
		// Deal with errors
		$errors = $this->getErrors();
		if (count($errors) > 0) {
			$text = implode(' ##### ', $errors );
			$ret  = false;
		} else {
			$text = ($ret) ? 'JT_SAVED' : 'JT_NOTAUTHORISED';
		}
					
		return JText::_($text);
	}
	
	private function delete_person($appId, $personId) {
		$tabPerson	= JTable::getInstance('joaktree_persons', 'Table');;
					
		// Bind the fields to the table
		$tabPerson->id             	= $personId;
		$tabPerson->app_id         	= $appId;
				
		// Load the person, so we can save the names for prosperity
		if (!$tabPerson->load()) {
			$this->setError('Error deleting person -> Person not found in table joaktree_persons: '.$appId.'!'.$personId);
			return false;
		}
		
		// Delete person, including all dependencies (cascading)
		if (!$tabPerson->delete()) {
			$this->setError('Error deleting person -> delete: '.$tabPerson->getError());
			return false;
		}
		
		// Logging
		$tabLog	= JTable::getInstance('joaktree_logs', 'Table');;
		$tabLog->app_id				= $appId;
		$tabLog->object_id			= $personId;
		$tabLog->object				= 'prsn';
		if (!$tabLog->log('D')) {
			$this->setError('Error deleting person -> log: '.$tabLog->getError());
			return false;
		}
		
		$tabLogRemoval	= JTable::getInstance('joaktree_logremovals', 'Table');;
		$tabLogRemoval->app_id		= $appId;
		$tabLogRemoval->object_id	= $personId;
		$tabLogRemoval->object		= 'prsn';
		$names = array();
		$names[] = $tabPerson->firstName;
		$names[] = $tabPerson->namePreposition;
		$names[] = $tabPerson->familyName;
		$names[] = '['.$tabPerson->id.']';
		$name = implode(' ', $names);
		$tabLogRemoval->description	= substr($name, 0, 100);
		if (!$tabLogRemoval->store()) {
			$this->setError('Error deleting person -> remove-log: '.$tabLogRemoval->getError());
			return false;
		}
			
		return $name;
	}
	
	private function save_person(&$form) {
		$ret = true;
		
		// Logging
		$tabLog	= JTable::getInstance('joaktree_logs', 'Table');;
		$tabLog->app_id				= $form['person']['app_id'];
		$tabLog->object_id			= $form['person']['id'];
		$tabLog->object				= 'prsn';
		if (!$tabLog->log(($form['person']['status'] == 'new') ? 'C' : 'U')) {
			$this->setError('Error saving person -> log: '.$tabLog->getError());
			return false;
		}
		
		// if it is not a new or updated person - just a new relationship
		// we skip this function
		if ( $form['person']['status'] == 'relation' ) {
			return true;
		}
		
		// new person - add it to persons-trees, persons, and admin
		if ( $form['person']['status'] == 'new' ) {
			if ($ret) { $ret = $this->save_person_trees($form); } // Note: the default tree is set in this step
			if ($ret) { $ret = $this->save_state($form); }		  // Note: the default tree is saved in this step
		   	if ($ret) { $ret = $this->save_names($form, 'main'); }		   	
		} // end of adding new person
		
		if ($ret) {
			$tabPerson	= JTable::getInstance('joaktree_persons', 'Table');;
			$query = $this->_db->getQuery(true);
		
			// Bind the form fields to the table
			$tabPerson->id             	= $form['person']['id'];
			$tabPerson->app_id         	= $form['person']['app_id'];
			if (isset($form['person']['sex'])) {
				$tabPerson->sex = $form['person']['sex'];
				
				if ($tabPerson->sex == 'F') {
					$sextype = 'mother';
				} else if ($tabPerson->sex == 'M') {
					$sextype = 'father';
				} else {
					$sextype = 'unknown';
				}
				
				// update the relationship table with the new sex
				if ($type != 'unknown') {
					$query->clear();
					$query->update(' #__joaktree_relations ');
					$query->set(   ' type        = '.$this->_db->quote($sextype).' ');
					$query->where( ' app_id      = '.$tabPerson->app_id.' ');
					$query->where( ' person_id_2 = '.$this->_db->quote($tabPerson->id ).' ');
					$query->where( ' type        IN ('.$this->_db->quote('father').', '.$this->_db->quote('mother').') ');
					$this->_db->setQuery( $query );

					if (!$this->_db->query()) {
						$this->setError('Error saving person -> joaktree_relations: '.$this->_db->getErrorMsg());
					}
				}
			}
		
			// Make sure the table is valid
			if (!$tabPerson->check()) {
				$this->setError('Error checking person: ');
				return false;
			}
		
			// Store the table to the database
			if (!$tabPerson->store(false)) {
				$this->setError('Error saving person: '.$tabPerson->getError());
				return false;
			}
		}
		
		return true;
	}
	
	private function save_state(&$form) {
		$tabPerson	= JTable::getInstance('joaktree_admin_persons', 'Table');;
					
		// Bind the form fields to the table
		$tabPerson->id             	= $form['person']['id'];
		$tabPerson->app_id         	= $form['person']['app_id'];
		if (isset($form['person']['livingnew'])) {
			$tabPerson->living = $form['person']['livingnew'];
		}
		$tabPerson->published = (isset($form['person']['published'])) ? $form['person']['published'] : true;
		$tabPerson->page	  = (isset($form['person']['page'])) ? $form['person']['page'] : true;
		$tabPerson->map	  	  = (isset($form['person']['map'])) ? $form['person']['map'] : 0;
		if (isset($form['person']['default_tree_id'])) {
			$tabPerson->default_tree_id = $form['person']['default_tree_id'];
		}
		
		// Make sure the table is valid
		if (!$tabPerson->check()) {
			$this->setError('Error checking admin person: ');
			return false;
		}
		
		// Store the table to the database
		if (!$tabPerson->store(false)) {
			$this->setError('Error saving admin person: '.$tabPerson->getError());
			return false;
		}
				
		return true;
	}
	
	private function save_events(&$form, $level) {
		// person events
		
		// Store the references to the database
		if (!$this->save_references($form, $level)) {
			return false;
		}
		
		// Store the notes to the database
		if (!$this->save_notes($form, $level, 'event')) {
			return false;
		}
		
		// Events
		if (isset($form['person']['events'])) {
			switch ($level) {
				case "relation":	$tabEvent	= JTable::getInstance('joaktree_relation_events', 'Table');
									if ($form['person']['id'] < $form['person']['relations']['id'][0]) {
										$tabEvent->person_id_1	= $form['person']['id'];
										$tabEvent->person_id_2	= $form['person']['relations']['id'][0];
									} else {
										$tabEvent->person_id_2	= $form['person']['id'];
										$tabEvent->person_id_1	= $form['person']['relations']['id'][0];
									}				
									break;
				case "person":		// person same as default
				default		 :		$tabEvent	= JTable::getInstance('joaktree_person_events', 'Table');
									$tabEvent->person_id        	= $form['person']['id'];
									break;
			}
			
			// Bind the form fields to the table
			$tabEvent->app_id         	= $form['person']['app_id'];
			
			for ($i=0; $i<count($form['person']['events']['orderNumber']); $i++){
				switch ($level) {
					case "relation":	$code = $form['person']['events']['relcode'][$i];				
										break;
					case "person":		// person same as default
					default		 :		$code = $form['person']['events']['code'][$i];	
										break;
				}
				
				$tabEvent->orderNumber 	= $form['person']['events']['orderNumber'][$i];
				$tabEvent->code		 	= $code;
				$tabEvent->type		 	= htmlspecialchars($form['person']['events']['type'][$i], ENT_QUOTES, 'UTF-8');	
			
				// create string for eventdate
				$eventDate = '';
				// extended date fields only
				if ($form['person']['events']['eventDateType'][$i] == 'extended') {
					if ($form['person']['events']['eventDateLabel1'][$i]) {
						$eventDate .= $form['person']['events']['eventDateLabel1'][$i].' ';
					}
				}
				
				// simple + extended date fields
				if (  ($form['person']['events']['eventDateType'][$i] == 'simple')
				   || ($form['person']['events']['eventDateType'][$i] == 'extended')
				   ) {
					if ($form['person']['events']['eventDateDay1'][$i]) {
						$eventDate .= $form['person']['events']['eventDateDay1'][$i].' ';
					}
					if ($form['person']['events']['eventDateMonth1'][$i]) {
						$eventDate .= $form['person']['events']['eventDateMonth1'][$i].' ';
					}
					if (!empty($form['person']['events']['eventDateYear1'][$i])) {
						$eventDate .= $form['person']['events']['eventDateYear1'][$i].' ';
					}
				}
				
				// extended date fields only
				if ($form['person']['events']['eventDateType'][$i] == 'extended') {
					if ($form['person']['events']['eventDateLabel2'][$i]) {
						$eventDate .= $form['person']['events']['eventDateLabel2'][$i].' ';
					}
					if ($form['person']['events']['eventDateDay2'][$i]) {
						$eventDate .= $form['person']['events']['eventDateDay2'][$i].' ';
					}
					if ($form['person']['events']['eventDateMonth2'][$i]) {
						$eventDate .= $form['person']['events']['eventDateMonth2'][$i].' ';
					}
					if (!empty($form['person']['events']['eventDateYear2'][$i])) {
						$eventDate .= $form['person']['events']['eventDateYear2'][$i].' ';
					}
				}

				// description date fields only
				if ($form['person']['events']['eventDateType'][$i] == 'description') {
					if (!empty($form['person']['events']['eventDate'][$i])) {
						$eventDate .= htmlspecialchars($form['person']['events']['eventDate'][$i].' ', ENT_QUOTES, 'UTF-8');
					}
				}
				$tabEvent->eventDate 	= trim($eventDate);
				// create string for eventdate
				
				// set the location
				$tabEvent->location	 	= htmlspecialchars($form['person']['events']['location'][$i], ENT_QUOTES, 'UTF-8');
				
				// set the value
				$tabEvent->value		= htmlspecialchars($form['person']['events']['value'][$i], ENT_QUOTES, 'UTF-8');
	
				// Make sure the table is valid
				if (!$tabEvent->check()) {
					$this->setError('Error checking event: ');
					return false;
				}
					
				if  (  ($form['person']['events']['status'][$i] == 'new')
					|| ($form['person']['events']['status'][$i] == 'loaded')
					) {
					// Store the table to the database
					if (!$tabEvent->store(false)) {
						$this->setError('Error saving event: '.$tabEvent->getError());
						return false;
					}
				} else if ($form['person']['events']['status'][$i] == 'loaded_deleted') {
					// Delete row from the database
					if (!$tabEvent->delete()) {
						$this->setError('Error deleting event: '.$tabEvent->getError());
						return false;
					}
				}
			}
		}

		return true;
	}
	
	private function save_names(&$form, $action = 'all') {
		$tabPerson	= JTable::getInstance('joaktree_persons', 'Table');;
					
		// Bind the form fields to the table
		$tabPerson->id             	= $form['person']['id'];
		$tabPerson->app_id         	= $form['person']['app_id'];
		$tabPerson->firstName       = isset($form['person']['firstName']) 
										? htmlspecialchars($form['person']['firstName'], ENT_QUOTES, 'UTF-8') 
										: null;
		$tabPerson->patronym        = isset($form['person']['patronym']) 
										? htmlspecialchars($form['person']['patronym'], ENT_QUOTES, 'UTF-8') 
										: null;
		$tabPerson->namePreposition = isset($form['person']['namePreposition']) 
										? htmlspecialchars($form['person']['namePreposition'], ENT_QUOTES, 'UTF-8') 
										: null;
		$tabPerson->familyName      = isset($form['person']['rawFamilyName']) 
										? htmlspecialchars($form['person']['rawFamilyName'], ENT_QUOTES, 'UTF-8') 
										: null;
		$tabPerson->prefix			= isset($form['person']['prefix']) 
										? htmlspecialchars($form['person']['prefix'], ENT_QUOTES, 'UTF-8') 
										: null;
		$tabPerson->suffix      	= isset($form['person']['suffix']) 
										? htmlspecialchars($form['person']['suffix'], ENT_QUOTES, 'UTF-8') 
										: null;
										
		// Make sure the table is valid
		if (!$tabPerson->check()) {
			$this->setError('Error checking person name: ');
			return false;
		}
		
		// Store the table to the database
		if (!$tabPerson->store(false)) {
			$this->setError('Error saving person name: '.$tabPerson->getError());
			return false;
		}
		
		if ($action == 'all') {
			// Store the references for additional names to the database
			if (!$this->save_references($form, 'person')) {
				return false;
			}
			
			// Store the notes for additional names to the database
			if (!$this->save_notes($form, 'person', 'name')) {
				return false;
			}
					
			// additional names
			$tabName	= JTable::getInstance('joaktree_person_names', 'Table');;
			// Bind the form fields to the table
			$tabName->person_id        	= $form['person']['id'];
			$tabName->app_id         	= $form['person']['app_id'];
			
			for ($i=0; $i<count($form['person']['names']['orderNumber']); $i++){
				$tabName->orderNumber 	= $form['person']['names']['orderNumber'][$i];
				$tabName->code		 	= $form['person']['names']['code'][$i];
				$tabName->value			= htmlspecialchars($form['person']['names']['value'][$i], ENT_QUOTES, 'UTF-8');
	
				// Make sure the table is valid
				if (!$tabName->check()) {
					$this->setError('Error checking name: ');
					return false;
				}
					
				if  (  ($form['person']['names']['status'][$i] == 'new')
					|| ($form['person']['names']['status'][$i] == 'loaded')
					) {
					// Store the table to the database
					if (!$tabName->store(false)) {
						$this->setError('Error saving name: '.$tabName->getError());
						return false;
					}
				} else if ($form['person']['names']['status'][$i] == 'loaded_deleted') {
					// Delete row from the database
					if (!$tabName->delete()) {
						$this->setError('Error deleting name: '.$tabName->getError());
						return false;
					}
				}
			}
		}
					
		return true;
	}
	
	private function save_references(&$form, $level) {
		if (!isset($form['person']['references']) || !is_array($form['person']['references'])) {
			// no references
			return true;
		}
		
		// Citations 
		$tabRef	= JTable::getInstance('joaktree_citations', 'Table');
		// Bind the form fields to the table
		switch ($level) {
			case "relation":	if ($form['person']['id'] < $form['person']['relations']['id'][0]) {
									$tabRef->person_id_1	= $form['person']['id'];
									$tabRef->person_id_2	= $form['person']['relations']['id'][0];
								} else {
									$tabRef->person_id_2	= $form['person']['id'];
									$tabRef->person_id_1	= $form['person']['relations']['id'][0];
								}
								break;
			case "person":		// person same as default
			default		 :		$tabRef->person_id_1 = $form['person']['id'];
								$tabRef->person_id_2 = 'EMPTY';
								break;
		}
				
		$tabRef->app_id         	= $form['person']['app_id'];
		
		for ($i=0; $i<count($form['person']['references']['objectOrderNumber']); $i++){
			$tabRef->objectType		= $form['person']['references']['objectType'][$i];
			$tabRef->objectOrderNumber	
									= $form['person']['references']['objectOrderNumber'][$i];
			$tabRef->source_id		= $form['person']['references']['app_source_id'][$i];
			$tabRef->orderNumber	= $form['person']['references']['orderNumber'][$i];
			$tabRef->dataQuality	= (!empty($form['person']['references']['dataQuality'][$i]))
										? $form['person']['references']['dataQuality'][$i]
										: null;
			$tabRef->page			= (!empty($form['person']['references']['page'][$i]))
										? htmlspecialchars($form['person']['references']['page'][$i], ENT_QUOTES, 'UTF-8')
										: null;
			$tabRef->quotation		= (!empty($form['person']['references']['quotation'][$i]))
										? htmlspecialchars($form['person']['references']['quotation'][$i], ENT_QUOTES, 'UTF-8')
										: null;
			$tabRef->note			= (!empty($form['person']['references']['note'][$i]))
										? htmlspecialchars($form['person']['references']['note'][$i], ENT_QUOTES, 'UTF-8')
										: null;
			
			// Make sure the table is valid
			if (!$tabRef->check()) {
				$this->setError('Error checking reference: ');
				return false;
			}
				
			if  (  ($form['person']['references']['status'][$i] == 'new')
				|| ($form['person']['references']['status'][$i] == 'loaded')
				) {
				// Store the table to the database
				if (!$tabRef->store(false)) {
					$this->setError('Error saving reference: '.$tabRef->getError());
					return false;
				}
			} else if ($form['person']['references']['status'][$i] == 'loaded_deleted') {
				// Delete row from the database
				if (!$tabRef->delete()) {
					$this->setError('Error deleting reference: '.$tabRef->getError());
					return false;
				}
			}
		}
		
		return true;
	}
	
	private function save_notes(&$form, $level, $event) {
		if (!isset($form['person']['notes']) || !is_array($form['person']['notes'])) {
			// no notes
			return true;
		}
		
		// Notes		
		switch ($level) {
			case "relation":	$tabNot	= JTable::getInstance('joaktree_relation_notes', 'Table');
								if ($form['person']['id'] < $form['person']['relations']['id'][0]) {
									$tabNot->person_id_1	= $form['person']['id'];
									$tabNot->person_id_2	= $form['person']['relations']['id'][0];
								} else {
									$tabNot->person_id_2	= $form['person']['id'];
									$tabNot->person_id_1	= $form['person']['relations']['id'][0];
								}				
								break;
			case "person":		// person same as default
			default		 :		$tabNot	= JTable::getInstance('joaktree_person_notes', 'Table');
								$tabNot->person_id   = $form['person']['id'];
								break;
		}
			   	
		$tabNot->app_id         	= $form['person']['app_id'];
	   	$indNotesTable = JoaktreeHelper::getIndNotesTable($tabNot->app_id);
	   	
	   	if ($indNotesTable) {
	   		$tabNot2	= JTable::getInstance('joaktree_notes', 'Table');
	   		$tabNot2->app_id       	= $form['person']['app_id'];
	   	}  	
	   	
		for ($i=0; $i<count($form['person']['notes']['orderNumber']); $i++){
			// person
			$tabNot->orderNumber		= $form['person']['notes']['orderNumber'][$i];
			$tabNot->indCitation		= false;
			if ($event == 'name') {
				$tabNot->nameOrderNumber	= $form['person']['notes']['objectOrderNumber'][$i];
				$tabNot->eventOrderNumber	= null;
			} else if ($event == 'event') {
				$tabNot->nameOrderNumber	= null;
				$tabNot->eventOrderNumber	= $form['person']['notes']['objectOrderNumber'][$i];
			} else {
				$tabNot->nameOrderNumber	= null;
				$tabNot->eventOrderNumber	= null;
			}
			
			if (isset($form['person']['notes']['text'][$i])) {
				$noteText = str_replace( $form['lineEnd']
				                       , "&#10;&#13;"
				                       , htmlspecialchars( $form['person']['notes']['text'][$i]
				                                         , ENT_QUOTES, 'UTF-8'
				                                         )
				                       );
			} else {
				$noteText = null;
			}
			
			if ($indNotesTable) {
				if (isset($form['person']['notes']['note_id'][$i])) {		
					$tabNot->note_id = $form['person']['notes']['note_id'][$i];
				} else {
					// retreive ID and double check that it is not used
					$continue = true;
					$i = 0;
					while ($continue) {
						$tmpId = JoaktreeHelper::generateJTId();
						$i++;
						
						if ($this->check($tmpId, 'note')) {
							$tabNot->note_id = $tmpId;
							$continue = false;
							break;
						}
						if ($i > 100) {
							$continue = false;
							return false;
						}
					}
				}
				
				$tabNot2->id 	 = $tabNot->note_id;
				$tabNot2->value	 = $noteText;
			} else { 
				$tabNot->value   = $noteText;
			}					
			
			// Make sure the table is valid
			if (!$tabNot->check()) {
				$this->setError('Error checking note: ');
				return false;
			}
			
			if  (  ($form['person']['notes']['status'][$i] == 'new')
				|| ($form['person']['notes']['status'][$i] == 'loaded')
				) {
				// Store the table to the database
				if (!$tabNot->store(false)) {
					$this->setError('Error saving note (1): '.$tabNot->getError());
					return false;
				}
				if ($indNotesTable) {		
					if (!$tabNot2->store(false)) {
						$this->setError('Error saving note (2): '.$tabNot2->getError());
						return false;
					}
				}
			} else if ($form['person']['notes']['status'][$i] == 'loaded_deleted') {
				// Delete row from the database
				if ($indNotesTable) {		
					if (!$tabNot2->delete()) {
						$this->setError('Error deleting note (2): '.$tabNot2->getError());
						return false;
					}
				}
				if (!$tabNot->delete()) {
					$this->setError('Error deleting note (1): '.$tabNot->getError());
					return false;
				}
			}
		}

		return true;
	}
	
	private function getSex($appId, $personId) {
		$query = $this->_db->getQuery(true);
		$query->select(' jpn.sex ');
		$query->from(  ' #__joaktree_persons  jpn ');
		$query->where( ' jpn.app_id = '.$appId.' ');
		$query->where( ' jpn.id     = '.$this->_db->quote($personId).' ');
		$this->_db->setQuery($query);
		$sex = $this->_db->loadResult();
		
		return $sex;
	}

	private function add_person_relations(&$form) {	
		$tabRel	= JTable::getInstance('joaktree_relations', 'Table');
		// Bind the form fields to the table
		$tabRel->app_id			= $form['person']['app_id'];
		
		// check family
		$famtmp = explode('!', $form['person']['relations']['family'][0]);
		$relation_id = $famtmp[0]; 
		$family_id  = $famtmp[1];
		
		if ($family_id == '0') {
			// retreive ID and double check that it is not used
			$continue = true;
			$i = 0;
			while ($continue) {
				$tmpId = JoaktreeHelper::generateJTId();
				$i++;
				
				if ($this->check($tmpId, 'family')) {
					$family_id = $tmpId;
					$continue = false;
					break;
				}
				if ($i > 100) {
					$continue = false;
					return false;
				}
			}	
		}

		if (($form['action'] == 'addparent') || ($form['action'] == 'addchild')) {
			if ($form['action'] == 'addparent') {
				// adding parent
				$tabRel->person_id_1	= $form['person']['relations']['id'][0];
				$tabRel->person_id_2	= $form['person']['id'];				
				$sex = $form['person']['sex'];
			} else {
				// adding child
				$tabRel->person_id_1	 = $form['person']['id'];
				$tabRel->person_id_2	 = $form['person']['relations']['id'][0];
				$sex = null;				
			}
			
//			// check family
//			$famtmp = explode('!', $form['person']['relations']['family'][0]);
//			$relation_id = $famtmp[0]; 
//			$family_id  = $famtmp[1];
			
			// If it is a second parent, this uniqueness of family_id has to be checked.
			if (($relation_id != '0') && ($family_id != '0')) {
				$family_id = $this->checkFamilyId($tabRel->app_id, $relation_id, $tabRel->person_id_2, $family_id);
			}
			
			if (!$sex) {
				$sex = $this->getSex($tabRel->app_id, $tabRel->person_id_2);
			}
			$tabRel->type			= ($sex == 'F') ? 'mother' : 'father';
			$tabRel->subtype		= $form['person']['relations']['relationtype'][0];
			$tabRel->family_id		= $family_id;
			$tabRel->orderNumber_1 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_1, 'parent');
			$tabRel->orderNumber_2 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_2, 'child');
		} else if ($form['action'] == 'addpartner') {
			if ($form['person']['id'] < $form['person']['relations']['id'][0]) {
				$tabRel->person_id_1	= $form['person']['id'];
				$tabRel->person_id_2	= $form['person']['relations']['id'][0];
			} else {
				$tabRel->person_id_2	= $form['person']['id'];
				$tabRel->person_id_1	= $form['person']['relations']['id'][0];
			}
			
//			// check family
//			$famtmp = explode('!', $form['person']['relations']['family'][0]);
//			$relation_id = $famtmp[0]; 
//			$family_id  = $famtmp[1];
			
			$tabRel->type			= 'partner';
			$tabRel->subtype		= $form['person']['relations']['partnertype'][0];
			$tabRel->family_id		= $family_id;
			$tabRel->orderNumber_1 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_1, 'partner');
			$tabRel->orderNumber_2 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_2, 'partner');
		}
		
		// Make sure the table is valid
		if (!$tabRel->check()) {
			$this->setError('Error checking relation (1): ');
			return false;
		// Else store the table to the database
		} else if (!$tabRel->store(false)) {
			$this->setError('Error saving relation (1): '.$tabRel->getError());
			return false;
		}
		
		$this->relationIds[] = $form['person']['relations']['id'][0];
		$this->relationIds[] = $form['person']['id'];
		
		// If it is a second parent, this relationship has to be added too.
		if (  (isset($relation_id)) 
		   && ($relation_id != '0')
		   && (  ($form['action'] == 'addparent')
		      || ($form['action'] == 'addchild')
		      )
		   )  {
			if ($form['action'] == 'addparent') {
				// update the relation between the two parents
				if ($form['person']['id'] < $relation_id) {
					$tabRel->person_id_1	= $form['person']['id'];
					$tabRel->person_id_2	= $relation_id;
				} else {
					$tabRel->person_id_2	= $form['person']['id'];
					$tabRel->person_id_1	= $relation_id;
				}
				$tabRel->orderNumber_1 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_1, 'partner');
				$tabRel->orderNumber_2 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_2, 'partner');			
				$tabRel->type			= 'partner';
				$tabRel->subtype		= 'spouse';
				
				// Make sure the table is valid
				if (!$tabRel->check()) {
					$this->setError('Error checking relation (2): ');
					return false;
				// Else store the table to the database
				} else if (!$tabRel->store(false)) {
					$this->setError('Error saving relation (2): '.$tabRel->getError());
					return false;
				}
			} 
			
			if ($form['action'] == 'addparent') {
				// update the relation between the child and second parent
				$tabRel->person_id_1	= $form['person']['relations']['id'][0];
			} else if ($form['action'] == 'addchild') {
				// update the relation between the child and second parent
				$tabRel->person_id_1	= $form['person']['id'];
			}
			
			$tabRel->person_id_2	= $relation_id;
			$tabRel->orderNumber_1 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_1, 'parent');
			$tabRel->orderNumber_2 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_2, 'child');
			$sex = $this->getSex($form['person']['app_id'], $relation_id);			
			$tabRel->type			= ($sex == 'F') ? 'mother' : 'father';
			$tabRel->subtype		= $form['person']['relations']['relationtype'][0];
			
			// Make sure the table is valid
			if (!$tabRel->check()) {
				$this->setError('Error checking relation (3): ');
				return false;
			// Else store the table to the database
			} else if (!$tabRel->store(false)) {
				$this->setError('Error saving relation (3): '.$tabRel->getError());
				return false;
			}
			
			$this->relationIds[] = $relation_id;
		}
		// end of storing relationship between two parents / second parent - child
		
		// If it is a second parent of 1 or more children, this relationship has to be added too.
		if ((isset($family_id)) && ($family_id != '0') && ($form['action'] == 'addpartner')) {
			$children = $this->getChildren($tabRel->app_id, $family_id);
			
			$tabRel->person_id_2	= $form['person']['id'];
			$sex = $form['person']['sex'];
			$tabRel->type			= ($sex == 'F') ? 'mother' : 'father';
			$tabRel->subtype		= $form['person']['relations']['relationtype'][0];
			
			foreach ($children as $child) {
				$tabRel->person_id_1	= $child->id;		
				$tabRel->orderNumber_1 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_1, 'parent');
				$tabRel->orderNumber_2 	= $this->getOrderNumber($tabRel->app_id, $tabRel->person_id_2, 'child');
				
				// Make sure the table is valid
				if (!$tabRel->check()) {
					$this->setError('Error check relation (4): ');
					return false;
				// Else store the table to the database
				} else if (!$tabRel->store(false)) {
					$this->setError('Error saving relation (4): '.$tabRel->getError());
					return false;
				}
				
				$this->relationIds[] = $child->id;
			}
		}
		// end of storing relationship between second parent - child
				
		return true;
	}
	
	private function update_person_relations(&$form) {
		$query = $this->_db->getQuery(true);
		$this->relationIds[] = $form['person']['id'];
		
		$tabRel	= JTable::getInstance('joaktree_relations', 'Table');
		// Bind the form fields to the table
		$tabRel->app_id			= $form['person']['app_id'];
		
		$numberRelations = count($form['person']['relations']['id']);
		$counter = 1;
		$parentId = '';
		for ($i=0, $n=$numberRelations; $i<$n; $i++) {
			$this->relationIds[] = $form['person']['relations']['id'][$i];
			
			if ($form['person']['relations']['status'][$i] == 'loaded_deleted') {	
				// Remove this person from the family
				$query->clear();
				
				// select the affected relationships
				$query->select(' jrn.person_id_1 ');
				$query->select(' jrn.person_id_2 ');
				$query->from(  ' #__joaktree_relations  jrn ');
				$query->where( ' jrn.app_id = '.$form['person']['app_id'].' ');
				$query->where( ' jrn.family_id = '.$this->_db->quote($form['person']['relations']['familyid'][$i]).' ');
				$query->where( ' (  jrn.person_id_1 = '.$this->_db->quote($form['person']['relations']['id'][$i]).' '
							 . ' OR jrn.person_id_2 = '.$this->_db->quote($form['person']['relations']['id'][$i]).' '
							 . ' ) '
							 );
							 
				$this->_db->setQuery($query);
				$pairs = $this->_db->loadObjectList();
				
				// save the affected relationships for later update
				foreach ($pairs as $pair) {
					$this->relationIds[] = $pair->person_id_1;
					$this->relationIds[] = $pair->person_id_2;
				}
				
				// delete the affected relationships
				$query->clear();
				$query->delete(' #__joaktree_relations ');
				$query->where( ' app_id = '.$form['person']['app_id'].' ');
				$query->where( ' family_id = '.$this->_db->quote($form['person']['relations']['familyid'][$i]).' ');
				$query->where( ' (  person_id_1 = '.$this->_db->quote($form['person']['relations']['id'][$i]).' '
							 . ' OR person_id_2 = '.$this->_db->quote($form['person']['relations']['id'][$i]).' '
							 . ' ) '
							 );
							 
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->setError('Error deleting relation: '.$this->_db->getErrorMsg());
					return false;
				}				
				
			} else {
				// just update the relationship
				switch ($form['type']) {
					case "partners"	: 	if ($form['person']['id'] < $form['person']['relations']['id'][$i]) {
											$tabRel->person_id_1	= $form['person']['id'];
											$tabRel->person_id_2	= $form['person']['relations']['id'][$i];
											$tabRel->orderNumber_1	= $counter++;					
										} else {
											$tabRel->person_id_1	= $form['person']['relations']['id'][$i];
											$tabRel->person_id_2	= $form['person']['id'];
											$tabRel->orderNumber_2	= $counter++;					
										}
										$tabRel->subtype		= $form['person']['relations']['partnertype'][$i];
										break;
					case "parents"	: 	$tabRel->person_id_1	= $form['person']['id'];
										$tabRel->person_id_2	= $form['person']['relations']['id'][$i];
										$tabRel->orderNumber_1	= $counter++;
										$tabRel->subtype		= $form['person']['relations']['relationtype'][$i];
										break;
					case "children"	: 	
					default:			
										if ($parentId != $form['person']['relations']['parentid'][$i]) {
											// new parent
											$parentId = $form['person']['relations']['parentid'][$i];
											$counter  = 1; 
											$this->relationIds[] = $form['person']['relations']['parentid'][$i];
										}
										$tabRel->person_id_1	= $form['person']['relations']['id'][$i];
										$tabRel->person_id_2	= $form['person']['id'];
										$tabRel->orderNumber_2	= $counter++;
										$tabRel->subtype		= $form['person']['relations']['relationtype'][$i];
										break;
				}
				
				if (!$tabRel->store(false)) {
					$this->setError('Error saving relation (5): '.$tabRel->getError());
					return false;
				}
				
				if (($form['type'] == 'children') && (!empty($form['person']['relations']['parentid'][$i]))) {
					// in case of children - update the order of the second parent too
					$tabRel->person_id_2	= $form['person']['relations']['parentid'][$i];
					if (!$tabRel->store(false)) {
						$this->setError('Error saving relation (6): '.$tabRel->getError());
						return false;
					}
				}				
			}			
		}
				
		return true;
	}
	
	private function save_person_trees(&$form) {
		// Linking person to trees
		$personTrees	= array();
		
		$tabTreePerson	= JTable::getInstance('joaktree_tree_persons', 'Table');
		// Bind the form fields to the table
		$tabTreePerson->app_id      = $form['person']['app_id'];
		$tabTreePerson->person_id	= $form['person']['id'];
		
		// new person without relation
		if (!isset($form['person']['relations']['id'][0])) {
			$tabTreePerson->id          = $form['person']['id'].'+'.$form['person']['default_tree_id'];
			$tabTreePerson->tree_id     = $form['person']['default_tree_id'];
			$tabTreePerson->type 	    = 'R';
			$tabTreePerson->lineage		= null;
			
			// Make sure the table is valid
			if (!$tabTreePerson->check()) {
				$this->setError('Error checking tree-person: ');
				return false;
			// Else store the table to the database
			} else if (!$tabTreePerson->store(false)) {
				$this->setError('Error saving tree-person: '.$tabTreePerson->getError());
				return false;
			}
			
			$personTrees[] = $form['person']['default_tree_id'];	
			
		} else {
			// fetch the trees of the relation
			$relationTrees 	= $this->getTrees($form['person']['app_id'], $form['person']['relations']['id'][0]);
			
			foreach ($relationTrees as $tree) {
				$tabTreePerson->id          = $form['person']['id'].'+'.$tree->tree_id;
				$tabTreePerson->tree_id     = $tree->tree_id;
				
				// check type of tree
				$indSave = false;
				if ($tree->holds == 'all') {
					$tabTreePerson->type 	    = 'R';
					$tabTreePerson->lineage		= null;
					$indSave 					= true;
				} else {
					// check type of action
					switch ($form['action']) {
						case "addchild":	$tabTreePerson->type 	    = 'C';
											$tabTreePerson->lineage		
												= $tree->lineage.' '.$form['person']['id'];
											$indSave 					= true;
											break;
						case "addpartner":	$tabTreePerson->type 	    = 'P';
											$tabTreePerson->lineage		= null;
											$indSave 					= true;
											break;
						case "addparent": 	// continue
						default:			// do nothing
											$indSave 					= false;
											break;
					}
				}
				
				if ($indSave) {	
					// Make sure the table is valid
					if (!$tabTreePerson->check()) {
						$this->setError('Error check tree-person: ');
						return false;
					// Else store the table to the database
					} else if (!$tabTreePerson->store(false)) {
						$this->setError('Error saving tree-person: '.$tabTreePerson->getError());
						return false;
					}
					// keep the tree for finding the possible default tree later on.
					$personTrees[] = $tree->tree_id;	
				}
			}
		}
	   	
	   	// find the default tree
	   	if (!in_array($form['person']['default_tree_id'], $personTrees)) {
	   		// the given default tree is not a tree for this person, we have to try to find another one.
	   		$form['person']['default_tree_id'] = (count($personTrees)) ?  $personTrees[0] : null;
	   	} // Else: the given default tree is also a tree for this person: we keep it! 	
	   		   	
		return true;
	}
	
	private function save_media(&$form) {
		// saving link for 1 picture
		
		if (!empty($form['person']['media']['path_file'][0])) {			
			$docId = ($form['person']['media']['status'][0] == 'new') 
						? null 
						: $form['person']['media']['id'][0];
			
			// Store the document
			$tabMedia	= JTable::getInstance('joaktree_documents', 'Table');
			// Bind the form fields to the table
			$tabMedia->app_id	= $form['person']['app_id'];
			$tabMedia->id		= $docId;	
			
			// file
			$params	= JoaktreeHelper::getJTParams();
			$gedcomroot = $params->get('gedcomDocumentRoot');
			$joomlaroot = $params->get('joomlaDocumentRoot');
			$tabMedia->file 	= str_replace($joomlaroot, $gedcomroot, $form['person']['media']['path_file'][0] );			
			
			$tabMedia->title	= htmlspecialchars($form['person']['media']['title'][0], ENT_QUOTES, 'UTF-8');
		
			// fileformat
			$format = explode('.', $form['person']['media']['path_file'][0]);
			$tabMedia->fileformat = strtoupper(array_pop($format));
			
			// not used yet
			$tabMedia->indCitation 	= false;
			$tabMedia->note_id 		= null;
			$tabMedia->note 		= null; //htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
			
			// Make sure the table is valid
			if (!$tabMedia->check()) {
				$this->setError('Error checking media: ');
				return false;
			// Else store the table to the database
			} else {
				$docId = $tabMedia->store(false);
				if (!$docId) {
					$this->setError('Error saving media: '.$tabMedia->getError());
					return false;
				}
			}
			
			// link document to person
			$tabDocPerson	= JTable::getInstance('joaktree_person_documents', 'Table');
			$tabDocPerson->app_id		= $form['person']['app_id'];
			$tabDocPerson->person_id	= $form['person']['id'];
			$tabDocPerson->document_id	= $docId;		
			
			// Make sure the table is valid
			if (!$tabDocPerson->check()) {
				$this->setError('Error checking document-person: ');
				return false;
			// Else store the table to the database
			} else if (!$tabDocPerson->store(false)) {
				$this->setError('Error saving document-person: '.$tabDocPerson->getError());
				return false;
			}
		}
		
		return true;		
	}
	
	private function save_medialist(&$form) {
		// deleting links for pictures
	
		$tabDocPerson	= JTable::getInstance('joaktree_person_documents', 'Table');
		$tabDocPerson->app_id		= $form['person']['app_id'];
		$tabDocPerson->person_id	= $form['person']['id'];
		
		$tabMedia	= JTable::getInstance('joaktree_documents', 'Table');
		$tabMedia->app_id	= $form['person']['app_id'];
		
		// setup query
		$query = $this->_db->getQuery(true);

		for ($i=0; $i<count($form['person']['media']['id']); $i++){
			$tabDocPerson->document_id	= $form['person']['media']['id'][$i];		
			if ($form['person']['media']['status'][$i] == 'loaded_deleted') {
				// Delete row from the database
				if (!$tabDocPerson->delete()) {
					$this->setError('Error deleting document-person: '.$tabDocPerson->getError());
					return false;
				}
					
				$query->clear();
				$query->select(' count( document_id ) AS number ');
				$query->from(  ' #__joaktree_person_documents ');
				$query->where( ' app_id = '.$form['person']['app_id'].' ');
				$query->where( ' document_id = '.$this->_db->quote($form['person']['media']['id'][$i]).' '); 
				$this->_db->setQuery($query);
				
				$result = $this->_db->loadResult();
				
				if (!$result) {
					$tabMedia->id		= $form['person']['media']['id'][$i];
					// Delete row from the database
					if (!$tabMedia->delete()) {
						$this->setError('Error deleting media: '.$tabMedia->getError());
						return false;
					}
				}
			}
		}
				
		return true;		
	}
	
	private function getTrees($appId, $personId) {
		$query = $this->_db->getQuery(true);
		
		$query->select( ' jtp.tree_id ' );
		$query->select( ' jtp.lineage ' );
		$query->from(   ' #__joaktree_tree_persons  jtp ');
		$query->where(  ' jtp.app_id    = '.$appId.' ');
		$query->where(  ' jtp.person_id = '.$this->_db->quote($personId).' ');
		
		$query->select( ' jte.holds ' );
		$query->innerJoin(' #__joaktree_trees  jte '
						 .' ON (   jte.app_id = jtp.app_id '
						 .'    AND jte.id     = jtp.tree_id '
						 .'    ) '
						 );
						 
		
		$this->_db->setQuery($query);
		$trees = $this->_db->loadObjectList();			
		
		return $trees; 
	}
	
	private function getOrderNumber($appId, $personId, $type) {
		$query = $this->_db->getQuery(true);
		
		switch ($type) {
			case "child"	:
				$query->select(' MAX(IFNULL(jrn.orderNumber_2, 0)) AS count ');
				$query->where( ' jrn.person_id_2 = '.$this->_db->quote($personId).' ');
				$query->where( ' jrn.type IN ('.$this->_db->quote('father').', '.$this->_db->quote('mother').') ');
				break;
			case "parent"	:
				$query->select(' MAX(IFNULL(jrn.orderNumber_1, 0)) AS count ');
				$query->where( ' jrn.person_id_1 = '.$this->_db->quote($personId).' ');
				$query->where( ' jrn.type IN ('.$this->_db->quote('father').', '.$this->_db->quote('mother').') ');
				break;
			case "partner"	:
				$query->select(' MAX( IF( IFNULL(jrn.orderNumber_1, 0) > IFNULL(jrn.orderNumber_2, 0) '
				              .'        , IFNULL(jrn.orderNumber_1, 0) '
				              .'        , IFNULL(jrn.orderNumber_2, 0) '
				              .'        ) ' 
				              .'    ) AS count ' 
				              );				              
				$query->where( ' (  jrn.person_id_1 = '.$this->_db->quote($personId).' '
							 . ' OR jrn.person_id_2 = '.$this->_db->quote($personId).' '
							 . ' ) '
							 );
				$query->where( ' jrn.type = '.$this->_db->quote('partner').' ');
				break;
			default : break;
		}
		
		$query->from(  ' #__joaktree_relations  jrn ');
		$query->where( ' jrn.app_id      = '.$appId.' ');
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		$result = ((int) $result) + 1;
		
		return $result;
	}
	
	private function check($tmpId, $type) {
		$query = $this->_db->getQuery(true);
		$query->select(' 1 ');
		
		switch ($type) {
			case 'person':
				$query->from(  ' #__joaktree_persons ');
				$query->where( ' id   = '.$this->_db->quote($tmpId).' ');
				break;
				
			case 'note':
				$query->from(  ' #__joaktree_notes ');
				$query->where( ' id   = '.$this->_db->quote($tmpId).' ');
				break;
				
			case 'family':
				$query->from(  ' #__joaktree_relations ');
				$query->where( ' family_id   = '.$this->_db->quote($tmpId).' ');
				break;
			default:
				$query->from(  ' dual ');
				break;	
		}
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		
		// ID is alreadey used -> return false
		// ID is not used in the selected table -> return true
		return ($result) ? false : true;
	}
	
	private function checkFamilyId($appId, $pid1, $pid2, $familyId) {
		$query = $this->_db->getQuery(true);
		
		$query->select(' jrn.family_id ');
		$query->from(  ' #__joaktree_relations  jrn ');
		$query->where( ' jrn.app_id      = '.$appId.' ');
		$query->where( ' jrn.family_id   = '.$this->_db->quote($familyId).' ');
		$query->where( ' jrn.type        = '.$this->_db->quote('partner').' ');
		$query->where( ' jrn.person_id_1 IN ('.$this->_db->quote($pid1).', '.$this->_db->quote($pid2).') ');
		$query->where( ' jrn.person_id_2 NOT IN ('.$this->_db->quote($pid1).', '.$this->_db->quote($pid2).') ');
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		
		if ($result) {		
			// retreive ID and double check that it is not used
			$continue = true;
			$i = 0;
			while ($continue) {
				$tmpId = JoaktreeHelper::generateJTId();
				$i++;
				
				if ($this->check($tmpId, 'family')) {
					$familyId = $tmpId;
					$continue = false;
					break;
				}
				if ($i > 100) {
					$continue = false;
					return false;
				}
			}
		}
		
		return $familyId;		
	}
	
	private function getChildren($appId, $familyId) {
		$query = $this->_db->getQuery(true);
		
		$query->select(' jrn.person_id_1 AS id ');
		$query->from(  ' #__joaktree_relations  jrn ');
		$query->where( ' jrn.app_id      = '.$appId.' ');
		$query->where( ' jrn.family_id   = '.$this->_db->quote($familyId).' ');
		$query->where( ' jrn.type        IN ('.$this->_db->quote('father').', '.$this->_db->quote('mother').') ');
		
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		
		return $result;
	}
}
?>