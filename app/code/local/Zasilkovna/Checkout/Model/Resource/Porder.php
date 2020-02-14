<?php

class Zasilkovna_Checkout_Model_Resource_Porder extends Mage_Core_Model_Resource_Db_Abstract{
	public function _construct(){
		$this->_init('zasilkovna/porder', 'id');
	}
}

?>