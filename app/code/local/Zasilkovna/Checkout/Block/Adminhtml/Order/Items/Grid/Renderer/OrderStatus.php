<?php


class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_OrderStatus
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{

		$orderNumber =  $row->getData('order_number');
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);

		return $order->getStatus();
	}
}