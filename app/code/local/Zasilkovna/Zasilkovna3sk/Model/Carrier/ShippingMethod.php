<?php
class Zasilkovna_Zasilkovna3sk_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'zasilkovna3sk';

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		// skip if not enabled
		if(!Mage::getStoreConfig('carriers/'.$this->_code.'/active'))
			return false;

		// this object will be returned as result of this method
		// containing all the shipping rates of this method
		$result = Mage::getModel('shipping/rate_result');

		// create new instance of method rate
		$method = Mage::getModel('shipping/rate_result_method');

		// save carrier information
		$method->setCarrier($this->_code);
		$method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));

		// save method information
		$method->setMethod($this->_code);
		$method->setMethodTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/methodtitle'));

		// save price and cost
		$method->setCost(Mage::getStoreConfig('carriers/'.$this->_code.'/price'));
		$method->setPrice(Mage::getStoreConfig('carriers/'.$this->_code.'/price'));

		// add this rate to the result
		$result->append($method);
		
		return $result; 
	 }
 }
