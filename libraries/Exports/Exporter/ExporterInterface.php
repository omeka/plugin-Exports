<?php
interface Exports_Exporter_ExporterInterface
{
    /**
     * Get the exporter registered name.
     */
    public function getName();

    /**
     * Get the exporter label.
     */
    public function getLabel();

    /**
     * Get the exporter description.
     */
    public function getDescription();

    /**
     * Add the form elements to configure the export.
     *
     * @param Zend_Form_SubForm $form
     */
    public function addElements(Zend_Form_SubForm $form);

    /**
     * Do the export, placing export assets in the export directory.
     *
     * The export job will make a directory and invoke this method. This method
     * should do the export and place all assets into that directory. The export
     * job will then ZIP up the export directory and delete any leftover server
     * artifacts.
     *
     * @param Job_ExportsExport $job
     */
    public function export(Job_ExportsExport $job);
}
