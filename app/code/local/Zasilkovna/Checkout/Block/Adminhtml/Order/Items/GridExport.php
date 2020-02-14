<?php


class Zasilkovna_Checkout_Block_Adminhtml_Order_Items_GridExport extends Mage_Adminhtml_Block_Widget_Grid
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
        parent::_prepareCollection();
        return $this;
    }


    // Set every column to be displayed on the grid
    protected function _prepareColumns()
    {
        $this->addColumn('order_number', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'order_number'
        ));

        $this->addColumn('recipient_firstname', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_firstname'
        ));

        $this->addColumn('recipient_lastname', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_lastname'
        ));

        $this->addColumn('recipient_company', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_company'
        ));

        $this->addColumn('recipient_email', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_email'
        ));

        $this->addColumn('recipient_phone', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_phone'
        ));

        $this->addColumn('cod', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'cod',
			'type' => 'options',
			'options'   => self::getOptionArray(),
			'filter_condition_callback' => array($this, 'filterOptionCod'),
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_CodValue'
        ));

        $this->addColumn('currency', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'currency'
        ));

        $this->addColumn('value', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'value',
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_Value'
        ));

        // vytvoreni prazdneho sloupce
        $this->addColumn('weight', array(
            'sortable' => true,
            'width' => '60',
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ValueDummy'
        ));

        $this->addColumn('branch_id', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'branch_id'
        ));

        // vytvoreni prazdneho sloupce
        $this->addColumn('store_label', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'store_label',
        ));

        // vytvoreni prazdneho sloupce
        $this->addColumn('adult', array(
            'sortable' => true,
            'width' => '60',
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ValueDummy'
        ));

        // vytvoreni prazdneho sloupce
        $this->addColumn('delayed', array(
            'sortable' => true,
            'width' => '60',
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ValueDummy'
        ));

        $this->addColumn('recipient_street', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_street'
        ));

        $this->addColumn('recipient_house_number', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_house_number'
        ));

        $this->addColumn('recipient_city', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_city'
        ));

        $this->addColumn('recipient_zip', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'recipient_zip'
        ));

        // vytvoreni prazdneho sloupce
        $this->addColumn('carrier_point', array(
            'sortable' => true,
            'width' => '60',
            'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ValueDummy'
        ));

        $this->addColumn('width', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'width'
        ));
        $this->addColumn('height', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'height'
        ));
        $this->addColumn('depth', array(
            'sortable' => true,
            'width' => '60',
            'index' => 'depth'
        ));

        $this->addColumn('exported', array(
			'header' => Mage::helper('zasilkovna')->__('Exportováno'),
            'sortable' => true,
            'width' => '60',
			'index' => 'exported',
			'type' => 'options',
			'options'   => self::getOptionArray(),
			'renderer' => 'Zasilkovna_Checkout_Block_Adminhtml_Order_Items_Grid_Renderer_ValueDummy',
			'filter_condition_callback' => array($this, 'filterOptionExport')
        ));

		$this->addExportType('*/*/exportZasilkovnaCsv', Mage::helper('zasilkovna')->__('CSV - Pouze neexportované'));

		$this->addExportType('*/*/exportZasilkovnaCsvAll', Mage::helper('zasilkovna')->__('CSV - Všechny záznamy'));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _exportCsvItemCheckExported(Varien_Object $item, Varien_Io_File $adapter)
    {
        if($item->exported == "1")return;

        $row = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($item);
            }
        }

        $adapter->streamWriteCsv(
            Mage::helper("core")->getEscapedCSVData(array_merge([''],$row))
        );
    }

    protected function _exportCsvItemMass(Varien_Object $item, Varien_Io_File $adapter, array $selected)
    {

        if(!in_array($item->id, $selected))return;

        $row = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($item);
            }
        }

        $adapter->streamWriteCsv(
            Mage::helper("core")->getEscapedCSVData(array_merge([''],$row))
        );
    }

    protected function _exportCsvItem(Varien_Object $item, Varien_Io_File $adapter)
    {

        $row = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($item);
            }
        }

        $adapter->streamWriteCsv(
            Mage::helper("core")->getEscapedCSVData(array_merge([''],$row))
        );
    }

    public function getCsvFile(){

        $this->initExport();

        $path = self::getFilePath();
        $file = $this->prepareFilePath($path);
        $io = $this->prepareIoStream($file, $path);

        $this->_exportIterateCollection('_exportCsvItemCheckExported', array($io));
        $this->flushIoStream($io);

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => false // can delete file after use
        );
    }

    public function getCsvAllFile(){

        $this->initExport();

        $path = self::getFilePath();
        $file = $this->prepareFilePath($path);
        $io = $this->prepareIoStream($file, $path);

        $this->_exportIterateCollection('_exportCsvItem', array($io));
        $this->flushIoStream($io);

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => false // can delete file after use
        );
    }

    public function getCsvMassFile($orderNumbers){

        $this->initExport();

        $path = self::getFilePath();
        $file = $this->prepareFilePath($path);
        $io = $this->prepareIoStream($file, $path);

        $this->_exportIterateCollection('_exportCsvItemMass', array($io, $orderNumbers));
        $this->flushIoStream($io);

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => false // can delete file after use
        );
    }

    /**
     * Cesta k export. souboru
     */
    private static function getFilePath()
    {
        return Mage::getBaseDir('var') . DS . 'export' . DS; //best would be to add exported path through config
    }

    /**
     * Nastaveni zakladnich nalezitosti
     */
    private function initExport()
    {
        $this->setCollection(Mage::getModel('zasilkovna/porder')->getCollection());

        $this->_isExport = true;
        $this->_prepareGrid();
    }

    /**
     * Sestavi jmeno pro export. soubor
     */
    private function prepareFilePath($path)
    { 
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        /**
         * It is possible that you have name collision (summer/winter time +1/-1)
         * Try to create unique name for exported .csv file
         */
        while (file_exists($file)) {
            sleep(1);
            $name = md5(microtime());
            $file = $path . DS . $name . '.csv';
        }
        return $file;
    }

    /**
     * Vytvori stream pro zapis exportu
     * @param array $file
     * @param string $file
     */
    private function prepareIoStream($file, $path)
    {
        $io = new Varien_Io_File();

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv([Mage::helper('zasilkovna')->__('Verze 5')]);
        $io->streamWriteCsv([""]);

        return $io;
    }

    /**
     * Zapise a zavre stream po exportu
     * @param array $io
     */
    private function flushIoStream(&$io)
    {
        if ($this->getCountTotals()) {
            $io->streamWriteCsv($this->_getExportTotals());
        }

        $io->streamUnlock();
        $io->streamClose();
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
