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
     */
    public function addElements($form);
}
