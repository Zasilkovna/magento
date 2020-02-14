<?php

class Zasilkovna_Checkout_Model_Misc_Payment
{
	public function toOptionArray()
	{
        // smysl maji pouze aktivni metody, ne vsechny
        $payments = Mage::getModel('payment/config')->getActiveMethods();
        $methods = [];

        foreach ($payments as $paymentCode=>$paymentModel) 
        {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(

                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }
        return $methods;
	}
}

?>