<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ExportState
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $exported = $row->getData('exported');

        return empty($exported) ? Mage::helper('zasilkovna')->__('Ne') : Mage::helper('zasilkovna')->__('Ano');
    }
}