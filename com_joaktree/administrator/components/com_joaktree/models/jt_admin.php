<?php
/**
 * Joomla! component Joaktree
 * file		jt_admin model - jt_admin.php
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

// Import Joomla! libraries
jimport('joomla.application.component.modellist');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

class JoaktreeModelJt_admin extends JModelList {

	var $_persons;
	var $_trees;
	var $_pagination = null;
	var $_total         = null;

	function __construct() {
		parent::__construct();

		$app = JFactory::getApplication();

		$context	= 'com_joaktree.jt_admin.list.';
		
		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	private function _buildQuery() {
		$query = $this->_db->getQuery(true);	
		
		// persons
		$query->select(' jpn.app_id                     AS app_id ');
		$query->select(' jpn.id                	        AS id ');
		$query->select(' MIN( jpn.firstName )        	AS firstName ');
		$query->select(' MIN( jpn.patronym )         	AS patronym ');
		$query->select(JoaktreeHelper::getConcatenatedFamilyName().' AS familyName ');
		$query->select(' MIN( jpn.sex )              	AS sex ');
		$query->from(  ' #__joaktree_persons jpn ');
		
		// person administration
		$query->select(' MIN( jan.default_tree_id )  	AS default_tree_id ');
		$query->select(' MIN( jan.published )         	AS published ');
		$query->select(' MIN( jan.living )            	AS living ');
		$query->select(' MIN( jan.page )              	AS page ');
		$query->select(' MIN( jan.map )              	AS map ');
		$query->select(' MIN( jan.robots )            	AS robots ');
		$query->innerJoin(' #__joaktree_admin_persons  jan'
						 .' ON (   jan.app_id = jpn.app_id '
						 .'    AND jan.id     = jpn.id '
						 .'    ) '
						 );
		
		// applications
		$query->select(' MIN( japp.title )              AS appTitle ');
		$query->innerJoin(' #__joaktree_applications  japp '
						 .' ON (japp.id = jpn.app_id) '
						 );
		
		// trees
		$query->select(' MIN( jte.name )              	AS familyTree ');
		$query->leftJoin(' #__joaktree_trees jte '
						.' ON (   jte.app_id     = jan.app_id '
						.'    AND jte.id         = jan.default_tree_id '
						.'    ) '
						);
						
		// births
		$query->select(' MIN( jpe1.eventDate )         	AS birthDate ');
		$query->select(' MIN( jpe1.location )        	AS birthPlace ');
		$query->leftJoin(' #__joaktree_person_events jpe1 '
						.' ON (   jpe1.app_id    = jpn.app_id '
						.'    AND jpe1.person_id = jpn.id '
						.'    AND jpe1.code = '.$this->_db->Quote('BIRT').' '
						.'    ) '
						);
						
		// deaths
		$query->select(' MIN( jpe2.eventDate )         	AS deathDate ');
		$query->select(' MIN( jpe2.location )        	AS deathPlace ');
		$query->leftJoin(' #__joaktree_person_events jpe2 '
						.' ON (   jpe2.app_id    = jpn.app_id '
						.'    AND jpe2.person_id = jpn.id '
						.'    AND jpe2.code = '.$this->_db->Quote('DEAT').' '
						.'    ) '
						);
						
		// burials
		$query->select(' MIN( jpe3.eventDate )        	AS burialDate ');
		$query->select(' MIN( jpe3.location )         	AS burialPlace ');
		$query->leftJoin(' #__joaktree_person_events jpe3 '
						.' ON (   jpe3.app_id    = jpn.app_id '
						.'    AND jpe3.person_id = jpn.id '
						.'    AND jpe3.code = '.$this->_db->Quote('BURI').' '
						.'    ) '
						);
						
		// person - period
		$attribs = array();
		$attribs[] = ' IFNULL(SUBSTR( RTRIM(jpe1.eventDate), -4 ), '.$this->_db->Quote('?').' ) ';
		$attribs[] = ' SUBSTR(IFNULL( RTRIM(jpe2.eventDate), RTRIM(jpe3.eventDate) ), -4) ';
		$query->select(' MIN('.$query->concatenate($attribs, ' - ').') AS period ');

		// WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres  =  $this->_buildContentWhere();		
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
		$query->group(' jpn.id ');
		$query->group(' jpn.app_id ');
		$query->order(' '.$this->_buildContentOrderBy().' ');
		
		// ready
		return $query;
	}

	private function _buildContentWhere() {
		$app = JFactory::getApplication();
				
		$context		= 'com_joaktree.jt_admin.list.';
		$filter_state	= $app->getUserStateFromRequest( $context.'filter_state',	'filter_state',		'',	'word' );
		$filter_living	= $app->getUserStateFromRequest( $context.'filter_living',	'filter_living',	'',	'word' );
		$filter_page	= $app->getUserStateFromRequest( $context.'filter_page',	'filter_page',		'',	'word' );
		$filter_map		= $app->getUserStateFromRequest( $context.'filter_map',		'filter_map',		-1,	'int' );
		$filter_tree	= $app->getUserStateFromRequest( $context.'filter_tree',	'filter_tree',		0,	'int' );
		$filter_apptitle = $app->getUserStateFromRequest( $context.'filter_apptitle','filter_apptitle',	0,	'int' );
		$filter_robots	= $app->getUserStateFromRequest( $context.'filter_robots',	'filter_robots',	-1,	'int' );
		$search1		= $app->getUserStateFromRequest( $context.'search1',		'search1',		'',	'string' );
		$search1		= JString::strtolower( $search1 );
		$search2		= $app->getUserStateFromRequest( $context.'search2',		'search2',		'',	'string' );
		$search2		= JString::strtolower( $search2 );
		$search3		= $app->getUserStateFromRequest( $context.'search3',		'search3',		'',	'string' );
		$search3		= JString::strtolower( $search3 );
		
		$where = array();
		
		if ($search1) {
			$where[] = 'LOWER(jpn.firstName) LIKE '.$this->_db->Quote('%'.$search1.'%');
		}
		
		if ($search2) {
			$where[] = 'LOWER(jpn.patronym) LIKE '.$this->_db->Quote('%'.$search2.'%');
		}
		
		if ($search3) {
			$where[] = 'LOWER(jpn.familyName) LIKE '.$this->_db->Quote('%'.$search3.'%');
		}
		
		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'jan.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'jan.published = 0';
			}
		}
		
		if ( $filter_living ) {
			if ( $filter_living == 'L' ) {
				$where[] = 'jan.living = 1';
			} else if ($filter_living == 'D' ) {
				$where[] = 'jan.living = 0';
			}
		}
		
		if ( $filter_page ) {
			if ( $filter_page == 'Y' ) {
				$where[] = 'jan.page = 1';
			} else if ($filter_page == 'N' ) {
				$where[] = 'jan.page = 0';
			}
		}
		
		if ( $filter_map >= 0 ) {
			$where[] = 'jan.map = ' . $filter_map;
		}
		
		if ( $filter_tree != 0 ) {
			$where[] = 'jan.default_tree_id = ' . $filter_tree;
		}
		
		if ( $filter_apptitle != 0 ) {
			$where[] = 'jpn.app_id = ' . $filter_apptitle;
		}
		
		if ( $filter_robots >= 0 ) {
			$where[] = 'jan.robots = ' . $filter_robots;
		}
		
		return $where;
	}

	private function _buildContentOrderBy() {
		$app = JFactory::getApplication();
		
		$context		= 'com_joaktree.jt_admin.list.';
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'jpn.id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		
		if ($filter_order == 'jpn.id'){
			$orderby 	= ' jpn.id '.$filter_order_Dir.' ';
		} else {
			$orderby 	= ' '.$filter_order.' '.$filter_order_Dir.' ';
		}

		return $orderby;
	}

	public function getPersons() {
		$query = $this->_buildQuery();
		$this->_persons = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
		
		return $this->_persons;
	}

   	public function getTrees() {
   		$query = $this->_db->getQuery(true);
   		$query->select(' id ');
   		$query->select(' name ');
   		$query->from(  ' #__joaktree_trees ');
   		$query->order( ' name ');
   			
		$this->_trees = $this->_getList( $query );
		
		return $this->_trees;
	}
	
	public function publish() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id					
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' published = !published ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');
				
				$this->_db->setQuery($query);
				$this->_db->query();
			}
			
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}

		return $return;
	}

	public function publishAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' published = 1 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function unpublishAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' published = 0 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');
				
				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function living() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' living = !living ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');
				
				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function livingAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' living = 1 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function notLivingAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' living = 0 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function page() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' page = !page ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function pageAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' page = 1 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function noPageAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit.state')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' page = 0 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	public function mapStatAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' map = 1 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	public function mapDynAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' map = 2 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	public function noMapAll() {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.edit')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($cids as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' map = 0 ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($cids));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}

	public function save($post) {
		$canDo	= JoaktreeHelper::getActions();	
		
		if ($canDo->get('core.edit')) {
			$cids = JRequest::getVar( 'cid' );
			
			foreach ($post['cid'] as $cid_num => $cid) {
				$id	 = explode('!', $cid);
				$id[0] = (int) $id[0];         // This is app_id
				$id[1] = substr($id[1], 0, (int) JoaktreeHelper::getIdlength()); // This is person_id
				
				$query = $this->_db->getQuery(true);
				$query->update(' #__joaktree_admin_persons ');
				$query->set(   ' robots = '.(int) $post['robot'.$id[1]].' ');
				$query->set(   ' map    = '.(int) $post['map'.$id[1]].' ');
				$query->where( ' app_id = '.$id[0].' ');
				$query->where( ' id     = '.$this->_db->quote( $id[1] ).' ');

				$this->_db->setQuery($query);
				$this->_db->query();				
			}
		
			$return = JText::sprintf('JTADMIN_PERSONS_UPDATED', count($post['cid']));
		} else {
			$return = JText::_('JT_NOTAUTHORISED');
		}
		
		return $return;
	}
	
	public function getTotal() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		
		return $this->_total;
	}

	public function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		
		return $this->_pagination;
	}
	
	public function getPatronymShowing() {
		$showPatr = false;

		// because patronym setting may differ for different applications
		// when at least one application has patronym showing is true
		// patronym column will be shown in the administrator.
		$query = $this->_db->getQuery(true);
		$query->select(' japp.params ');
		$query->from(  ' #__joaktree_applications  japp ');
		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		foreach ($results as $result)  {
			// load parameters into registry object
			$registry = new JRegistry;
			$registry->loadString($result->params, 'JSON');
			$patr = $registry->get('patronym');
			
			if ($patr > 0) {
				$showPatr = true;
			}
			
			unset($registry);
		}
		
		return $showPatr;
	}
	
	public function getColumnSettings() {
		$columns 	= array();
		$params  	= JComponentHelper::getParams('com_joaktree') ;
		$indCookie	= $params->get('indCookies', true);
		
		if ($indCookie) {
			// retrieve info from cookies
			// column GedCom = applications
			$tmp	= JRequest::getVar('jt_app', null, 'COOKIE', 1);
			$columns['app'] = (isset($tmp) && ($tmp == '1')) ? true : false;		
			unset($tmp);
			
			// column patronyms
			$tmp	= JRequest::getVar('jt_pat', null, 'COOKIE', 1);
			$columns['pat'] = (isset($tmp) && ($tmp == '1')) ? true : false;		
			unset($tmp);
	
			// column periods
			$tmp	= JRequest::getVar('jt_per', null, 'COOKIE', 1);
			$columns['per'] = (isset($tmp) && ($tmp == '1')) ? true : false;		
			unset($tmp);
	
			// column default trees
			$tmp	= JRequest::getVar('jt_tree', null, 'COOKIE', 1);
			$columns['tree'] = (isset($tmp) && ($tmp == '1')) ? true : false;		
			unset($tmp);
	
			// column map
			$tmp	= JRequest::getVar('jt_map', null, 'COOKIE', 1);
			$columns['map'] = (isset($tmp) && ($tmp == '1')) ? true : false;		
			unset($tmp);
			
			// column robots
			$tmp	= JRequest::getVar('jt_rob', null, 'COOKIE', 1);
			$columns['rob'] = (isset($tmp) && ($tmp == '1')) ? true : false;		
			unset($tmp);
		} else {
			// no cookies are used -> all columns will be shown
			// column GedCom = applications
			$columns['app'] = false;		
			
			// column patronyms
			$columns['pat'] = false;		
	
			// column periods
			$columns['per'] = false;			
	
			// column default trees
			$columns['tree'] = false;			
	
			// column map
			$columns['map'] = false;			
			
			// column robots
			$columns['rob'] = false;			
		}
		
		return $columns;
	}
}
?>