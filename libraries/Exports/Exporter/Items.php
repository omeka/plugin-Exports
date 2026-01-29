<?php
class Exports_Exporter_Items implements Exports_Exporter_ExporterInterface
{
    public function getName()
    {
        return 'items';
    }

    public function getLabel()
    {
        return __('Items');
    }

    public function getDescription()
    {
        return __('Export a file containing data about selected items.');
    }

    public function addElements($form)
    {
        $form->addElement('text', 'query', [
            'label' => __('Search Query'),
        ]);
    }
}
