<?php
class Exports_Exporter_Manager
{
    protected $exporters;

    public function __construct()
    {
        $exporters = apply_filters('exports_exporters', []);
        foreach ($exporters as $name => $exporter) {
            // Validate the exporters.
            if (!($exporter instanceof Exports_Exporter_ExporterInterface)) {
                throw new Exception(sprintf('The "%s" exporter must implement Exports_Exporter_ExporterInterface', $name));
            }
        }
        $this->exporters = $exporters;
    }

    public function get($exporterName)
    {
        if (!isset($this->exporters[$exporterName])) {
            return new Exports_Exporter_Unknown($exporterName);
        }
        return $this->exporters[$exporterName];
    }

    public function getExporters()
    {
        return $this->exporters;
    }
}
