<?php
/**
 * Joomla! component Joaktree
 *
 * @version	1.4.3
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

// Version
$version = 'v1.4.3';

// Initialize the database
$db =& JFactory::getDBO();
$update_queries = array ();
$application = JFactory::getApplication();

// check type of install
$installer =& JInstaller::getInstance();
$manifest  =& $installer->getManifest();
if ($manifest) {
	$root =& $manifest->document;
}

$upgrade = $installer->getUpgrade();

// Inform the user what is going on
if (!$upgrade) {
	$application->enqueueMessage( 'New installation of Joaktree tables for version '.$version, 'notice' ) ;
} else {
	$application->enqueueMessage( 'Upgrade installation of Joaktree tables to '.$version, 'notice' ) ;
}

if (!$upgrade) {
	// New installation

	// Table joaktree_admin_persons
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_admin_persons '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', id               varchar(20)           NOT NULL '
	   .', default_tree_id  int(11)      default  NULL '
	   .', published        tinyint(1)            NOT NULL '
	   .', access           int(11)      unsigned NOT NULL '
	   .', living           tinyint(1)            NOT NULL '
	   .', page             tinyint(1)            NOT NULL '
	   .', PRIMARY KEY  (app_id, id) '
	   .') '
	   .'COMMENT="'.$version.'" ';

	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_admin_persons '
	   .'  MODIFY  id               varchar(20)           NOT NULL';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_admin_persons '
	   .'  ADD  robots              tinyint(2)            NOT NULL default 0 ' 
	   .'AFTER  page ';
	   	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_admin_persons '
	   .'  ADD  map                 tinyint(1)            NOT NULL default 0 ' 
	   .'AFTER  robots ';	   
	// end: joaktree_admin_persons 
	   
	// Table joaktree_applications
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_applications '
	   .'( id               tinyint(4)  unsigned  NOT NULL auto_increment '
	   .', asset_id         int(10)      unsigned NOT NULL '
	   .', title            varchar(30)           NOT NULL '
	   .', description      varchar(100)          NOT NULL '
	   .', programName      varchar(30)           NOT NULL '
	   .', params           varchar(2048)         NOT NULL '
	   .', PRIMARY KEY  (id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_applications 
	
	// Table joaktree_citations
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_citations '
	   .'( objectType       enum( ' . $db->Quote( 'person' )
	                          .', ' . $db->Quote( 'personName' )
	                          .', ' . $db->Quote( 'personEvent' )
	                          .', ' . $db->Quote( 'personNote' )
	                          .', ' . $db->Quote( 'personDocument' )
	                          .', ' . $db->Quote( 'relation' )
	                          .', ' . $db->Quote( 'relationEvent' )
	                          .', ' . $db->Quote( 'relationNote' )
				  .')                 NOT NULL '
	   .', objectOrderNumber smallint(2)          NOT NULL default 0 '
	   .', app_id           tinyint(4)            NOT NULL '
	   .', person_id_1      varchar(20)           NOT NULL '
	   .', person_id_2      varchar(20)           NOT NULL default '. $db->Quote( 'EMPTY' ).' '
	   .', source_id        varchar(20)           NOT NULL '
	   .', orderNumber      smallint(2)           NOT NULL '
	   .', dataQuality      tinyint(2)                NULL '
	   .', page             varchar(250)  default     NULL '
	   .', quotation        varchar(250)  default     NULL '
	   .', note             varchar(250)  default     NULL '
	   .', PRIMARY KEY  (objectType,objectOrderNumber,app_id,person_id_1,person_id_2,source_id,orderNumber) '
	   .', KEY person_id (app_id,person_id_1) '
	   .') '
	   .'COMMENT="'.$version.'" ';   
	// end: joaktree_citations

	// Table joaktree_display_settings
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_display_settings '
	   .'( id               int(11)      unsigned NOT NULL auto_increment '
	   .', code             varchar(4)            NOT NULL '
	   .', level            enum( ' . $db->Quote( 'person' )
	                          .', ' . $db->Quote( 'name' )
	                          .', ' . $db->Quote( 'relation' )
				  .')                 NOT NULL '
	   .', ordering         tinyint(3)            NOT NULL '
	   .', published        tinyint(1)   unsigned NOT NULL default 0 '
	   .', access           tinyint(3)   unsigned NOT NULL default 0 '
	   .', accessLiving     tinyint(3)   unsigned NOT NULL default 0 '
	   .', altLiving        tinyint(3)            NOT NULL default 0 '
	   .', PRIMARY KEY  (id) '
	   .', UNIQUE KEY UK_CODE_LEVEL (code, level) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	   
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ENGA", "relation", 1, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARB", "relation", 2, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARC", "relation", 3, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARS", "relation", 4, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARL", "relation", 5, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARR", "relation", 6, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ANUL", "relation", 7, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("DIV",  "relation", 8, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NCHI", "relation", 9, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EVEN", "relation", 10, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ENOT", "relation", 11, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ESOU", "relation", 12, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CAST", "person", 1, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("TITL", "person", 2, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BIRT", "person", 3, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BAPM", "person", 4, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BRTM", "person", 5, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CHR",  "person", 6, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BLES", "person", 7, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BARM", "person", 8, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BASM", "person", 9, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CONF", "person", 10, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ADOP", "person", 11, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CHRA", "person", 12, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("DEAT", "person", 13, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BURI", "person", 14, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CREM", "person", 15, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("YART", "person", 16, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("FCOM", "person", 17, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EDUC", "person", 18, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("GRAD", "person", 19, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("OCCU", "person", 20, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RETI", "person", 21, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EMIG", "person", 22, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("IMMI", "person", 23, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NATU", "person", 24, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NATI", "person", 25, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RESI", "person", 26, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RELI", "person", 27, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("DSCR", "person", 28, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("EVEN", "person", 29, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NOTE", "person", 30, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ENOT", "person", 31, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("SOUR", "person", 32, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ESOU", "person", 33, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NAME", "name", 1, 1, 1, 1, 1)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("GIVN", "name", 2, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NICK", "name", 3, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("ADPN", "name", 4, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("AKA",  "name", 5, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("BIRN", "name", 6, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CENN", "name", 7, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("CURN", "name", 8, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("FRKA", "name", 9, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("HEBN", "name", 10, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("INDG", "name", 11, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("MARN", "name", 12, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("RELN", "name", 13, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("OTHN", "name", 14, 0, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("NOTE", "name", 15, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("SOUR", "name", 16, 1, 1, 3, 0)';
	$update_queries[] = 'INSERT INTO #__joaktree_display_settings (code, level, ordering, published, access, accessLiving, altLiving ) VALUES ("SURN", "name", 17, 0, 1, 3, 0)';
	// end: joaktree_display_settings
	   
	// Table joaktree_documents
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_documents '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', id               varchar(20)           NOT NULL '
	   .', file             varchar(200)          NOT NULL '
	   .', fileformat       varchar(10)           NOT NULL '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', note_id          varchar(20)               NULL '
	   .', title            varchar(100)              NULL '
	   .', note             text         default      NULL '
	   .', PRIMARY KEY  (app_id,id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_documents
	   
	// Table joaktree_gedcom_objectlines
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_gedcom_objectlines '
	   .'( id               int(11)      unsigned NOT NULL auto_increment '
	   .', object_id        varchar(20)           NOT NULL '
	   .', order_nr         int(11)               NOT NULL '
	   .', level            int(11)               NOT NULL '
	   .', tag              varchar(20)           NOT NULL '
	   .', value            text         default      NULL '
	   .', PRIMARY KEY  (id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_gedcom_objectlines '
	   .'  ADD  subtype     enum( '   . $db->Quote( 'spouse' )
	                          	.', ' . $db->Quote( 'partner' )
				  				.', ' . $db->Quote( 'natural' )
				  				.', ' . $db->Quote( 'adopted' )
				  				.', ' . $db->Quote( 'step' )
				  				.', ' . $db->Quote( 'foster' )
				  				.', ' . $db->Quote( 'legal' )
				  				.')                   NULL '
	   .'AFTER  value ';   
	// end: joaktree_gedcom_objectlines
	   
	// Table joaktree_gedcom_objects   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_gedcom_objects '
	   .'( id               int(11)      unsigned NOT NULL auto_increment '
	   .', tag              varchar(4)            NOT NULL '
	   .', value            varchar(50)  default  NULL '
	   .', PRIMARY KEY  (id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_gedcom_objects
	
	// Table joaktree_locations   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_locations '
	   .'( id               int(11)      unsigned NOT NULL AUTO_INCREMENT '
	   .', indexLoc         varchar(1)            NOT NULL '
	   .', value            varchar(75)           NOT NULL '
	   .', latitude         decimal(10,7)             NULL '
	   .', longitude        decimal(10,7)             NULL '
	   .', indServerProcessed tinyint(1) unsigned NOT NULL default 0 '
	   .', indDeleted       tinyint(1)   unsigned NOT NULL default 0 '
	   .', results          tinyint(2)   unsigned     NULL '
	   .', resultValue      varchar(100)              NULL '
	   .', PRIMARY KEY  (id) '
	   .', KEY indexLoc (indexLoc) '
	   .') '
	   .'COMMENT="'.$version.'" ';	   
	// end: joaktree_locations
	   
	// Table joaktree_logremovals   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_logremovals '
	   .'( id               int(10)      unsigned NOT NULL AUTO_INCREMENT '
	   .', app_id           tinyint(4)   unsigned NOT NULL '
	   .', object_id        varchar(20)           NOT NULL '
	   .', object           enum( ' . $db->Quote( 'prsn' )
	                          .', ' . $db->Quote( 'sour' )
	                          .', ' . $db->Quote( 'repo' )
	                          .', ' . $db->Quote( 'docu' )
	                          .', ' . $db->Quote( 'note' )
	                          .')                 NOT NULL '
	   .', description      varchar(100)          NOT NULL '
	   .', PRIMARY KEY  (id) '
	   .', KEY objectIndex2 (app_id,object_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';	   
	// end: joaktree_logremovals
    
	// Table joaktree_logs   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_logs '
	   .'( id               int(10)      unsigned NOT NULL AUTO_INCREMENT '
	   .', app_id           tinyint(4)   unsigned NOT NULL '
	   .', object_id        varchar(20)           NOT NULL '
	   .', object           enum( ' . $db->Quote( 'prsn' )
	                          .', ' . $db->Quote( 'sour' )
	                          .', ' . $db->Quote( 'repo' )
	                          .', ' . $db->Quote( 'docu' )
	                          .', ' . $db->Quote( 'note' )
	                          .')                 NOT NULL '
	   .', changeDateTime   datetime              NOT NULL '
	   .', logevent         varchar(9)            NOT NULL '
	   .', user_id          int(11)               NOT NULL '
	   .', PRIMARY KEY  (id) '
	   .', KEY objectIndex1 (app_id,object_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
    // end: joaktree_logs

	// Table joaktree_maps   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_maps '
	   .'( id               int(11)      unsigned NOT NULL AUTO_INCREMENT '
	   .', name             varchar(50)           NOT NULL '
	   .', selection		enum( ' . $db->Quote( 'tree' )
	                          .', ' . $db->Quote( 'person' )
	   						  .', ' . $db->Quote( 'location' )
	                          .')                 NOT NULL '
	   .', service          varchar(20)           NOT NULL default '.$db->Quote( 'staticmap' ).' '
	   .', app_id           tinyint(4)   unsigned NOT NULL '
	   .', relations        tinyint(1)   unsigned NOT NULL default 0 '
	   .', params           varchar(2048)         NOT NULL '
	   .', tree_id          tinyint(4)   unsigned     NULL '
	   .', person_id        varchar(20)               NULL '
	   .', subject          varchar(50)               NULL '
	   .', period_start     int(11)      unsigned     NULL '
	   .', period_end       int(11)      unsigned     NULL '
	   .', excludePersonEvents   varchar(200)         NULL '
	   .', excludeRelationEvents varchar(200)         NULL '
	   .', PRIMARY KEY  (id) '
	   .') '
	   .'COMMENT="'.$version.'" ';	   
	// end: joaktree_maps
	   
	// Table joaktree_notes   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_notes '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', id               varchar(20)           NOT NULL '
	   .', value            text                      NULL '
	   .', PRIMARY KEY  (app_id,id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_notes
	   
	// Table joaktree_persons   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_persons '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', id               varchar(20)           NOT NULL '
	   .', indexNam         varchar(1)            NOT NULL '
	   .', firstName        varchar(50)           NOT NULL '
	   .', patronym         varchar(50)  default      NULL '
	   .', namePreposition  varchar(15)  default      NULL '
	   .', familyName       varchar(50)           NOT NULL '
	   .', sex              char(1)               NOT NULL '
	   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', indHasParent     tinyint(1)   unsigned NOT NULL default 0 '
	   .', indHasPartner    tinyint(1)   unsigned NOT NULL default 0 '
	   .', indHasChild      tinyint(1)   unsigned NOT NULL default 0 '
	   .', lastUpdateTimeStamp '
	   .'                   timestamp             NOT NULL '
	   .'                   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP '
	   .', indIsWitness     tinyint(1)   unsigned NOT NULL default 0 '
	   .', prefix           varchar(20)               NULL '
	   .', suffix           varchar(20)               NULL '
	   .', PRIMARY KEY  (app_id,id) '
	   .', KEY IndexNam (indexNam) '
	   .') '
	   .'COMMENT="'.$version.'" ';	   
	// end: joaktree_persons

	// Table joaktree_person_documents   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_person_documents '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', person_id        varchar(20)           NOT NULL '
	   .', document_id      varchar(20)           NOT NULL '
	   .', PRIMARY KEY  (app_id,person_id,document_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_person_documents
		   
	// Table joaktree_person_events
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_person_events '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', person_id        varchar(20)           NOT NULL '
	   .', orderNumber      smallint(2)           NOT NULL '
	   .', code             varchar(4)            NOT NULL '
	   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', type             varchar(30)  default      NULL '
	   .', eventDate        varchar(40)  default      NULL '
	   .', loc_id           int(11)      default      NULL '
	   .', location         varchar(75)  default      NULL '
	   .', value            varchar(100) default      NULL '
	   .', PRIMARY KEY  (app_id,person_id,orderNumber) '
	   .', KEY LOC1     (location) '
	   .', KEY LOI1     (loc_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_person_events
	   
	// Table joaktree_person_names
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_person_names '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', person_id        varchar(20)           NOT NULL '
	   .', orderNumber      smallint(2)           NOT NULL '
	   .', code             varchar(4)            NOT NULL '
	   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', eventDate        varchar(40)  default      NULL '
	   .', value            varchar(100) default      NULL '
	   .', PRIMARY KEY  (app_id,person_id,orderNumber) '
	   .') '
	   .'COMMENT="'.$version.'" ';   
	// end: joaktree_person_names
	   
	// Table joaktree_person_notes
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_person_notes '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', person_id        varchar(20)           NOT NULL '
	   .', orderNumber      smallint(2)           NOT NULL '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', nameOrderNumber  smallint(2)  default      NULL '
	   .', eventOrderNumber smallint(2)  default      NULL '
	   .', note_id          varchar(20)               NULL '
	   .', value            text '
	   .', PRIMARY KEY  (app_id,person_id,orderNumber) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_person_notes
	   
	// Table joaktree_registry_items
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_registry_items '
	   .'( id               int(11)      unsigned NOT NULL auto_increment '
	   .', regkey           varchar(255)          NOT NULL '
	   .', value            varchar(2048)         NOT NULL '
	   .', PRIMARY KEY  (id) '
	   .', UNIQUE KEY UK_KEY (regkey) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	   
	$update_queries[] = 'INSERT INTO #__joaktree_registry_items (regkey, value) VALUES ("LAST_UPDATE_DATETIME", NOW() )';
	$update_queries[] = 'INSERT INTO #__joaktree_registry_items (regkey, value) VALUES ("INITIAL_CHAR", "0" )';
	// end: joaktree_registry_items
	   
	// Table joaktree_relations
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_relations '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', person_id_1      varchar(20)           NOT NULL '
	   .', person_id_2      varchar(20)           NOT NULL '
	   .', type             enum( '   . $db->Quote( 'partner' )
	                          	.', ' . $db->Quote( 'father' )
				  			 	.', ' . $db->Quote( 'mother' )
				  				.')                 NOT NULL '
	   .', family_id        varchar(20)           NOT NULL '
	   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', orderNumber_1    smallint(2)  default      NULL '
	   .', orderNumber_2    smallint(2)  default      NULL '
	   .', PRIMARY KEY  (app_id,person_id_1,person_id_2) '
	   .', KEY person_id (app_id,person_id_1) '
	   .', KEY to_person_id (app_id,person_id_2) '
	   .') '
	   .'COMMENT="'.$version.'" ';

	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_relations '
	   .'  ADD  subtype     enum( '   . $db->Quote( 'spouse' )
	                          	.', ' . $db->Quote( 'partner' )
				  				.', ' . $db->Quote( 'natural' )
				  				.', ' . $db->Quote( 'adopted' )
				  				.', ' . $db->Quote( 'step' )
				  				.', ' . $db->Quote( 'foster' )
				  				.', ' . $db->Quote( 'legal' )
				  				.')                 NULL '
	   .'AFTER  type ';   
	// end: joaktree_relations

	// Table joaktree_relation_events
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_relation_events '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', person_id_1      varchar(20)           NOT NULL '
	   .', person_id_2      varchar(20)           NOT NULL '
	   .', orderNumber      smallint(2)           NOT NULL '
	   .', code             varchar(4)            NOT NULL '
	   .', indNote          tinyint(1)   unsigned NOT NULL default 0 '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', type             varchar(30)  default      NULL '
	   .', eventDate        varchar(40)  default      NULL '
	   .', loc_id           int(11)      default      NULL '
	   .', location         varchar(75)  default      NULL '
	   .', value            varchar(100) default      NULL '
	   .', PRIMARY KEY  (app_id,person_id_1,person_id_2,orderNumber) '
	   .', KEY LOC2     (location) '
	   .', KEY LOI2     (loc_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_relation_events
	   
	// Table joaktree_relation_notes
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_relation_notes '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', person_id_1      varchar(20)           NOT NULL '
	   .', person_id_2      varchar(20)           NOT NULL '
	   .', orderNumber      smallint(2)           NOT NULL '
	   .', indCitation      tinyint(1)   unsigned NOT NULL default 0 '
	   .', eventOrderNumber smallint(2)  default      NULL '
	   .', note_id          varchar(20)               NULL '
	   .', value            text '
	   .', PRIMARY KEY  (app_id,person_id_1,person_id_2,orderNumber) '
	   .') '
	   .'COMMENT="'.$version.'" ';   
	// end: joaktree_relation_notes
	   
	// Table joaktree_repositories
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_repositories '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', id               varchar(20)           NOT NULL '
	   .', name             varchar(50)           NOT NULL '
	   .', website          varchar(100) default  NULL '
	   .', PRIMARY KEY  (app_id,id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_repositories
	
	// Table joaktree_sources
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_sources '
	   .'( app_id           tinyint(4)            NOT NULL '
	   .', id               varchar(20)           NOT NULL '
	   .', repo_id          varchar(20)  default  NULL '
	   .', title            varchar(250) default  NULL '
	   .', author           varchar(250) default  NULL '
	   .', publication      varchar(250) default  NULL '
	   .', information      text         default  NULL '
	   .', PRIMARY KEY  (app_id,id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_sources

	// Table joaktree_themes
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_themes '
	   .'( id               smallint(6)  unsigned NOT NULL auto_increment '
	   .', name             varchar(25)  default  NULL '
	   .', home             tinyint(1)   unsigned NOT NULL default 0 '
	   .', params           varchar(2048)         NOT NULL '
	   .', PRIMARY KEY  (id) '
	   .', UNIQUE KEY UKNAME (name) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	   
	$params = '{"search_width":"9"' 
			 .',"show_update":"Y"'
			 .',"columns":"3"'
			 .',"groupCount":"3"'
			 .',"abbreviation":""'
			 .',"lineage":"3"'
			 .',"Directory":"images\\/joaktree"'
			 .',"pxHeight":"135"'
			 .',"pxWidth":"325"'
			 .',"transDelay":"50"'
			 .',"nextDelay":"5000"'
			 .',"TitleSlideshow":"A Genealogy Slideshow"'
			 .',"Sequence":"3"'
			 .',"pxMapWidth":"700"'
			 .',"statMarkerColor":""'
			 .',"descendantchart":"1"'
			 .',"descendantlevel":"20"'
			 .',"ancestorchart":"1"'
			 .',"ancestorlevel":"1"'
			 .',"ancestordates":"1"'
			 .',"indTabBehavior":"1"'
			 .',"notetitlelength":"30"'
			 .'}'; 
	$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Joaktree\', 1, \''.$params.'\')';
	$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Blue\', 0, \''.$params.'\')';
	$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Green\', 0, \''.$params.'\')';
	$update_queries[] = 'INSERT INTO #__joaktree_themes (name, home, params) VALUES (\'Red\', 0, \''.$params.'\')';
	// end: joaktree_themes

	// Table joaktree_trees
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_trees '
	   .'( id               int(10)      unsigned NOT NULL auto_increment '
	   .', app_id           tinyint(4)            NOT NULL '
	   .', holds            enum( \'all\' '
	   .'                       , \'descendants\' '
	   .'                       )                 NOT NULL default \'all\' '
	   .', language         varchar(7)            NOT NULL default \'*\' '
	   .', published        tinyint(1)            NOT NULL default 1 '
	   .', access           int(11)      unsigned NOT NULL default 1 '
	   .', name             varchar(250)          NOT NULL '
	   .', theme_id         int(11)               NOT NULL '
	   .', indGendex        tinyint(1)   unsigned NOT NULL default 0 '
	   .', root_person_id   varchar(20)               NULL '
	   .', introText        mediumtext                NULL '
	   .', PRIMARY KEY  (id) '
	   .') '
	   .'COMMENT="'.$version.'" ';

	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  ADD  asset_id            int(10)      unsigned NOT NULL '
	   .'AFTER  app_id ';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  MODIFY  root_person_id   varchar(20)               NULL';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  ADD  indMarriageCount    tinyint(1)   unsigned NOT NULL default 0 '
	   .'AFTER  indGendex ';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  ADD  indPersonCount      tinyint(1)   unsigned NOT NULL default 0 '
	   .'AFTER  indGendex ';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  ADD  robots              tinyint(2)            NOT NULL default 0 ' 
	   .'AFTER  root_person_id ';	   
	// end: joaktree_trees
	   
	// Table joaktree_tree_persons
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_tree_persons '
	   .'( id               varchar(31)           NOT NULL '
	   .', app_id           tinyint(4)            NOT NULL '
	   .', tree_id          int(11)               NOT NULL '
	   .', person_id        varchar(20)           NOT NULL '
	   .', type             enum( ' . $db->Quote( 'R' )
	                          	.', ' . $db->Quote( 'P' )
				  				.', ' . $db->Quote( 'C' )
				  				.')                 NOT NULL '
	   .', lineage          varchar(250) default  NULL '
	   .', PRIMARY KEY  (id) '
	   .', KEY person_id (app_id,person_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
	// end: joaktree_tree_persons
	   
} else if ($upgrade) {
	// Table joaktree_admin_persons
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_admin_persons '
	   .'  ADD  robots              tinyint(2)            NOT NULL default 0 ' 
	   .'AFTER  page ';	 
	     
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_admin_persons '
	   .'  ADD  map                 tinyint(1)            NOT NULL default 0 ' 
	   .'AFTER  robots ';	   
	// end: joaktree_admin_persons 
	   
	// Table joaktree_applications
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_applications '
	   .'  ADD  asset_id            int(10)      unsigned NOT NULL '
	   .'AFTER  id ';	   
	// end: joaktree_applications 
	
	// Table joaktree_citations
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_citations '
	   .'  ADD  dataQuality         tinyint(2)                NULL '
	   .'AFTER  orderNumber ';	
	// end: joaktree_citations
	
	// Table joaktree_gedcom_objectlines
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_gedcom_objectlines '
	   .'  ADD  subtype     enum( '   . $db->Quote( 'spouse' )
	                          	.', ' . $db->Quote( 'partner' )
				  				.', ' . $db->Quote( 'natural' )
				  				.', ' . $db->Quote( 'adopted' )
				  				.', ' . $db->Quote( 'step' )
				  				.', ' . $db->Quote( 'foster' )
				  				.', ' . $db->Quote( 'legal' )
				  				.')                   NULL '
	   .'AFTER  value ';   
	// end: joaktree_gedcom_objectlines
	
	// Table joaktree_locations   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_locations '
	   .'( id               int(11)      unsigned NOT NULL auto_increment '
	   .', indexLoc         varchar(1)            NOT NULL '
	   .', value            varchar(75)           NOT NULL '
	   .', latitude         decimal(10,7)             NULL '
	   .', longitude        decimal(10,7)             NULL '
	   .', indServerProcessed tinyint(1) unsigned NOT NULL default 0 '
	   .', indDeleted       tinyint(1)   unsigned NOT NULL default 0 '
	   .', results          tinyint(2)   unsigned     NULL '
	   .', resultValue      varchar(100)              NULL '
	   .', PRIMARY KEY  (id) '
	   .', KEY indexLoc (indexLoc) '
	   .') '
	   .'COMMENT="'.$version.'" ';	   
	// end: joaktree_locations
	   
	// Table joaktree_logremovals   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_logremovals '
	   .'( id               int(10)      unsigned NOT NULL AUTO_INCREMENT '
	   .', app_id           tinyint(4)   unsigned NOT NULL '
	   .', object_id        varchar(20)           NOT NULL '
	   .', object           enum( ' . $db->Quote( 'prsn' )
	                          .', ' . $db->Quote( 'sour' )
	                          .', ' . $db->Quote( 'repo' )
	                          .', ' . $db->Quote( 'docu' )
	                          .', ' . $db->Quote( 'note' )
	                          .')                 NOT NULL '
	   .', description      varchar(100)          NOT NULL '
	   .', PRIMARY KEY  (id) '
	   .', KEY objectIndex2 (app_id,object_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';	   
	// end: joaktree_logremovals
    
	// Table joaktree_logs   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_logs '
	   .'( id               int(10)      unsigned NOT NULL AUTO_INCREMENT '
	   .', app_id           tinyint(4)   unsigned NOT NULL '
	   .', object_id        varchar(20)           NOT NULL '
	   .', object           enum( ' . $db->Quote( 'prsn' )
	                          .', ' . $db->Quote( 'sour' )
	                          .', ' . $db->Quote( 'repo' )
	                          .', ' . $db->Quote( 'docu' )
	                          .', ' . $db->Quote( 'note' )
	                          .')                 NOT NULL '
	   .', changeDateTime   datetime              NOT NULL '
	   .', logevent         varchar(9)            NOT NULL '
	   .', user_id          int(11)               NOT NULL '
	   .', PRIMARY KEY  (id) '
	   .', KEY objectIndex1 (app_id,object_id) '
	   .') '
	   .'COMMENT="'.$version.'" ';
    // end: joaktree_logs
    
	// Table joaktree_maps   
	$update_queries[] = 
	    'CREATE TABLE IF NOT EXISTS '
	   .'#__joaktree_maps '
	   .'( id               int(11)      unsigned NOT NULL auto_increment '
	   .', name             varchar(50)           NOT NULL '
	   .', selection		enum( ' . $db->Quote( 'tree' )
	                          .', ' . $db->Quote( 'person' )
	   						  .', ' . $db->Quote( 'location' )
	                          .')                 NOT NULL '
	   .', service          varchar(20)           NOT NULL default '.$db->Quote( 'staticmap' ).' '
	   .', app_id           tinyint(4)   unsigned NOT NULL '
	   .', relations        tinyint(1)   unsigned NOT NULL default 0 '
	   .', params           varchar(2048)         NOT NULL '
	   .', tree_id          tinyint(4)   unsigned     NULL '
	   .', person_id        varchar(20)               NULL '
	   .', subject          varchar(50)               NULL '
	   .', period_start     int(11)      unsigned     NULL '
	   .', period_end       int(11)      unsigned     NULL '
	   .', excludePersonEvents   varchar(200)         NULL '
	   .', excludeRelationEvents varchar(200)         NULL '
	   .', PRIMARY KEY  (id) '
	   .') '
	   .'COMMENT="'.$version.'" ';	   
	// end: joaktree_maps
	   
	// Table joaktree_persons   
	$update_queries[] = 
	    'ALTER TABLE '
	    .'#__joaktree_persons '
	   .'  ADD  indIsWitness     tinyint(1)   unsigned NOT NULL default 0 ';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	    .'#__joaktree_persons '
	   .'  ADD  prefix           varchar(20)               NULL ';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	    .'#__joaktree_persons '
	   .'  ADD  suffix           varchar(20)               NULL ';

	   $update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_persons '
	   .'  ADD  indexNam              varchar(1)            NOT NULL '
	   .'AFTER  id ';
	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_persons '
 	   .'  ADD  KEY IndexNam     (indexNam) ';

	$update_queries[] = 
 	   'UPDATE #__joaktree_persons '
 	   .'SET   indexNam = SUBSTR(familyName, 1, 1) ';
	// end: joaktree_persons
	   
	// Table joaktree_person_events
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_person_events '
 	   .'  ADD  loc_id           int(11)      default      NULL '
	   .'AFTER  eventDate ';	   	   

	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_person_events '
 	   .'  ADD  KEY LOI1     (loc_id) ';	   	   
 	// end: joaktree_person_events
	   
	// Table joaktree_relations
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_relations '
	   .'  ADD  family_id           varchar(20)           NOT NULL '
	   .'AFTER  type ';	   	   

	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_relations '
	   .'  ADD  subtype     enum( '   . $db->Quote( 'spouse' )
	                          	.', ' . $db->Quote( 'partner' )
				  				.', ' . $db->Quote( 'natural' )
				  				.', ' . $db->Quote( 'adopted' )
				  				.', ' . $db->Quote( 'step' )
				  				.', ' . $db->Quote( 'foster' )
				  				.', ' . $db->Quote( 'legal' )
				  				.')                 NULL '
	   .'AFTER  type ';   
	// end: joaktree_relations
	
	// Table joaktree_relation_events
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_relation_events '
 	   .'  ADD  loc_id           int(11)      default      NULL '
	   .'AFTER  eventDate ';	   	   
 	   
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_relation_events '
 	   .'  ADD  KEY LOI2     (loc_id) ';	   	   
	// end: joaktree_relation_events
	   
	// Table joaktree_trees
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  ADD  asset_id            int(10)      unsigned NOT NULL '
	   .'AFTER  app_id ';
	      
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  ADD  robots              tinyint(2)            NOT NULL default 0 ' 
	   .'AFTER  root_person_id ';	   

	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_trees '
	   .'  ADD  language            varchar(7)            NOT NULL default \'*\' ' 
	   .'AFTER  holds ';	   
	// end: joaktree_trees
	
	// Table joaktree_tree_persons
	$update_queries[] = 
	    'ALTER TABLE '
	   .'#__joaktree_tree_persons '
	   .'MODIFY  lineage            varchar(250) default      NULL ';	   
	// end: joaktree_tree_persons
	
	// filling location	   
	$update_queries[] = 
		'INSERT INTO #__joaktree_locations (indexLoc , value) '
	   .'SELECT SUBSTRING(TRIM(location), 1, 1) '
	   .',      TRIM(location) '
	   .'FROM   #__joaktree_person_events '
	   .'WHERE  loc_id IS NULL '
	   .'AND    location IS NOT NULL '
	   .'UNION '
	   .'SELECT SUBSTRING(TRIM(location), 1, 1) '
	   .',      TRIM(location) '
	   .'FROM   #__joaktree_relation_events '
	   .'WHERE  loc_id IS NULL '
	   .'AND    location IS NOT NULL ';	 
	   
	$update_queries[] = 
		'UPDATE '
	   .'  #__joaktree_person_events jpe '
	   .', #__joaktree_locations     jln '
	   .'SET    jpe.loc_id         = jln.id '
	   .'WHERE  TRIM(jpe.location) = TRIM(jln.value) '
	   .'AND    jpe.loc_id         IS NULL ';
	   
	$update_queries[] = 
		'UPDATE '
	   .'  #__joaktree_relation_events jre '
	   .', #__joaktree_locations     jln '
	   .'SET    jre.loc_id         = jln.id '
	   .'WHERE  TRIM(jre.location) = TRIM(jln.value) '
	   .'AND    jre.loc_id         IS NULL ';
	// End: filling locations
} 


// Perform all queries - we don't care if it fails
foreach( $update_queries as $query ) {
    $db->setQuery( $query );
    $db->query();
}

// Fix for family_id
unset($query);
$query = $db->getQuery(true);

$query->select(' COUNT( jrn.app_id ) ');
$query->from(  ' #__joaktree_relations  jrn ');
//$query->where( ' jrn.app_id = '.(int) $this->procObject->id.' ');
$query->where( ' (  jrn.family_id IS NULL '
			 . ' OR jrn.family_id = '.$db->quote('').' '
       		 . ' ) '
       		 );

$db->setQuery($query);
$count = $db->loadResult();

if ((int) $count > 0) {
		// first update all empty family_id's for partner relationships
		$attribs = array();
		$attribs[] = 'person_id_1';
		$attribs[] = 'person_id_2';
		$key = $query->concatenate($attribs, '+');	
		unset($attribs);
		
		$query->clear();
		$query->update( ' #__joaktree_relations ' );
		$query->set(   ' family_id = '.$key.' ' );
		//$query->where( ' app_id = '.(int) $this->procObject->id.' ');
		$query->where( ' type = '.$db->quote('partner').' ');
		$query->where( ' (  family_id IS NULL '
					 . ' OR family_id = '.$db->quote('').' '
		       		 . ' ) '
		       		 );
		
		$db->setQuery($query);
		$db->query();
		$db->transactionCommit();
		
		// second update all children - for first parent
		$query->clear();
		$query->update( ' #__joaktree_relations child '
					  . ' INNER JOIN #__joaktree_relations parents '
					  . ' ON (   parents.app_id      = child.app_id '
					  . '    AND parents.type        = '.$db->quote('partner').' '
					  . '    AND parents.person_id_1 = child.person_id_2 '
					  . '    ) ' 
					  . ' INNER JOIN #__joaktree_relations p2 '
					  . ' ON (   p2.app_id      = child.app_id '
					  . '    AND p2.person_id_1 = child.person_id_1 '
					  . '    AND p2.type        IN ( '.$db->quote('father').', '.$db->quote('mother').') '
					  . '    AND p2.person_id_2 = parents.person_id_2 '
					  . '    ) ' 
					  );
		$query->set(   ' child.family_id = parents.family_id ' );
		//$query->where( ' child.app_id = '.(int) $this->procObject->id.' ');
		$query->where( ' child.type IN ( '.$db->quote('father').', '.$db->quote('mother').') ');
					  
		$db->setQuery($query);
		$db->query();
		$db->transactionCommit();
		
		// third update all children - for second parent
		$query->clear();
		$query->update( ' #__joaktree_relations child '
					  . ' INNER JOIN #__joaktree_relations parents '
					  . ' ON (   parents.app_id      = child.app_id '
					  . '    AND parents.type        = '.$db->quote('partner').' '
					  . '    AND parents.person_id_2 = child.person_id_2 '
					  . '    ) ' 
					  . ' INNER JOIN #__joaktree_relations p2 '
					  . ' ON (   p2.app_id      = child.app_id '
					  . '    AND p2.person_id_1 = child.person_id_1 '
					  . '    AND p2.type        IN ( '.$db->quote('father').', '.$db->quote('mother').') '
					  . '    AND p2.person_id_2 = parents.person_id_1 '
					  . '    ) ' 
					  );
		$query->set(   ' child.family_id = parents.family_id ' );
		//$query->where( ' child.app_id = '.(int) $this->procObject->id.' ');
		$query->where( ' child.type IN ( '.$db->quote('father').', '.$db->quote('mother').') ');
					 
		$db->setQuery($query);
		$db->query();
		$db->transactionCommit();
		
		// fourth update all children in a single parent family
		$attribs = array();
		$attribs[] = 'person_id_2';
		$attribs[] = $db->quote('+');
		$key = $query->concatenate($attribs);	
		unset($attribs);
		
		$query->clear();		
		$query->update( ' #__joaktree_relations ' );
		$query->set(   ' family_id = '.$key.' ' );
		//$query->where( ' app_id = '.(int) $this->procObject->id.' ');
		$query->where( ' type IN ( '.$db->quote('father').', '.$db->quote('mother').') ');
		$query->where( ' (  family_id IS NULL '
					 . ' OR family_id = '.$db->quote('').' '
		       		 . ' ) '
		       		 );
		
		$db->setQuery($query);
		$db->query();
		$db->transactionCommit();
}
// End: Fix for family_id


$application->enqueueMessage( JText::_( 'Database script is finished for version: ' ).$version, 'notice' ) ;
?>