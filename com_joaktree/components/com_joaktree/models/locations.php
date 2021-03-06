<?php
/**
 * Joomla! component Joaktree
 * file		front end locations model - locations.php
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

// Import Joomla! libraries
jimport('joomla.application.component.model');
JLoader::register('MBJServiceInteractivemap', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'services'.DS.'interactivemap.php');

class JoaktreeModelLocations extends JModel {

	function __construct() {
		parent::__construct();			
	}

 	public function getTreeId() {
		return JoaktreeHelper::getTreeId();
	}
	
	public static function getAccess() {
		return JoaktreeHelper::getAccessTree();
	}
	
	public static function getInteractiveMap() {
		return MBJServiceInteractivemap::isActivated();
	}

	public function getLocationIndex() {
		$app 	= JFactory::getApplication('site');
		$index	= $app->getUserState('joaktree.locations.index');
		
		if(!$index) {
			$index = array();
			
			// retrieve the index information from the database
			$query = $this->_db->getQuery(true);
			$query->select(' DISTINCT indexLoc ');
			$query->from(  ' #__joaktree_locations ');
			$query->order( ' indexLoc ');
			$this->_db->setQuery($query);
			$results = $this->_db->loadRowList();

			$params = JoaktreeHelper::getJTParams();
			$groupCountLoc = (int) $params->get('groupCountLoc', '3');
			$i = 0; 
			$n = 0;

			foreach ($results as $result) {				
				$n++;
				$index[$i][$n] =array_shift($result);
				if (($n == $groupCountLoc) || ($groupCountLoc == 0)) {
					$i++;
					$n = 0;
				} 
			}			
			
			 $app->setUserState('joaktree.locations.index', $index);
		}
		
		return $index;
	}
	
	public static function getLocationFilter() {
		static $filterId;
		
		if (!isset($filterId)) {
			$app 		= JFactory::getApplication('site');
			$params 	= $app->getParams();
			$indCookie  = $params->get('indCookies', true);	
			
			if ($indCookie) {
				// we return the cookie
				$filterId	= (int) JRequest::getInt('jt_loc_index', null, 'COOKIE', 0);
			} else {
				// we return 0
				$filterId	= 0;
			} 
		}
				
		return $filterId;
	}
	
	public static function getMapUrl() {
		static $mapUrl;
		
		if (!isset($mapUrl)) {
			$app 		= JFactory::getApplication('site');
			$params 	= $app->getParams();
			$indCookie  = $params->get('indCookies', true);	
			
			if ($indCookie) {
				// we return the cookie
				$mapUrl	= JRequest::getVar('jt_loc_url', null, 'COOKIE');
			} else {
				// we return 0
				$mapUrl	= null;
			} 
		}
				
		return $mapUrl;
	}
	
	private function _buildQuery()
	{
		$levels			= JoaktreeHelper::getUserAccessLevels();
		$displayAccess	= JoaktreeHelper::getDisplayAccess();
		$treeId     	= intval( $this->getTreeId() );
		
		$query	= $this->_db->getQuery(true);
		$query->select(' DISTINCT jln.value    AS location ');
		$query->select(' jln.id       AS loc_id ');		
		$query->select(' !(ISNULL(jln.latitude) AND ISNULL(jln.longitude))  AS indGeocode ');		
		$query->from(  ' #__joaktree_locations  jln ');
						 
		$query->innerJoin(' ( SELECT jpe.person_id     AS person_id_1 '
					.'   ,      NULL              AS person_id_2 '
					.'   ,      jpe.code          AS code '
					.'   ,      '.$this->_db->Quote( 'person' ).' AS level '
					.'   ,      jpe.loc_id        AS loc_id '
					.'   ,      jpe.app_id        AS app_id '
					.'   ,      jdsp.access       AS access '
					.'   ,      jdsp.accessLiving AS accessLiving '
					.'   FROM   #__joaktree_person_events  jpe '
					.'   INNER JOIN #__joaktree_display_settings  jdsp '
					.'   ON (    jdsp.code        = jpe.code '
					.'      AND  jdsp.level       = '.$this->_db->Quote( 'person' ).' '
					.'      AND  jdsp.published   = true '
					.'      ) '
					.'   WHERE  jpe.location      IS NOT NULL '
					.'   UNION '
					.'   SELECT jre.person_id_1   AS person_id_1 '
					.'   ,      jre.person_id_2   AS person_id_2 '
					.'   ,      jre.code          AS code '
					.'   ,      '.$this->_db->Quote( 'relation' ).' AS level '
					.'   ,      jre.loc_id        AS loc_id '
					.'   ,      jre.app_id        AS app_id '
					.'   ,      jdsr.access       AS access '
					.'   ,      jdsr.accessLiving AS accessLiving '
					.'   FROM   #__joaktree_relation_events  jre '
					.'   INNER JOIN #__joaktree_display_settings  jdsr '
					.'   ON (    jdsr.code        = jre.code '
					.'      AND  jdsr.level       = '.$this->_db->Quote( 'relation' ).' '
					.'      AND  jdsr.published   = true '
					.'      ) '
					.'   WHERE  jre.location     IS NOT NULL '
					.'   ) AS iv_event '
				    .' ON (   iv_event.loc_id = jln.id ) '
					);
					
		$query->innerJoin(' #__joaktree_tree_persons jtp '
						 .' ON (   jtp.app_id    = iv_event.app_id '
						 .'    AND jtp.tree_id   = '.(int) $treeId.' '
						 .'    AND jtp.person_id IN (iv_event.person_id_1, iv_event.person_id_2) '
						 .'    ) '
						 );
		$query->innerJoin(' #__joaktree_trees      jte '
						 .' ON (   jte.app_id    = jtp.app_id '
						 .'    AND jte.id        = jtp.tree_id '
						 .'    AND jte.published = true '
						 .'    AND jte.access    IN ' .$levels.' '
						 .'    ) '
						 );

		$query->innerJoin(' #__joaktree_admin_persons     jan1 '
						 .' ON (   jan1.app_id    = iv_event.app_id '
						 .'    AND jan1.id        = iv_event.person_id_1 '
						 .'    AND jan1.published = true '
			             // privacy filter
						 .'    AND (  (   jan1.living = false '
						 .'           AND '.$displayAccess['NAMEname']->notLiving.' > 1 '
						 .'           AND iv_event.access IN '.$levels.' ' 
						 .'           ) '
						 .'        OR (   jan1.living = true  '
						 .'           AND '.$displayAccess['NAMEname']->living.'    > 1 '
						 .'           AND iv_event.accessLiving IN '.$levels.' ' 
						 .'           ) '
						 .'        ) '
						 .'    ) '
						 );
		$query->innerJoin(' #__joaktree_admin_persons     jan2 '
						 .' ON (   jan2.app_id    = iv_event.app_id '
						 .'    AND jan2.id        = IFNULL(iv_event.person_id_2, iv_event.person_id_1) '
						 .'    AND jan2.published = true '
			             // privacy filter
						 .'    AND (  (   jan2.living = false '
						 .'           AND '.$displayAccess['NAMEname']->notLiving.' > 1 '
						 .'           AND iv_event.access IN '.$levels.' ' 
						 .'           ) '
						 .'        OR (   jan2.living = true  '
						 .'           AND '.$displayAccess['NAMEname']->living.'    > 1 '
						 .'           AND iv_event.accessLiving IN '.$levels.' ' 
						 .'           ) '
						 .'        ) '
						 .'    ) '
						 );
						 
		// Get the WHERE clauses for the query + GROUP BY
		$wheres      	=  $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
		$query->order(' jln.value ');
						 		
		return $query;
	}

	private function _buildContentWhere()
	{	
		// always from the request
		$tmp		= JRequest::getVar('filter');
		
		// if nothing found, check the cookie -- only when we use cookies
		if (!isset($tmp)) {
			$app 		= JFactory::getApplication('site');
			$params 	= $app->getParams();
			
			if ($params->get('indCookies', true)) {
				$tmp = JRequest::getInt('jt_loc_index', null, 'COOKIE', 0);
			}
		}
		
		// if nothing found, we use 0
		$filterId = (!isset($tmp)) ? 0 : (int) $tmp;	
		$index  	= $this->getLocationIndex();
		$filters	= $index[$filterId];
		
		$wheres 	= array();
		$wheres[] 	= ' jln.indDeleted = 0 ';
		
        if ($filters) {
			$wheres[] = (count($filters) == 1) 
							? ' jln.indexLoc =  "'.array_shift($filters).'" '
							: ' jln.indexLoc IN ("'.implode('","', $filters).'" ) ';
        }
		
		return $wheres;
	}

	public function getLocationlist() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_locationlist)) {
			$query = $this->_buildQuery();			
			$this->_locationlist = $this->_getList( $query );		
		}
		
		return $this->_locationlist;
	}

	public function getTreeinfo() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_treeinfo)) {
			$query	= $this->_db->getQuery(true);
			$query->select(' * ');
			$query->from(  ' #__joaktree_trees ');
			$query->where( ' id = ' . intval( $this->getTreeId() ) . ' ');
			$query->where( ' access IN ' . JoaktreeHelper::getUserAccessLevels().' ');
			
			$this->_db->setQuery($query);
			$this->_treeinfo = $this->_db->loadObject();
		}
		
		return $this->_treeinfo;
	}

	public function getMenus() {
		static $_menuTreeId 	= array();
		
		// retrieve the menu item ids - if not done yet
		if ( count($_menuTreeId) == 0 ) {
			$_menuTreeId = JoaktreeHelper::getMenus('joaktreelist');
		}
		
		return $_menuTreeId;
	}

	public function getLastUpdate() {
		return JoaktreeHelper::lastUpdateDateTime();	
	}
}
?>