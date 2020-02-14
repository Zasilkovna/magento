<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Address
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        // toto je hnusny
        $street = $row->getData('recipient_street');
        $city = $row->getData('recipient_city');
        $houseNumber = $row->getData('recipient_house_number');
        $zip = $row->getData('recipient_zip');

        $value = <<< HTML
<p>$street $houseNumber</p>
<p>$city, $zip </p>
HTML;

        return $value;
    }
}