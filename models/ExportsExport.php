<?php
class ExportsExport extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $owner_id;
    public $exporter_name;
    public $name;
    public $label;
    public $status;
    public $data;
    public $added;
    public $modified;

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
    }

    public function getResourceId()
    {
        return 'Exports_Exports';
    }

    public function getRecordUrl($action = 'show')
    {
        return [
            'module' => 'exports',
            'controller' => 'index',
            'action' => $action,
            'id' => $this->id,
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getExporter()
    {
        $exporterManager = Zend_Registry::get('exports_exporter_manager');
        return $exporterManager->get($this->getExporterName());
    }

    public function setExporterName($exporterName)
    {
        $this->exporter_name = $exporterName;
    }

    public function getExporterName()
    {
        return $this->exporter_name;
    }

    public function setName($exporterName)
    {
        $this->name = sprintf(
            '%s_%s_%s',
            $exporterName,
            time(),
            substr(md5(rand()), 0, 4)
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getData()
    {
        return json_decode($this->data, true);
    }

    public function setData($data)
    {
        $this->data = json_encode($data);
    }

    public function getUri()
    {
        $storage = Zend_Registry::get('storage');
        return $storage->getUri(sprintf('exports/%s.zip', $this->getName()));
    }

    protected function afterDelete()
    {
        if (!in_array($this->getStatus(), [Process::STATUS_COMPLETED, Process::STATUS_ERROR])) {
            return;
        }
        // Dispatch the export delete job.
        $dispatcher = Zend_Registry::get('job_dispatcher');
        $dispatcher->sendLongRunning(
            'Job_ExportsDelete',
            ['export_name' => $this->getName()]
        );
    }
}
