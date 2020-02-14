<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_AddressPickupPoint
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {

        $branchName = $row->getData('point_name');
        $branchId = $row->getData('branch_id');

		return sprintf("%s (%s)", $branchName, $branchId);
    }
}