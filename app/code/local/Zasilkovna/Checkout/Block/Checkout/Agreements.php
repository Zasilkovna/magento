<?php

class Zasilkovna_Checkout_Block_Checkout_Agreements extends Mage_Checkout_Block_Agreements
{
    protected function _toHtml()
    {
        $this->setTemplate('zasilkovna/checkout/agreements.phtml');
        return parent::_toHtml();
    }
}