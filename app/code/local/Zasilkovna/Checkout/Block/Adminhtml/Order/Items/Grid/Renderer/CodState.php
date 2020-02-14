<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_CodState
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $cod = $row->getData('cod');

        return ($cod == $cod > 0 ? Mage::helper('zasilkovna')->__('Ano') : Mage::helper('zasilkovna')->__('Ne'));
    }
}