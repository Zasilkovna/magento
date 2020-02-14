<?php


class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Name
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $firstname =  $row->getData('recipient_firstname');
        $lastname =  $row->getData('recipient_lastname');

        // hnusny
        $value = $lastname . ' ' . $firstname;

        $html = $value;
        return $html;
    }
}