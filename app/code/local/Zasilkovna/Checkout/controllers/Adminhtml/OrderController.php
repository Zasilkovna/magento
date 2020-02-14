<?php

class Zasilkovna_Checkout_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
	const EXPORT_FILE_NAME = 'zasilkovna_';

	public function indexAction()
	{

//        Load the Adminhtml layout
		$this->loadLayout();

//        This block will point to the file Block/Adminhtml/Order/Items.php
		$block = $this->getLayout()->createBlock('backend/adminhtml_order_items');
		$this->_addContent($block);

//        Render the layout
		$this->renderLayout();
	}

	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('backend/adminhtml_order_items')->toHtml()
		);
	}

	public function exportZasilkovnaCsvAction()
	{
		$grid = $this->getLayout()->createBlock('backend/adminhtml_order_items_gridExport');

		$file = $grid->getCsvFile();
		$fileName = $this->getExportFileName();

		if($file !== FALSE)
		{
			// toto bych sjednotil do metody
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            $queryExportedAt = "UPDATE packetery_order SET exported_at=(now()) WHERE exported IS NULL OR exported <> 1";
            $connection->query($queryExportedAt);

            $queryExported = "UPDATE packetery_order SET exported=1";
            $connection->query($queryExported);

			$this->_prepareDownloadResponse($fileName, $file);
		}
		else
		{
			Mage::getSingleton('core/session')->addError('Chyba! Nenalezena žádná data pro export.');
			$this->_redirectUrl($this->_getRefererUrl());
		}
	}

	public function exportZasilkovnaCsvAllAction()
	{
		$grid = $this->getLayout()->createBlock('backend/adminhtml_order_items_gridExport');

		$file = $grid->getCsvAllFile();
		$fileName = $this->getExportFileName();

		if($file !== FALSE)
		{
			// toto bych sjednotil do metody
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            $queryExportedAt = "UPDATE packetery_order SET exported_at=(now())";
            $connection->query($queryExportedAt);

            $queryExported = "UPDATE packetery_order SET exported=1";
            $connection->query($queryExported);

			$this->_prepareDownloadResponse($fileName, $file);
		}
		else
		{
			Mage::getSingleton('core/session')->addError('Chyba! Nenalezena žádná data pro export.');
			$this->_redirectUrl($this->_getRefererUrl());
		}

	}

    public function massExportAction()
    {
        $orderIds = $this->getRequest()->getParam('order_id');

        if(is_array($orderIds)) 
        {
            $grid = $this->getLayout()->createBlock('backend/adminhtml_order_items_gridExport');

			$file = $grid->getCsvMassFile($orderIds);
			$fileName = $this->getExportFileName();

            if($file !== FALSE)
            {
				// toto bych sjednotil do metody
                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                $queryExportedAt = "UPDATE packetery_order SET exported_at=(now()) WHERE id IN(".implode(',', $orderIds ).")";
                $connection->query($queryExportedAt);

                $queryExported = "UPDATE packetery_order SET exported=1 WHERE id IN(".implode(',', $orderIds ).")";
                $connection->query($queryExported);

                $this->_prepareDownloadResponse($fileName, $file);
            }
            else
            {
                Mage::getSingleton('core/session')->addError('Chyba! Nenalezena žádná data pro export.');
                $this->_redirectUrl($this->_getRefererUrl());
            }

        }
		// tohle je zde proc?
        $this->_redirect('*/*/index');
	}
	
	/**
	 * Prebere se soucasny nazev ktery vygeneruje grid a rozsiri se prefix zasilkovna
	 * @param array $file
	 */
	private function getExportFileName($extension = '.csv')
	{
		return self::EXPORT_FILE_NAME . md5(microtime()). $extension;
	}
}

?>