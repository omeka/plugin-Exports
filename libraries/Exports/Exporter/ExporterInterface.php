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
}
