<?php
/**
 * Joomla! component Joaktree
 * file		front end component - joaktree.php
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

// Require the base controller
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_COMPONENT.DS.'controller.php';
require_once(JPATH_COMPONENT.DS.'helper'.DS.'helper.php');

//load the GedCom language file
$lang 	= JFactory::getLanguage();
$lang->load('com_joaktree.gedcom', JPATH_ADMINISTRATOR);

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname    = 'JoaktreeController'.ucfirst($controller);
$controller   = new $classname( );

$controller->execute( JRequest::getWord( 'task' )  );   // null );

// Redirect if set by the controller
$controller->redirect();
?>