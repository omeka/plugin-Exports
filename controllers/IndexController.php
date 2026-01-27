<?php
class Exports_IndexController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ExportsExport');
    }

    public function setExporterAction()
    {
        $exporterManager = new Exports_Exporter_Manager;

        if ($this->getRequest()->isPost()) {
            $this->_helper->flashMessenger(__('Configure your export below.'), 'success');
            $this->_helper->redirector('export', null, null, ['exporter' => $_POST['exporter']]);
        }

        $this->view->assign(['exporterManager' => $exporterManager]);
    }

    public function exportAction()
    {
        $exporterName = $this->_getParam('exporter');
        $exporterManager = new Exports_Exporter_Manager;
        $exporter = $exporterManager->get($exporterName);

        return parent::addAction();
    }
}
