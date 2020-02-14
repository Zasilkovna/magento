<?php
class Zasilkovna_Checkout_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    const MODUL_NAME = 'zasilkovna';
    const MODUL_TITLE = 'Zásilkovna';
    const MODUL_IDENTITY = 'magento-1.9-packeta-4.1';
    const MODUL_CONF = 'zasilkovna_rules/configuration%s/';
    const MODUL_CONF_GLOBAL = 'zasilkovna_rules/configuration_global/';

	protected $_code = self::MODUL_NAME;
	protected $_weightTotal = 0;
	protected $_priceSubtotal = 0;

	protected $_countryCode;
	protected $_weightRules;
	protected $_configPath;
	protected $_globalConfigPath;

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		// skip if not enabled
		if(!Mage::getStoreConfig('carriers/'.$this->_code.'/active'))
			return false;

        $this->initProps($request);
            
        // this object will be returned as result of this method
		// containing all the shipping rates of this method
        $result = Mage::getModel('shipping/rate_result');
        $_weightMax = Mage::getStoreConfig( $this->_globalConfigPath."max_weight" );
        $_freeShipping = $this->getFreeShipping();

        // Package is over maximum allowed weight
        if ( !empty( $_weightMax ) )
            if($this->_weightTotal > $_weightMax)
                return;

        // Free Shipping is enabled && price is over free shipping threshold
        if($_freeShipping !== false && $_freeShipping <= $this->_priceSubtotal){
            $result->append($this->_getFree());
        // Weight rules are empty
        }else if( !$this->_weightRules || count($this->_weightRules) < 1 ){
            $result->append($this->_getDefault());
        }else{
            $result->append($this->_getWeighted());
        }

        return $result;
    }
    
    protected function _getDefault(){

        // save price and cost
        $defaultPrice = Mage::getStoreConfig($this->_configPath.'default_price');
        $globalDefaultPrice = Mage::getStoreConfig($this->_globalConfigPath.'default_price');
        if (!$defaultPrice)$defaultPrice=$globalDefaultPrice;
        if (!$defaultPrice)$defaultPrice=0;

        return $this->initMethodPrice($defaultPrice);
    }

    protected function _getFree(){
        
        return $this->initMethodPrice(0);
    }

	protected function _getWeighted(){

		$price = PHP_INT_MIN; 
        
		foreach ($this->_weightRules as $rule){

            if( $this->_weightTotal >= $rule['weight_min'] && $this->_weightTotal <= $rule['weight_max'] )
            {
                $price = $rule['price'];
			}
        }
        
        if($price >= 0)
        {
            return $this->initMethodPrice($price);
        }
        else
        {
            return $this->_getDefault();
        }
	}


	/**
	 * Get allowed shipping methods
	 *
	 * @return array
	 */

	public function getAllowedMethods()
	{
		return array($this->_code => "Standard");
    }

    /**
     * Nastaveni parametru dopravy
     */
    private function initMethodPrice($price)
    {
        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier($this->_code);

        $carrierTitle = $method->getCarrierTitle();

        if(empty($carrierTitle))
        {
            $method->setCarrierTitle(self::MODUL_TITLE);
        }
        
        // save method information
        $method->setMethod($this->_code);
        
        $methodTitle = $method->getMethodTitle();
        
        if(empty($methodTitle))
        {
            $method->setMethodTitle(self::MODUL_TITLE);
        }

        $method->setCost($price);
        $method->setPrice($price);

        return $method;
    }

    /**
     * Inicializace zakladnich promennych
     * @param Mage_Shipping_Model_Rate_Request $request
     */
    private function initProps(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_countryCode = $request->getDestCountryId();
        $this->_weightTotal = $request->getPackageWeight();
        $this->_priceSubtotal = (int) $request->getPackageValue();

        $configSufix = strtolower($this->_countryCode);
        $this->_configPath = sprintf(self::MODUL_CONF, "_$configSufix");
        $this->_globalConfigPath = self::MODUL_CONF_GLOBAL;

        if(!Mage::getStoreConfig($this->getConfigPriceRules()))
        {
            $this->_configPath = sprintf(self::MODUL_CONF, "");
        }
		$this->_weightRules = unserialize( Mage::getStoreConfig( $this->getConfigPriceRules() ) );
    }

    /**
     * Cesta k cenam konfigu
     */
    private function getConfigPriceRules()
    {
        return $this->_configPath."price_rules";
    }

    /**
     * Nastaveni pro dopravu zdarma
     * @return int | bool
     */
    private function getFreeShipping()
    {        
        /** set $_freeShipping to either int (value) or false (free shipping disabled) **/
        $_freeShipping = false;
        $_countryFreeShipping = Mage::getStoreConfig( $this->_configPath."free_shipping" );
        $_globalFreeShipping = Mage::getStoreConfig( $this->_globalConfigPath."free_shipping" );        

        // Use country specific free shipping
        if ( !empty ( $_countryFreeShipping ) ) $_freeShipping = $_countryFreeShipping;
        // Use global free shipping
        elseif( !empty ( $_globalFreeShipping ) ) $_freeShipping = $_globalFreeShipping;
        // Free shipping is disabled
        else $_freeShipping = false;

        return $_freeShipping;
    }

    /**
     * Priprava JS skriptu pro vyvolani widggetu
     * TODO: bylo by lepsi to navazt do sablony
     */
    public static function getPacketaJS($countryId, $shipStreet, $shipCity)
    {
        $packetaApiKey = Mage::getStoreConfig('zasilkovna_options/configuration/api_key');
        $packetaLanguage = Mage::app()->getLocale()->getLocaleCode();
        $languageParts = explode('_', $packetaLanguage);

        $options = [
            'webUrl' 		=> Mage::getBaseUrl(),
            'appIdentity' 	=> self::MODUL_IDENTITY,
            'country' 		=> strtolower($countryId),
            'language' 		=> reset($languageParts),
            'street' 		=> $shipStreet,
            'city' 			=> $shipCity,
        ];
    
        $packteraJS = "Packeta.Widget.pick('$packetaApiKey', showSelectedPickupPoint," . json_encode($options) . ")";	

        // potrebuji tam apostrof
        return str_replace("\"", "'", $packteraJS);
    }
}
