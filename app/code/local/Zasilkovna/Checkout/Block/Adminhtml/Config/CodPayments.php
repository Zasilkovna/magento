<?php

class Zasilkovna_Checkout_Block_Adminhtml_Config_CodPayments extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer;

    /**
     * Fix for ignored "depends enabled"
     * See: https://magento.stackexchange.com/questions/15500/configuration-depends-with-front-and-backend-model
     */
    public function _toHtml()
    {
        return '<div class="zasilkovna_rules_configuration_cod_payments">' . parent::_toHtml() . '</div>';
    }

    public function _prepareToRender()
    {

        $this->addColumn('payment_method', array(
            'label' => Mage::helper('zasilkovna')->__('payment method'),
            'style' => 'width:100px',
            'renderer' => $this->_getRenderer(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('zasilkovna')->__('Add method');
    }

    protected function _getRenderer()
    {
        if (!$this->_itemRenderer) {
			$this->_itemRenderer = $this->getLayout()->createBlock(
				'Zasilkovna_Checkout_Block_Adminhtml_Config_Form_Field_Payment',
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