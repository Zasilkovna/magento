<?php


class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Order
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{

		$orderNumber =  $row->getData('order_number');
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);

		$html ='<a href="' . $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId(), 'key' => $this->getCacheKey())) . '" target="_blank" title="' . $orderNumber . '" >' . $orderNumber . '</a>';
		
		return $html;
	}
}