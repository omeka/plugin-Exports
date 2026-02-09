<?php
class Exports_Exporter_Unknown implements Exports_Exporter_ExporterInterface
{
    protected $exporterName;

    public function __construct($exporterName)
    {
        $this->exporterName = $exporterName;
    }

    public function getName()
    {
        return $this->exporterName;
    }

    public function getLabel()
    {
        return sprintf(__('Unknown [%s]'), $this->exporterName);
    }

    public function getDescription()
    {
        return '';
    }

    public function addElements(Zend_Form_SubForm $form)
    {
    }

    public function export(Job_ExportsExport $job)
    {
    }
}
