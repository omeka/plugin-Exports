<?php
class Exports_Exporter_Records implements Exports_Exporter_ExporterInterface
{
    public function getName()
    {
        return 'records';
    }

    public function getLabel()
    {
        return __('Records');
    }

    public function getDescription()
    {
        return __('Export a file containing data about selected records.');
    }

    public function addElements(Zend_Form_SubForm $form)
    {
        // Get the available API record types.
        $apiResources = Omeka_Controller_Plugin_Api::getApiResources();
        $recordTypes = [];
        foreach ($apiResources as $apiResourceName => $apiResource) {
            if (!isset($apiResource['record_type'])) {
                // Only resources that have a record type can be exported.
                continue;
            }
            $recordTypes[$apiResourceName] = $apiResource['record_type'];
        }
        asort($recordTypes);
        $recordTypes = ['' => __('Select Below')] + $recordTypes;

        // Add the form elements.
        $form->addElement('select', 'record', [
            'label' => __('Record Type'),
            'description' => __('Select the type of record to export.'),
            'multiOptions' => $recordTypes,
            'required' => true,
        ]);
        $form->addElement('text', 'query', [
            'label' => __('Search Query'),
            'description' => __('Enter the query used to filter the records to be exported. If no query is entered, all available records will be exported.'),
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
        $form->addElement('text', 'multivalue_separator', [
            'label' => __('Multivalue Separator'),
            'description' => __('Enter the character to separate multiple values in a cell'),
            'value' => '|',
            'required' => true,
        ]);
    }

    public function export(Job_ExportsExport $job)
    {
        $export = $job->getExport();
        $exportData = $export->getData();

        if (!isset($exportData['record'])) {
            throw new Exception(sprintf('Exports: Missing "record" option for export "%s" using exporter "Records".', $export->getLabel()));
        }
        $apiResources = Omeka_Controller_Plugin_Api::getApiResources();
        if (!isset($apiResources[$exportData['record']])) {
            throw new Exception(sprintf('Exports: Unknown API record "%s" for export "%s" using exporter "Records".', $exportData['record'], $export->getLabel()));
        }
        $apiResource = $apiResources[$exportData['record']];
        if (!isset($apiResource['record_type'])) {
            throw new Exception(sprintf('Exports: Unsupported API record "%s" for export "%s" using exporter "Records".', $exportData['record'], $export->getLabel()));
        }

        // Set the record query.
        parse_str($exportData['query'] ?? [], $recordQuery);

        // Set the record table
        $recordsTable = get_db()->getTable($apiResource['record_type']);

        // Set the API record adapter.
        $recordAdapterClass = sprintf('Api_%s', $apiResource['record_type']);
        $recordAdapter = new $recordAdapterClass;

        // Delegate to the export format.
        switch ($exportData['format'] ?? null) {
            case 'csv':
                $this->exportCsv($job, $recordsTable, $recordAdapter, $recordQuery);
                break;
            case 'json':
                $this->exportJson($job, $recordsTable, $recordAdapter, $recordQuery);
                break;
            default:
                throw new Exception(sprintf('Exports: Invalid "format" option "%s" for export "%s" using exporter "Records".', $exportData['format'], $export->getLabel()));
        }
    }

    /**
     * Export to CSV.
     *
     * @param Job_ExportsExport $job
     * @param Omeka_Db_Table $recordsTable
     * @param Omeka_Record_Api_RecordAdapterInterface $recordAdapter
     * @param array $recordQuery
     */
    public function exportCsv($job, $recordsTable, $recordAdapter, $recordQuery)
    {
        $export = $job->getExport();

        // To avoid having to hold every CSV row in memory before writing to the
        // file, we're defining the header row first and then adding the record
        // rows using the header row as a template. This requires two passes of
        // the records.

        // Iterate every record, building the CSV header row.
        $page = 1;
        $headerRow = [];
        do {
            $records = $recordsTable->findBy($recordQuery, 100, $page++);
            foreach ($records as $record) {
                $representation = $recordAdapter->getRepresentation($record);
                foreach ($representation as $k => $v) {
                    $fieldData = $this->getFieldData($k, $v, $export);
                    if (is_array($fieldData)) {
                        foreach ($fieldData as $data) {
                            $headerRow[$data[0]] = $data[0];
                        }
                    }
                }
            }
        } while ($records);

        // Write the header row to the CSV file.
        ksort($headerRow);
        // Move the ID to the beginning of the array.
        if (isset($headerRow['id'])) {
            $id = $headerRow['id'];
            unset($headerRow['id']);
            $headerRow = ['id' => $id] + $headerRow;
        }
        $fp = fopen(sprintf('%s/%s.csv', $job->getExportDirectoryPath(), $export->getName()), 'w');
        fputcsv($fp, $headerRow, ',', '"', '');

        // Iterate every record, building one CSV record row at a time.
        $page = 1;
        $rowTemplate = array_fill_keys($headerRow, null);
        do {
            $records = $recordsTable->findBy($recordQuery, 100, $page++);
            foreach ($records as $record) {
                $representation = $recordAdapter->getRepresentation($record);
                $recordRow = $rowTemplate;
                foreach ($representation as $k => $v) {
                    $fieldData = $this->getFieldData($k, $v, $export);
                    if (is_array($fieldData)) {
                        foreach ($fieldData as $data) {
                            if (array_key_exists($data[0], $recordRow)) {
                                $recordRow[$data[0]] = $data[1];
                            }
                        }
                    }
                }
                // Write the record row to the CSV file.
                fputcsv($fp, $recordRow, ',', '"', '');
            }
        } while ($records);

        fclose($fp);
    }

    /**
     * Get CSV field data from a JSON-LD key-value pair.
     *
     * Determines whether to process the key-value pair and returns an array of
     * corresponding CSV header-field pairs.
     */
    public function getFieldData($k, $v, $export)
    {
        $exportData = $export->getData();
        $multivalueSeparator = $exportData['multivalue_separator'] ?? '|';

        // First, handle specific fields by key.
        if ('tags' === $k) {
            $tags = [];
            foreach ($v as $tag) {
                $tags[] = $tag['name'];
            }
            return [['tags', implode($multivalueSeparator, $tags)]];
        }
        if ('element_texts' === $k) {
            $elementTexts = [];
            foreach ($v as $elementText) {
                $header = sprintf('%s:%s', $elementText['element_set']['name'], $elementText['element']['name']);
                $elementTexts[$header][] = $elementText['text'];
            }
            $fieldData = [];
            foreach ($elementTexts as $header => $texts) {
                $fieldData[] = [$header, implode($multivalueSeparator, $texts)];
            }
            return $fieldData;
        }
        if ('metadata' === $k) {
            return [['metadata', json_encode($v)]];
        }

        // Next, let plugins return CSV field data.
        $fieldData = [];
        $fieldData = apply_filters(
            'exports_records_csv_get_field_data',
            $fieldData, // Plugins set CSV header-field pairs to this array
            [
                'k' => $k, // The JSON-LD key
                'v' => $v,  // The JSON-LD value
                'export' => $export, // The export record
            ]
        );
        if ($fieldData) {
            return $fieldData;
        }

        // Next, handle the remaining array and scalar fields.
        $getValueString = function ($v) {
            if (is_string($v) || is_bool($v) || is_int($v) || is_float($v) || is_null($v)) {
                return (string) $v;
            }
            return null;
        };
        if (is_array($v)) {
            $fieldData = [];
            foreach ($v as $subK => $subV) {
                if (in_array($subK, ['url', 'resource'])) {
                    continue;
                }
                $subValueString = $getValueString($subV);
                if (null !== $subValueString) {
                    $fieldData[] = [sprintf('%s_%s', $k, $subK), $subValueString];
                }
            }
            return $fieldData;
        }
        $valueString = $getValueString($v);
        if (null !== $valueString) {
            return [[$k, $valueString]];
        }

        // There's nothing to get.
        return null;
    }

    /**
     * Export to JSON.
     *
     * @param Job_ExportsExport $job
     * @param Omeka_Db_Table $recordsTable
     * @param Omeka_Record_Api_RecordAdapterInterface $recordAdapter
     * @param array $recordQuery
     */
    public function exportJson($job, $recordsTable, $recordAdapter, $recordQuery)
    {
        $export = $job->getExport();

        $totalCount = $recordsTable->count($recordQuery);

        $fp = fopen(sprintf('%s/%s.json', $job->getExportDirectoryPath(), $export->getName()), 'w');
        fwrite($fp, '[');

        $page = 1;
        $count = 0;
        do {
            $records = $recordsTable->findBy($recordQuery, 100, $page++);
            foreach ($records as $record) {
                $count++;
                $representation = $recordAdapter->getRepresentation($record);
                $recordJson = json_encode($representation);
                if ($count === $totalCount) {
                    // The JSON specification does not allow trailing commas
                    // within arrays. Don't include it on the last resource.
                    fwrite($fp, $recordJson);
                } else {
                    fwrite($fp, sprintf('%s,', $recordJson));
                }
            }
        } while ($records);

        fwrite($fp, ']');
        fclose($fp);
    }
}
