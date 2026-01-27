<?php
class Exports_Exporter_Resources implements Exports_Exporter_ExporterInterface
{
    public function getLabel()
    {
        return __('Resources');
    }

    public function getDescription()
    {
        return __('Export a file containing data about selected resources (CSV or JSON-LD).');
    }
}
