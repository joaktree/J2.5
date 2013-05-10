<?php
/**
 * Joomla! component Joaktree
 * file		JoaktreeHelper - helper.php
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

jimport( 'joomla.filesystem.folder' ); 
jimport( 'joomla.filesystem.file' );

class JoaktreeHelper {
	
	public function getIdlength() {
		// ID length = 20
		return 20;
	}
	
	/* 
	** function for retrieving version number from config.xml
	*/
	public function getJoaktreeVersion() {
		// get the folder and xml-files
		$folder = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_joaktree';
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.'com_joaktree';
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}
		
		// loop through the xml-files
		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		// return the found version
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}
	
	public static function jsfile() {
		$ds = '/';
		return 'administrator'.$ds.'components'.$ds.'com_joaktree'.$ds.'assets'.$ds.'js'.$ds.'joaktree_admin.js';
	}
	
	public static function joaktreecss() {		
		$ds = '/';
		return 'administrator'.$ds.'components'.$ds.'com_joaktree'.$ds.'assets'.$ds.'css'.$ds.'joaktree_admin.css';
	}
	
	public static function getActions($asset = 'com_joaktree') {
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $asset));
		}

		// special treatement for media - we take the authorisation from media manager
		$result->set('media.create', $user->authorise('core.create', 'com_media'));
		
		return $result;
	}
	
	public function getApplications() {
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);

		// retrieve the names
		$query->select(' id AS value ');
		$query->select(' title AS text ');
		$query->from(  ' #__joaktree_applications ');
				
		$db->setQuery($query);
		$applications = $db->loadObjectList();

		return $applications;
	}
	
	public function getThemes() {
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		// retrieve the names
		$query->select(' id AS value ');
		$query->select(' name AS text ');
		$query->from(  ' #__joaktree_themes ');
				
		$db->setQuery($query);
		$themes = $db->loadObjectList();

		return $themes;
	}
	
	public function getThemeName($id) {
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$name = '';
		
		// retrieve the name
		$query->select(' name ');
		$query->from(  ' #__joaktree_themes ');
		$query->where( ' id   = '.(int) $id.' ');
		
		$db->setQuery($query);
		$name = $db->loadResult();
		
		return $name;
	}
	
	public function getJTParams($app_id) {
		static $_params;
		static $localAppId;

		if ((!isset($_params)) || ($localAppId != $app_id)) {
			// Load the parameters.
			$_params = JComponentHelper::getParams('com_joaktree') ;
			
			if (!empty($app_id)) {
				// retrieve the parameters of the application source
				$appParams  = self::getApplicationParams($app_id);
			
				// merge all parameters
				$_params->merge($appParams);
				
				//	set the local app id
				$localAppId = $app_id;
			}
		}
		
		return $_params;
	}
	
	public function getApplicationParams($app_id) {
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		// retrieve the app parameters
		$query->select(' japp.params ');
		$query->from(  ' #__joaktree_applications  japp ');
		$query->where( ' japp.id =  '.(int) $app_id.' ');
		
		$db->setQuery($query);
		$appSource = $db->loadObject();

		$registry = new JRegistry;
		
		if (is_object($appSource)) {
			// load parameters into registry object
			$registry->loadString($appSource->params, 'JSON');
			unset($appSource->params);
		}
				
		return $registry;
	}
	
	private function _getConcatenatedName($attribs) {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			return $query->concatenate($attribs, ' ');
	}
	
	public function getConcatenatedFamilyName() {
		static $concatTxt;
		
		if (empty($concatTxt)) {
			$attribs = array();
			$attribs[] = 'jpn.namePreposition';
			$attribs[] = 'jpn.familyName';
			$concatTxt = self::_getConcatenatedName($attribs);	
		}
		
		return ' '.$concatTxt.' ';
	}
	
	public function getConcatenatedFullName() {
		static $concatTxt;
		
		if (empty($concatTxt)) {
			$attribs = array();
			$attribs[] = 'jpn.firstName';
			$attribs[] = 'jpn.namePreposition';
			$attribs[] = 'jpn.familyName';
			$concatTxt = self::_getConcatenatedName($attribs);	
		}
		
		return ' '.$concatTxt.' ';
	}	
}
?>