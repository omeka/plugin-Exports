<?php
class Exports_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_autoCsrfProtection = true;

    public function init()
    {
        $this->_helper->db->setDefaultModelName('ExportsExport');
    }

    protected function _getBrowseDefaultSort()
    {
        return array('added', 'd');
    }

    protected function _getDeleteConfirmMessage($export)
    {
        return __(sprintf('This will delete the export "%s".', $export->getLabel()));
    }

    protected function _getDeleteSuccessMessage($export)
    {
        return __(sprintf('The export "%s" was successfully deleted.', $export->getLabel()));
    }

    public function setExporterAction()
    {
        $exporterManager = Zend_Registry::get('exports_exporter_manager');

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
        $exporterManager = Zend_Registry::get('exports_exporter_manager');
        $exporter = $exporterManager->get($this->_getParam('exporter'));
        $form = new Exports_Form_Export($exporter);
        $csrf = new Omeka_Form_SessionCsrf;

        // Re-export if the "id" parameter is present.
        $id = $this->_getParam('id');
        if ($id) {
            $export = $this->_helper->db->findById();
            $form->getSubForm('data')->setDefaults($export->getData());
        }

        $this->view->assign([
            'exporter' => $exporter,
            'form' => $form,
            'csrf' => $csrf,
        ]);

        if ($this->getRequest()->isPost()) {

            if (!($form->isValid($_POST) && $csrf->isValid($_POST))) {
                $form->getElement('exporter')->setValue($exporter->getLabel());
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
                return;
            }

            // Populate the export record.
            $export = new ExportsExport;
            $export->setExporterName($_POST['exporter_name']);
            $export->setName($_POST['exporter_name']);
            $export->setLabel($_POST['label']);
            $export->setData($_POST['data']);

            if ($export->save(false)) {

                // Dispatch the export job.
                $dispatcher = Zend_Registry::get('job_dispatcher');
                $dispatcher->sendLongRunning(
                    'Job_ExportsExport',
                    ['export_id' => $export->id]
                );

                $this->_helper->flashMessenger(__('Exporting "%s".', $export->getLabel()), 'success');
                $this->_helper->redirector->goToRoute(['action' => 'show', 'id' => $export->getId()]);

            } else {
                $export->delete();
                $this->_helper->flashMessenger($e);
            }
        }
    }
}
