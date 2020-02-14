
<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ExportTime
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    // Here we create a link to point the Order View page for the current value
    public function render(Varien_Object $row)
    {
        $exportedAt = $row->getData('exported_at');

        return Mage::helper('core')->formatDate($exportedAt, 'medium', true);
    }
}