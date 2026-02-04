<?php
class Exports_Exporter_Resources implements Exports_Exporter_ExporterInterface
{
    public function getName()
    {
        return 'resources';
    }

    public function getLabel()
    {
        return __('Resources');
    }

    public function getDescription()
    {
        return __('Export a file containing data about selected resources.');
    }

    public function addElements(Zend_Form_SubForm $form)
    {
        // Get the available resource types.
        $apiResources = Omeka_Controller_Plugin_Api::getApiResources();
        $resourceTypes = [];
        foreach ($apiResources as $apiResourceName => $apiResource) {
            if (!isset($apiResource['record_type'])) {
                // Only resources that have a record type can be exported.
                continue;
            }
            $resourceTypes[$apiResourceName] = $apiResourceName;
        }
        asort($resourceTypes);
        $resourceTypes = ['' => __('Select Below')] + $resourceTypes;

        // Add the form elements.
        $form->addElement('select', 'resource', [
            'label' => __('Resource Type'),
            'description' => __('Select the type of resource to export.'),
            'multiOptions' => $resourceTypes,
            'required' => true,
        ]);
        $form->addElement('text', 'query', [
            'label' => __('Search Query'),
            'description' => __('Enter the query used to filter the resources to be exported. If no query is entered, all available resources will be exported.'),
        ]);
        $form->addElement('select', 'format', [
            'label' => __('Format'),
            'description' => __('Select the format of the export file.'),
            'multiOptions' => [
                '' => __('Select Below'),
                'csv' => 'CSV',
                'json' => 'JSON',
            ],
            'required' => true,
        ]);
    }

    public function export(Job_ExportsExport $job)
    {
        $export = $job->getExport();
        $exportData = $export->getData();

        $exportResource = $exportData['resource'] ?? null;
        $exportFormat = $exportData['format'] ?? null;
        parse_str($exportData['query'] ?? '', $exportQuery);

        if (!isset($exportResource)) {
            throw new Exception(sprintf('Exports plugin: Missing "resource" option for export "%s" using exporter "Resources".', $export->getLabel()));
        }
        if (!isset($exportFormat)) {
            throw new Exception(sprintf('Exports plugin: Missing "format" option for export "%s" using exporter "Resources".', $export->getLabel()));
        }

        $apiResources = Omeka_Controller_Plugin_Api::getApiResources();

        if (!isset($apiResources[$exportResource])) {
            throw new Exception(sprintf('Exports plugin: Unknown API resource "%s" for export "%s" using exporter "Resources".', $exportResource, $export->getLabel()));
        }

        $apiResource = $apiResources[$exportResource];

        if (!isset($apiResource['record_type'])) {
            throw new Exception(sprintf('Exports plugin: Unsupported API resource "%s" for export "%s" using exporter "Resources".', $exportResource, $export->getLabel()));
        }

        $recordAdapterClass = sprintf('Api_%s', $apiResource['record_type']);
        $recordAdapter = new $recordAdapterClass;
        $recordsTable = get_db()->getTable($apiResource['record_type']);

        $page = 1;
        do {
            $records = $recordsTable->findBy($exportQuery, 100, $page++);
            foreach ($records as $record) {
                $representation = $recordAdapter->getRepresentation($record);
                $job->makeFile(sprintf('%s.json', $record->id), json_encode($representation, JSON_PRETTY_PRINT));
            }
        } while ($records);
    }

    public function getApiResources($resourceType = null)
    {
    }
}
