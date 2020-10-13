DROP TABLE IF EXISTS `#__f2c_search_form`;
DROP TABLE IF EXISTS `#__f2c_search_formfield`;
DROP TABLE IF EXISTS `#__f2c_search_dataview`;
DROP TABLE IF EXISTS `#__f2c_search_dataviewfield`;
DROP TABLE IF EXISTS `#__f2c_search_fieldtype`;

CREATE TABLE `#__f2c_search_form` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `asset_id` int(10) unsigned NOT NULL default '0',    
  `projectid` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `mod_pre_text` varchar(255) NOT NULL default '',
  `totals_pre_text` varchar(255) NOT NULL default '',
  `totals_post_text` varchar(255) NOT NULL default '',
  `submit_pre_text` varchar(45) NOT NULL default '',
  `submit_post_text` varchar(45) NOT NULL default '',
  `submit_caption` varchar(45) NOT NULL default '',
  `submit_class` varchar(45) NOT NULL default '',
  `num_cols` int(10) unsigned NOT NULL default '0',
  `attribs` varchar(5120) NOT NULL default '',  
  PRIMARY KEY  (`id`)
) CHARACTER SET `utf8`;

CREATE TABLE `#__f2c_search_formfield` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '', 
  `fieldid` int(10) NOT NULL default '0',
  `caption` varchar(100) NOT NULL default '',
  `helptext` varchar(255) NOT NULL default '',
  `ordering` int(10) unsigned NOT NULL default '0',
  `formid` int(10) unsigned NOT NULL default '0',
  `attribs` varchar(100) NOT NULL default '',
  `emptytext` varchar(100) NOT NULL default '',
  `displaymode` int(10) unsigned NOT NULL default '0',
  `settings` mediumtext,
  `fieldtypeid` int(10) NOT NULL default '0',  
  PRIMARY KEY  (`id`)
) CHARACTER SET `utf8`;

CREATE TABLE `#__f2c_search_dataview` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_form_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `metatags` text,
  `asset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET `utf8`;

CREATE TABLE `#__f2c_search_dataviewfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_form_fieldid` int(11) NOT NULL,
  `value` mediumtext,
  `dataview_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET `utf8`;

CREATE TABLE  `#__f2c_search_fieldtype` (
  `id` int(10) NOT NULL auto_increment,
  `description` varchar(45) NOT NULL default '',
  `name` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`id`)
) CHARACTER SET `utf8`;

INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-5, 'Text Combo', 'Textcombo');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-4, 'Integer Slider', 'Intslider');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-3, 'Free Text', 'Freetext');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-2, 'Joomla Category', 'Category');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (-1, 'Joomla Author', 'Author');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (4, 'Checkbox', 'Checkbox');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (5, 'Single Select List', 'Singleselectlist');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (10, 'Multi Select List', 'Multiselectlist');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (15, 'Database Lookup', 'Databaselookup');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (17, 'Database Lookup Multi', 'Databaselookupmulti');
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (18, 'Date', 'Date');	
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (19, 'Date Interval (custom)', 'Dateinterval');	
INSERT INTO `#__f2c_search_fieldtype` (`id`, `description`, `name`) VALUES (20, 'Integer Interval (custom)', 'Intinterval');	