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

    public function browseAction()
    {
        $this->view->assign([]);

        return parent::browseAction();
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

        $this->view->assign([
            'exporter' => $exporter,
            'form' => $form,
            'csrf' => $csrf,
        ]);

        if ($this->getRequest()->isPost()) {

            if (!$csrf->isValid($_POST)) {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
                return;
            }

            // Populate the export record.
            $export = new ExportsExport;
            $export->exporter_name = $_POST['exporter_name'];
            $export->label = $_POST['label'];
            $export->name = sprintf(
                '%s_%s_%s',
                $_POST['exporter_name'],
                time(),
                substr(md5(rand()), 0, 4)
            );
            $export->data = json_encode($_POST['data']);

            if ($export->save(false)) {

                // Dispatch the export job.
                $dispatcher = Zend_Registry::get('job_dispatcher');
                $dispatcher->sendLongRunning(
                    'Job_ExportsExport',
                    ['export_id' => $export->id]
                );

                $this->_helper->flashMessenger(__('Exporting "%s".', $export->label), 'success');
                $this->_helper->redirector('browse');

            } else {
                $export->delete();
                $this->_helper->flashMessenger($e);
            }
        }
    }
}
