<?php

class Zasilkovna_Checkout_Model_Observer{

	const CONFIGURATION_KEY = 'configuration_';
	const SHIPPING_CODE = 'zasilkovna_zasilkovna';
	const ORDER_ADDRESS_REGEX ='/^(.*[^0-9]+) (([1-9][0-9]*)\/)?([1-9][0-9]*[a-cA-C]?)$/';

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
                    $message = Mage::helper('zasilkovna')->__('Některé ceny nejsou vyplněné');
                    throw new Exception($message);                
                }
                continue;
            }
            $params = $group['fields']['price_rules']['value'];
            $groupNameParts = explode(self::CONFIGURATION_KEY, $key);
            $groupName = end($groupNameParts);            
            // jen pro kody zemi
            $groupName = !empty($groupName) && strlen($groupName) == 2 ? strtoupper($groupName) : null; 
            $weightMax = 0; 
		
            foreach ($params as $key => $line)
            {
                if($key == "__empty")
                {
                    continue;
                }
							
				if ($line['weight_max'] < $weightMax ||  $line['weight_min'] < $weightMax || $line['weight_min'] > $line['weight_max'])
                {
                    $message = Mage::helper('zasilkovna')->__('Váhové intervaly se nesmí překrývat') . $groupName ? " [$groupName]" : "";
                    throw new Exception($message);
                }
                if (empty($line['price']))
                {
                    $message = Mage::helper('zasilkovna')->__('Některé ceny nejsou vyplněné.') . $groupName ? " [$groupName]" : "";
                    throw new Exception($message);
                }
                $weightMax = $line['weight_max'];
            }
        }
	}

	/**
	 * Runs after order process - adds data to packetery DB table
	 * TODO: chybi mi validace jako v JS
	 */

	public function updatePacketeryData($observer){
		
		$orderEvent = $observer->getEvent()->getOrder();
		$orderId = $orderEvent->getIncrementId();

		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId); 
		$shippingMethod = $order->getShippingMethod();
		
		// RUN ONLY IF ZASILKOVNA CARRIER IS SELECTED
		if($shippingMethod != self::SHIPPING_CODE)
		{
			return;
		}
		
		$requestParams = Mage::app()->getRequest()->getParams();
		$packetaId = $requestParams['packetaId'];
		$packetaName = $requestParams['packetaName'];
		$data = $this->prepareData($order, $packetaId, $packetaName);

		$this->saveData($data);
	}

	/**
	 * Vypocita celkovou hmotnost objednavky
	 */
	private function getOrderTotalWeight($order)
	{
		$items = $order->getAllItems();
		$weightTotal = 0;

		foreach ($items as $item)
		{
			$weightTotal += $item->getWeight();
		}
		return $weightTotal;
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
	 * @param $order
	 */
	private function prepareData($order, $packetaId, $packetaName)
	{
		$paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
		$shippingAddress = $order->getShippingAddress();

		list($street, $houseNumber) = $this->parseOrderAddres($shippingAddress);

		return array(
			'order_number' => $order->getIncrementId(), 
			'recipient_firstname' => $shippingAddress->getFirstname(),
			'recipient_lastname' => $shippingAddress->getLastname(),
			'recipient_phone' => $shippingAddress->getTelephone(),
			'recipient_email' => $shippingAddress->getEmail(),
			'weight' => $this->getOrderTotalWeight($order),
			'currency' => $order->getOrderCurrencyCode(),
			'value' => $order->getGrandTotal(),
			'branch_id' => $packetaId,
			'point_name' => $packetaName,
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