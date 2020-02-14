<?php

class Zasilkovna_Checkout_Block_Adminhtml_Config_Form_Field_Payment extends Mage_Core_Block_Html_Select
{
	/**
	 * Prepare HTML output
	 *
	 * @return Mage_Core_Block_Html_Select
	 */
	public function _toHtml()
	{
		$options = Mage::getSingleton('Zasilkovna_Checkout_Model_Misc_Payment')
			->toOptionArray();
		foreach ($options as $option) {
			$this->addOption($option['value'], $option['label']);
		}

		return parent::_toHtml();
	}

	/**
	 * Set field name
	 *
	 * @param string $value
	 */
	public function setInputName($value)
	{
		return $this->setName($value);
	}
}