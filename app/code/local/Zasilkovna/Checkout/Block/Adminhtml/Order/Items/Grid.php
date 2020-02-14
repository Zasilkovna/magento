<?php

class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();

		// Set a unique id for our grid
		$this->setId('order_items');

		// Default sort by column
		$this->setDefaultSort('item_id');

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
            'label'=> Mage::helper('zasilkovna')->__('Export'),
            'url'  => $this->getUrl('*/*/massExport', array('' => ''))        // public function massDeleteAction() in Mage_Adminhtml_Tax_RateController
        ));

        return $this;
    }

	// Set every column to be displayed on the grid
	protected function _prepareColumns(){
		$this->addColumn('order_number', array(
			'header' => Mage::helper('zasilkovna')->__('Číslo objednávky'),
			'sortable' => true,
			'width' => '60',
            'index' => 'order_number',
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Order'
		));

        $this->addColumn('recipient_name', array(
            'header' => Mage::helper('zasilkovna')->__('Jméno příjemce'),
            'sortable' => true,
            'width' => '60',
            'is_system' => true,
            'index' => array('recipient_lastname','recipient_firstname'),
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Name'
        ));


		$this->addColumn('recipient_company', array(
			'header' => Mage::helper('zasilkovna')->__('Společnost příjemce'),
			'sortable' => true,
			'width' => '60',
			'index' => 'recipient_company'
		));

		$this->addColumn('recipient_email', array(
			'header' => Mage::helper('zasilkovna')->__('Email příjemce'),
			'sortable' => true,
			'width' => '60',
			'index' => 'recipient_email'
		));

		$this->addColumn('recipient_phone', array(
			'header' => Mage::helper('zasilkovna')->__('Telefon příjemce'),
			'sortable' => true,
			'width' => '60',
			'index' => 'recipient_phone'
		));


        $this->addColumn('recipient_address', array(
            'header' => Mage::helper('zasilkovna')->__('Adresa příjemce'),
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_street',
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Address'
        ));

		$this->addColumn('cod', array(
			'header' => Mage::helper('zasilkovna')->__('Dobírka'),
			'sortable' => true,
			'width' => '60',
			'type' => 'options',
			'options'   => self::getOptionArray(),
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_CodState',
			'filter_condition_callback' => array($this, 'filterOptionCod')
		));

		$this->addColumn('currency', array(
			'header' => Mage::helper('zasilkovna')->__('Měna'),
			'sortable' => true,
			'width' => '60',
			'index' => 'currency'
		));

		$this->addColumn('value', array(
			'header' => Mage::helper('zasilkovna')->__('Celková cena'),
			'sortable' => true,
			'width' => '60',
			'index' => 'value',
		));

		$this->addColumn('branch_id', array(
			'header' => Mage::helper('zasilkovna')->__('ID odběr. místa'),
			'sortable' => true,
			'width' => '60',
			'index' => 'branch_id'
		));

        $this->addColumn('point_name', array(
            'header' => Mage::helper('zasilkovna')->__('Adresa odběr. místa'),
            'sortable' => true,
            'width' => '60',
            'index' => 'point_name'
        ));

        $this->addColumn('exported', array(
			'header' => Mage::helper('zasilkovna')->__('Exportováno'),
            'sortable' => true,
            'width' => '60',
			'index' => 'exported',
			'type' => 'options',
			'options'   => self::getOptionArray(),
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ExportState',
			'filter_condition_callback' => array($this, 'filterOptionExport')
        ));

        $this->addColumn('exported_at', array(
            'header' => Mage::helper('zasilkovna')->__('Čas exportu'),
            'sortable' => true,
            'width' => '60',
            'is_system' => true,
            'index' => 'exported_at',
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ExportTime'
        ));

		$this->addExportType('*/*/exportZasilkovnaCsv', Mage::helper('zasilkovna')->__('CSV - Pouze neexportované'));

		$this->addExportType('*/*/exportZasilkovnaCsvAll', Mage::helper('zasilkovna')->__('CSV - Všechny záznamy'));


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
		return array(Mage::helper('zasilkovna')->__('Ne'), Mage::helper('zasilkovna')->__('Ano'));
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
}

?>
