<?php
class Exports_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_autoCsrfProtection = true;

    public function init()
    {
        $this->_helper->db->setDefaultModelName('ExportsExport');
    }

    public function browseAction()
    {
        $exporterManager = new Exports_Exporter_Manager;

        $this->view->assign([
            'exporterManager' => $exporterManager,
        ]);

        return parent::browseAction();
    }

    public function setExporterAction()
    {
        $exporterManager = new Exports_Exporter_Manager;

        if ($this->getRequest()->isPost()) {
            $this->_helper->flashMessenger(__('Configure your export below.'), 'success');
            $this->_helper->redirector('export', null, null, ['exporter' => $_POST['exporter']]);
        }

        $this->view->assign([
            'exporterManager' => $exporterManager,
        ]);
    }

    public function exportAction()
    {
        $exporterManager = new Exports_Exporter_Manager;
        $exporter = $exporterManager->get($this->_getParam('exporter'));
        $form = new Exports_Form_Export($exporter);

        $this->view->assign([
            'exporter' => $exporter,
            'form' => $form,
        ]);

        return parent::addAction();
    }
}
