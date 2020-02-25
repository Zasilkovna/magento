<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();

		// Set a unique id for our grid
		$this->setId('order_items');

		// Default sort by column
		$this->setDefaultSort('order_number');

		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);

		parent::_construct();
	}


	protected function _prepareCollection()
	{

		// Instantiate the collection of data to be display on the grid
		$this->setCollection(Mage::getModel('zasilkovna/porder')->getCollection());

        //$this->getColumn('order_number')->setUseIndex(true);

		parent::_prepareCollection();
		return $this;
	}

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('order_id');

        $this->getMassactionBlock()->addItem('export', array(
            'label'=> Mage::helper('zasilkovna')->__('CSV Export'),
            'url'  => $this->getUrl('*/*/massExport', array('' => ''))        // public function massDeleteAction() in Mage_Adminhtml_Tax_RateController
        ));

        return $this;
    }

	// Set every column to be displayed on the grid
	protected function _prepareColumns(){
		$this->addColumn('order_number', array(
			'header' => Mage::helper('zasilkovna')->__('Order number'),
			'sortable' => true,
			'width' => '60',
            'index' => 'order_number',
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Order'
		));

		$this->addColumn('order_status', array(
			'header' => Mage::helper('zasilkovna')->__('Order status'),
			'sortable' => true,
			'width' => '60',
			'type' => 'options',
			'options'   => self::getOrderStatues(),
			'filter_condition_callback' => array($this, 'filterOrderStatus'),
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_OrderStatus'
		));

        $this->addColumn('recipient_lastname', array(
            'header' => Mage::helper('zasilkovna')->__('Full name'),
            'sortable' => true,
            'width' => '60',
            'index' => array('recipient_firstname','recipient_lastname'),
			'filter_condition_callback' => array($this, 'filterRecipientName'),
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Name'
        ));


		$this->addColumn('recipient_company', array(
			'header' => Mage::helper('zasilkovna')->__('Recipient company'),
			'sortable' => true,
			'width' => '60',
			'index' => 'recipient_company'
		));

		$this->addColumn('recipient_email', array(
			'header' => Mage::helper('zasilkovna')->__('Recipient e-mail'),
			'sortable' => true,
			'width' => '60',
			'index' => 'recipient_email'
		));

		$this->addColumn('recipient_phone', array(
			'header' => Mage::helper('zasilkovna')->__('Recipient phone number'),
			'sortable' => true,
			'width' => '60',
			'index' => 'recipient_phone'
		));


        $this->addColumn('recipient_address', array(
            'header' => Mage::helper('zasilkovna')->__('Recipient address'),
            'sortable' => true,
            'width' => '60',
			'type' => 'text',
            'index' => 'recipient_street',
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Address',
			'filter_condition_callback' => array($this, 'filterRecipientStreet')
        ));

		$this->addColumn('cod', array(
			'header' => Mage::helper('zasilkovna')->__('COD'),
			'sortable' => true,
			'width' => '60',
			'type' => 'options',
			'options'   => self::getOptionArray(),
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_CodState',
			'filter_condition_callback' => array($this, 'filterOptionCod')
		));

		$this->addColumn('currency', array(
			'header' => Mage::helper('zasilkovna')->__('Currency'),
			'sortable' => true,
			'width' => '60',
			'index' => 'currency'
		));

		$this->addColumn('value', array(
			'header' => Mage::helper('zasilkovna')->__('Total price'),
			'sortable' => true,
			'width' => '60',
			'index' => 'value',
		));

        $this->addColumn('point_name', array(
            'header' => Mage::helper('zasilkovna')->__('Pickup point address'),
            'sortable' => true,
            'width' => '60',
			'index' => 'point_name',
			'filter_condition_callback' => array($this, 'filterPointName'),
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_AddressPickupPoint'
        ));

        $this->addColumn('exported', array(
			'header' => Mage::helper('zasilkovna')->__('Exported'),
            'sortable' => true,
            'width' => '60',
			'index' => 'exported',
			'type' => 'options',
			'options'   => self::getOptionArray(),
			'filter_condition_callback' => array($this, 'filterOptionExport'),
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ExportState'
        ));

        $this->addColumn('exported_at', array(
            'header' => Mage::helper('zasilkovna')->__('Export date'),
            'sortable' => true,
            'width' => '60',
            'is_system' => true,
            'index' => 'exported_at',
			'filter' => false,
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ExportTime'
        ));

		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}

	/**
	 * Moznosti volby Ano/Ne
	 */
	public static function getOptionArray()
	{
		return array(Mage::helper('zasilkovna')->__('No'), Mage::helper('zasilkovna')->__('Yes'));
	}

	public static function getOrderStatues() {

		return Mage::getResourceModel('sales/order_status_collection')->load()->toOptionHash();
	}

	/**
	 * Vlastni filtrace pro stav export
	 * @param $collection
	 * @param $column
	 */
	public function filterOptionExport($collection, $column)
	{
		$filterValue = intval($column->getFilter()->getValue());
		 
		if($filterValue === 0)
		{
			$collection->getSelect()->where("exported = '' OR exported IS NULL");
		}
		 
		if($filterValue === 1)
		{
			$collection->getSelect()->where("exported = 1");
		}
	}

	/**
	 * Vlastni filtrace pro to jestli ma objednavka dobirku 
	 * (ve smyslu pravidel dobirky zasilkovny)
	 * @param $collection
	 * @param $column
	 */
	public function filterOptionCod($collection, $column)
	{
		$filterValue = intval($column->getFilter()->getValue());
		 
		if($filterValue === 0)
		{
			$collection->getSelect()->where("cod = '0.00'");
		}
		 
		if($filterValue === 1)
		{
			$collection->getSelect()->where("exported > 0");
		}
	}

	/**
	 * @param $collection
	 * @param $column
	 */
	public function filterRecipientStreet($collection, $column) {

		$filterValue = $this->filterValue($column, true);
		if(!$filterValue) {
			return;
		}

		$collection->getSelect()->where('CONCAT_WS("",recipient_street, recipient_house_number, recipient_city, recipient_zip) LIKE ? ', "%{$filterValue}%");
	}

	/**
	 * @param $collection
	 * @param $column
	 */
	public function filterRecipientName($collection, $column) {

		$filterValue = $this->filterValue($column, true);

		if(!$filterValue) {
			return;
		}

		$collection->getSelect()->where('CONCAT(recipient_firstname, recipient_lastname) LIKE ? ', "%{$filterValue}%");
	}

	public function filterPointName($collection, $column) {

		$filterValue = $this->filterValue($column, true);

		if(!$filterValue) {
			return;
		}

		$collection->getSelect()->where('CONCAT(point_name, branch_id) LIKE ? ', "%{$filterValue}%");
	}

	/**
	 * @param $collection
	 * @param $column
	 */
	public function filterOrderStatus($collection, $column) {

		$filterValue = $column->getFilter()->getValue();

		if(!$filterValue) {
			return;
		}

		$collection->getSelect()->join(array('s' => 'sales_flat_order'), 'order_number = s.increment_id')->where('s.status = ?', $filterValue);
	}

	/**
	 * @param $column
	 * @param bool $removeSpaces
	 *
	 * @return string|string[]|null
	 */
	private function filterValue($column, $removeSpaces = false) {

		$filterValue = $column->getFilter()->getValue();

		return ($removeSpaces ? preg_replace('/\s+/', '', $filterValue) : $filterValue);
	}
}

?>
