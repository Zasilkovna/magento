<?php

class Zasilkovna_Checkout_Block_Adminhtml_Config_PriceRules extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $_itemRenderer;

	/**
	 * Fix for ignored "depends enabled"
	 * See: https://magento.stackexchange.com/questions/15500/configuration-depends-with-front-and-backend-model
	 */
	public function _toHtml()
	{
		return '<div class="zasilkovna_rules_configuration_price_rules">' . parent::_toHtml() . '</div>';
	}

	public function _prepareToRender()
	{

		$this->addColumn('weight_min', array(
			'label' => Mage::helper('zasilkovna')->__('Weight from'),
			'style' => 'width:100px',
			'class' => 'validate-zero-or-greater input-text',
		));
		$this->addColumn('weight_max', array(
			'label' => Mage::helper('zasilkovna')->__('Weight to (includes)'),
			'style' => 'width:100px',
			'class' => 'validate-zero-or-greater input-text',
		));
		$this->addColumn('price', array(
			'label' => Mage::helper('zasilkovna')->__('Price'),
			'class' => 'validate-zero-or-greater input-text',
		));

		$this->_addAfter = false;
		$this->_addButtonLabel = Mage::helper('zasilkovna')->__('Add rule');
	}

	protected function _getRenderer()
	{
		if (!$this->_itemRenderer) {
			$this->_itemRenderer = $this->getLayout()->createBlock(
				'Zasilkovna_Checkout_Block_Adminhtml_Config_Form_Field_Rule',
				'',
				array('is_render_to_js_template' => true)
			);
		}
		return $this->_itemRenderer;
	}

	protected function _prepareArrayRow(Varien_Object $row)
	{
		$row->setData(
			'option_extra_attr_' . $this->_getRenderer()
				->calcOptionHash($row->getData('price')),
			'selected="selected"'
		);
	}
}