<?php

class Zasilkovna_Checkout_Model_Observer{

	const CONFIGURATION_KEY = 'configuration_';
	const SHIPPING_CODE = 'zasilkovna_zasilkovna';
	const ORDER_ADDRESS_REGEX ='/^(.*[^0-9]+) (([1-9][0-9]*)\/)?([1-9][0-9]*[a-cA-C]?)$/';

	const CZ_FICTIVE_BRANCH = array(
		'id' => 540,
		'name' => 'AA Andělská Hora',
	);
	const SK_FICTIVE_BRANCH = array(
		'id' => 703,
		'name' => 'Abovce',
	);
	const HU_FICTIVE_BRANCH = array(
		'id' => 2485,
		'name' => 'AA Angyalföld',
	);

	/**
	 * Runs before config save - validates input values
	 */

	public function updateRules($observer){

		// Run validation based on section
		// If validation fails throws generic exception

		$config = $observer->getObject();

		if ($config->getSection() == "zasilkovna_rules")
			$this->validateRules();

		return true;
	}

	/**
	* Neni uplne idealni, mit tu validaci takto, ale ponechavam zatim toto reseni.
	**/
	private function validateRules(){
		
		$groups = Mage::app()->getRequest()->getPost('groups'); 
		
		foreach ($groups as $key => $group)
		{
            if(empty($group['fields']['price_rules']['value']))
            {
                if(empty($group['fields']['default_price']['value']))
                {
                    $message = Mage::helper('zasilkovna')->__('Some prices are not filled');
                    throw new Exception($message);                
                }
                continue;
            }
            $params = $group['fields']['price_rules']['value'];
            $groupNameParts = explode(self::CONFIGURATION_KEY, $key);
            $groupName = end($groupNameParts);            
            // jen pro kody zemi
            $groupName = (!empty($groupName) && strlen($groupName) == 2 ? sprintf("[%s]: ", strtoupper($groupName)) : "");
            $weightMax = 0; 
		
            foreach ($params as $key => $line)
            {
                if($key == "__empty")
                {
                    continue;
                }

				if ($line['weight_max'] < $weightMax ||  $line['weight_min'] < $weightMax || ($line['weight_min'] > 0 && $line['weight_min'] < $weightMax) || $line['weight_min'] > $line['weight_max'] )
                {
					$message = $groupName . Mage::helper('zasilkovna')->__('The weight intervals must not overlap');
					Mage::throwException($message);

                } elseif($line['weight_min'] === $line['weight_max']) {
					$message = $groupName . Mage::helper('zasilkovna')->__('The weight intervals must not be the same.');
					Mage::throwException($message);
				}
                if (empty($line['price']))
                {
                    $message = $groupName . Mage::helper('zasilkovna')->__('Some prices are not filled');
					Mage::throwException($message);
                }
                $weightMax = $line['weight_max'];
            }
        }
	}

	/**
	 * Runs after order process - adds data to packetery DB table
	 * TODO: chybi mi validace jako v JS
	 */

	/**
	 * @param \Varien_Event_Observer $observer
	 */
	public function updatePacketeryData(Varien_Event_Observer $observer){

		$orderEvent = $observer->getEvent()->getOrder();

		/** @var \Mage_Sales_Model_Order $orderEvent */
		$orderId = $orderEvent->getIncrementId();
		$parentOrderNumber = $orderEvent->getRelationParentRealId();

		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId); 
		$shippingMethod = $order->getShippingMethod();
		
		// RUN ONLY IF ZASILKOVNA CARRIER IS SELECTED
		if($shippingMethod != self::SHIPPING_CODE)
		{
			return;
		}

		// Editace objednávky - objednávka má svého předka
		if($parentOrderNumber) {
			$packeteryOrder = Mage::getModel('zasilkovna/porder')->load($parentOrderNumber, 'order_number');
			$branchId = $packeteryOrder->getData('branch_id');
			$pointName = $packeteryOrder->getData('point_name');

		}
		else {
			$requestParams = Mage::app()->getRequest()->getParams();
			$branchId = $requestParams['packetaId'];
			$pointName = $requestParams['packetaName'];
		}

		$data = $this->prepareData($order, $branchId, $pointName);
		$this->saveData($data);
	}

	/**
	 * Podle nejakych pravidel to zpracuje adresu objednavky
	 * @param $shippingAddress
	 */
	private function parseOrderAddres($shippingAddress)
	{
		$streetMatches = array();
		$match = preg_match(self::ORDER_ADDRESS_REGEX, $shippingAddress->getStreet()[0], $streetMatches);

		if(!$match){
			$houseNumber = NULL;
			$street = $shippingAddress->getStreet()[0];
		}else if( !isset($streetMatches[4]) ){
			$houseNumber = NULL;
			$street = $streetMatches[1];
		}else{
			$houseNumber = (!empty($streetMatches[3])) ? $streetMatches[3]."/".$streetMatches[4] : $streetMatches[4];
            $street = $streetMatches[1];
		}

		return array($street, $houseNumber);
	}

	/**
	 * Overeni podle nastaveni modulu, jestli je pro zasilkovnu 
	 * dobirka nebo ne.
	 */
	private function isCod($methodCode)
	{
		$codPayments = explode(',' , Mage::getStoreConfig('zasilkovna_cod/configuration/cod') );
		return in_array($methodCode, $codPayments);
	}

	/**
	 * Sestaveni unikaniho label/id obchodu
	 */
	private function getLabel()
	{
        $store = Mage::app()->getStore();
        
        if($store)
        {
            return $store ->getFrontendName();
        }
        return null;
	}

	/**
	 * Pripravi na data pro ulozeni objednavky
	 *
	 * @param $order
	 * @param $packetaId
	 * @param $packetaName
	 *
	 * @return array
	 */
	private function prepareData($order, $packetaId, $packetaName)
	{
		$fictiveBranches = array(
			'cz' => self::CZ_FICTIVE_BRANCH,
			'sk' => self::SK_FICTIVE_BRANCH,
			'hu' => self::HU_FICTIVE_BRANCH,
		);

		$country = strtolower($order->getShippingAddress()->getCountryId());
		$paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
		$shippingAddress = $order->getShippingAddress();

		list($street, $houseNumber) = $this->parseOrderAddres($shippingAddress);

		return array(
			'order_number' => $order->getIncrementId(), 
			'recipient_firstname' => $shippingAddress->getFirstname(),
			'recipient_lastname' => $shippingAddress->getLastname(),
			'recipient_phone' => $shippingAddress->getTelephone(),
			'recipient_email' => $shippingAddress->getEmail(),
			'weight' => $order->getWeight(),
			'currency' => $order->getOrderCurrencyCode(),
			'value' => $order->getGrandTotal(),
			'branch_id' => ($packetaId ? $packetaId : (isset($fictiveBranches[$country]) ? $fictiveBranches[$country]['id'] : self::CZ_FICTIVE_BRANCH['id'])),
			'point_name' => ($packetaId ? $packetaName : (isset($fictiveBranches[$country]) ? $fictiveBranches[$country]['name'] : self::CZ_FICTIVE_BRANCH['name'])),
			'cod' => ($this->isCod($paymentMethod) ? $order->getShippingAmount() : 0),
			'recipient_company' => $shippingAddress->getCompany(),
			'recipient_street' => $street,
			'recipient_house_number' => $houseNumber,
			'recipient_city' => $shippingAddress->getCity(),
			'recipient_zip' => $shippingAddress->getPostcode(),
			'store_label' => $this->getLabel()
		);
	}

	/**
	 * Ulozi data obbjednavky do modulu zasilkovny
	 * @package array $data
	 */
	private function saveData($data)
	{
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$query = "INSERT INTO packetery_order 
					(`order_number`, `recipient_firstname`, `recipient_lastname`, `recipient_phone`, `recipient_company`, `recipient_email`, `cod` ,`currency`,`value`, `weight`,`branch_id`,`point_name`,`recipient_street`,`recipient_house_number`,`recipient_city`,`recipient_zip`, `store_label`) 
					VALUES (:order_number, :recipient_firstname, :recipient_lastname, :recipient_phone, :recipient_company,:recipient_email, :cod, :currency, :value, :weight, :branch_id, :point_name, :recipient_street, :recipient_house_number, :recipient_city, :recipient_zip, :store_label)";

		$connection->query($query, $data);		
	}
}