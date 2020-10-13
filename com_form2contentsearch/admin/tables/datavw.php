<?php
class Form2ContentSearchTableDatavw extends JTable
{
	/**
	 * Constructor
	 *
	 * @param database Database object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__f2c_search_dataview', 'id', $db);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	3.0.0
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_form2contentsearch.datavw.'.(int)$this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	3.0.0
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	3.0.0
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
        $asset->loadByName('com_form2contentsearch');
        return $asset->id;		
	}
	 
	/**
	 * Validation
	 *
	 * @return boolean True if buffer valid
	 */
	function check()
	{
		if(trim($this->title) == '')
		{
			$this->setError(JText::_('COM_FORM2CONTENTSEARCH_ERROR_FORM_TITLE_EMPTY'));
			return false;
		}
						
		return true;
	}	
}
?>