<?php
class Form2ContentSearchTableFormField extends JTable
{
	/**
	 * Constructor
	 *
	 * @param database Database object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__f2c_search_formfield', 'id', $db);
	}
	 	
    public function store($updateNulls = false)
    {
    	if(empty($this->id))
    	{
    		// new Content Type Field => Get ordering
    		$this->ordering = $this->getNextOrder('formid = ' . (int)$this->formid);
    	}
    	
    	return parent::store($updateNulls);
    }
    
    public function bind($array, $ignore = '') 
    {
       if (isset($array['settings']) && is_array($array['settings'])) 
       {
                // Convert the params field to a string.
                $parameter = new JRegistry;
                $parameter->loadArray($array['settings']);
                $array['settings'] = (string)$parameter;
       }
        
       return parent::bind($array, $ignore);
    }
}
?>