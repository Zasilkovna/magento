<?php

class Zasilkovna_Checkout_Model_Porder extends Mage_Core_Model_Abstract{

	public function _construct(){
		parent::_construct();
		$this->_init('zasilkovna/porder');
	}
}

?>