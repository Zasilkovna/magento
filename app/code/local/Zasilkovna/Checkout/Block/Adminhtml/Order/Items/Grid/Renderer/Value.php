<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Value
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $row->getData('value');
    }
}