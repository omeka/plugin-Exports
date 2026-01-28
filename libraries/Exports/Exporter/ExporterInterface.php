<?php
interface Exports_Exporter_ExporterInterface
{
    /**
     * Get the exporter label.
     */
    public function getLabel();

    /**
     * Get the exporter description.
     */
    public function getDescription();

    /**
     * Get the form HTML needed to configure the export.
     */
    public function getForm($view);
}
