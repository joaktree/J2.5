<?php
/**
 * Joomla! component Joaktree
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

// Initialize the database
$db =& JFactory::getDBO();
$update_queries = array ();
$application = JFactory::getApplication();

// Do not drop tables, because they contain user settings
// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_admin_persons ';
// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_applications ';
// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_display_settings ';
// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_themes ';
// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_trees ';
// $update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_registry_items ';

// Drop the following tables
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_citations ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_documents ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_gedcom_objectlines ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_gedcom_objects ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_locations ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_logremovals ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_logs ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_maps ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_notes ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_persons ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_documents ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_events ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_names ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_person_notes ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_relations ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_relation_events ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_relation_notes ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_repositories ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_sources ';
$update_queries[] = 'DROP TABLE IF EXISTS #__joaktree_tree_persons ';

// Perform all queries - we don't care if it fails
foreach( $update_queries as $query ) {
    $db->setQuery( $query );
    $db->query();
}

// Set a simple message
$application->enqueueMessage( JText::_( 'NOTE: Six database tables were NOT removed to allow for upgrades' ), 'notice' ) ;
?>