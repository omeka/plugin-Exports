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
            $resourceTypes[$apiResourceName] = $apiResourceName;
        }
        asort($resourceTypes);
        $resourceTypes = ['' => __('Select Below')] + $resourceTypes;

        // Add the form elements.
        $form->addElement('select', 'resource', [
            'label' => __('Resource Type'),
            'description' => __('Select the type of resource to export.'),
            'multiOptions' => $resourceTypes,
        ]);
        $form->addElement('text', 'query', [
            'label' => __('Search Query'),
        ]);
    }

    public function export(Job_ExportsExport $job)
    {
        // @todo: Make the Items export
    }
}
