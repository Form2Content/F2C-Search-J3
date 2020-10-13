<?php
defined('_JEXEC') or die('Restricted acccess');

/**
 * Script file of Form2Content Search component
 */
class com_Form2ContentSearchInstallerScript
{
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) 
    {
    	// Create an index on the F2C FieldContent table
		$db 			= JFactory::getDBO();
		$app			= JFactory::getApplication();
		
		if(!self::tableHasIndex('#__f2c_fieldcontent', 'idx_f2c_search'))
		{
			$db->setQuery('CREATE INDEX idx_f2c_search ON #__f2c_fieldcontent (formid, fieldid);');
			$db->query();
			
			if($db->getErrorMsg())
			{
				$app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
				return false;
			}		
		}
    }
 
        /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) 
    {
    	// Drop the index on the F2C FieldContent table
		$db 			= JFactory::getDBO();
		$app			= JFactory::getApplication();
		
		if(self::tableHasIndex('#__f2c_fieldcontent', 'idx_f2c_search'))
		{
			$db->setQuery('DROP INDEX idx_f2c_search ON #__f2c_fieldcontent;');
			$db->query();
			
			if($db->getErrorMsg())
			{
				$app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
				return false;
			}		
		}
    }
 
  	/**
     * method to update the component
     *
     * @return void
     */
	function update($parent) 
    {
    	$db = JFactory::getDBO();
        	
		// add missing columns (release 4.1.0)
		$db->setQuery('SHOW COLUMNS FROM #__f2c_search_formfield LIKE \'settings\'');
		
		if(!$db->loadResult())
		{
			$db->setQuery('ALTER TABLE #__f2c_search_formfield MODIFY COLUMN `fieldid` int(10) NOT NULL default 0');
			$db->query();
			$db->setQuery('ALTER TABLE #__f2c_search_formfield ADD COLUMN `settings` mediumtext');
			$db->query();
		}
		
		// add new tables (release 4.2.0)		
		$db->setQuery('show tables like \''. $db->replacePrefix('#__f2c_search_dataview') . '\'');
		
		if(!$db->loadResult())
		{
			$db->setQuery('CREATE TABLE `#__f2c_search_dataview` ( 
							`id` int(11) NOT NULL AUTO_INCREMENT,
  							`search_form_id` int(11) NOT NULL,
  							`title` varchar(255) NOT NULL,
  							`asset_id` int(11) NOT NULL,
  							PRIMARY KEY (`id`));');
			$db->query();
			
			$db->setQuery('CREATE TABLE `#__f2c_search_dataviewfield` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
  							`search_form_fieldid` int(11) NOT NULL,
  							`value` mediumtext,
  							`dataview_id` int(11) NOT NULL,
  							PRIMARY KEY (`id`));');
			$db->query();
		}
		
		// set fieldtypeid (release 4.6.0)
		$db->setQuery('SHOW COLUMNS FROM #__f2c_search_formfield LIKE \'fieldtypeid\'');
		
		if(!$db->loadResult())
		{
			$db->setQuery('ALTER TABLE #__f2c_search_formfield ADD COLUMN `fieldtypeid` int(11) NOT NULL');
			$db->query();
			
			$db->setQuery('UPDATE #__f2c_search_formfield ff LEFT JOIN #__f2c_projectfields pf on ff.fieldid = pf.id SET ff.fieldtypeid = IFNULL(pf.fieldtypeid, ff.fieldid)');
			$db->query();
		}	

		// add new tables (release 6.3.0)		
		$db->setQuery('show tables like \''. $db->replacePrefix('#__f2c_search_fieldtype') . '\'');
		
		if(!$db->loadResult())
		{
			$db->setQuery('CREATE TABLE  `#__f2c_search_fieldtype` (
							  `id` int(10) NOT NULL auto_increment,
							  `description` varchar(45) NOT NULL default \'\',
							  `name` varchar(45) NOT NULL default \'\',
							  PRIMARY KEY  (`id`)
							);');
			$db->query();
			
			// Insert fieldtypes
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-5, \'Text Combo\', \'Textcombo\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-4, \'Integer Slider\', \'Intslider\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-3, \'Free Text\', \'Freetext\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-2, \'Joomla Category\', \'Category\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-1, \'Joomla Author\', \'Author\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (4, \'Checkbox\', \'Checkbox\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (5, \'Single Select List\', \'Singleselectlist\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (10, \'Multi Select List\', \'Multiselectlist\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (15, \'Database Lookup\', \'Databaselookup\');');
			$db->query();
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (17, \'Database Lookup Multi\', \'Databaselookupmulti\');');		
			$db->query();			
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (18, \'Date\', \'Date\');');		
			$db->query();	
			$db->setQuery('INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (19, \'Date Interval (custom)\', \'Dateinterval\');');		
			$db->query();	
			
			$db->setQuery('ALTER TABLE #__f2c_search_formfield ADD COLUMN `title` varchar(255) NOT NULL default \'\' AFTER `id`');
			$db->query();
			$db->setQuery('UPDATE #__f2c_search_formfield ff LEFT JOIN #__f2c_projectfields pf ON ff.fieldid = pf.id SET ff.title = pf.fieldname');
			$db->query();
			$db->setQuery('UPDATE #__f2c_search_formfield ff INNER JOIN #__f2c_search_fieldtype ft ON ff.fieldtypeid = ft.id SET ff.title = ft.description WHERE ff.title = \'\'');
			$db->query();
		}

		// add new field (release 6.4.0)
		$db->setQuery('INSERT INTO #__f2c_search_fieldtype (`name`, `description`) SELECT \'Intinterval\',\'Integer Interval (custom)\' FROM #__f2c_search_fieldtype WHERE name = \'Intinterval\' HAVING COUNT(*) = 0');
		$db->execute();
		
		// add new columns to #__f2c_search_dataview (release 6.5.0)
		$db->setQuery('SHOW COLUMNS FROM #__f2c_search_dataview LIKE \'description\'');
		
		if(!$db->loadResult())
		{
			$db->setQuery('ALTER TABLE #__f2c_search_dataview ADD COLUMN `description` text AFTER `title`');
			$db->query();

			$db->setQuery('ALTER TABLE #__f2c_search_dataview ADD COLUMN `metatags` text AFTER `description`');
			$db->query();
		}	
	}
 
     /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
        function preflight($type, $parent) 
        {
        }
 
        /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
    }
    
    private function tableHasIndex($tableName, $indexName)
    {
		$db = JFactory::getDBO();
		
		$db->setQuery('SHOW INDEX FROM #__f2c_fieldcontent');
		$lstIndexes = $db->loadObjectList();
		
		if(count($lstIndexes))
		{
			foreach($lstIndexes as $index)
			{
				if($index->Key_name == 'idx_f2c_search')
				{
					return true;
				}
			}
		}

		return false;
    }
}
?>