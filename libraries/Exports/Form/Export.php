<?php
class Exports_Form_Export extends Omeka_Form
{
    public function __construct($exporter)
    {
        parent::__construct();

        $this->addElement('hidden', 'exporter_name', [
            'value' => $exporter->getName(),
            // Remove all decorators from this hidden element.
            'decorators' => ['ViewHelper'],
        ]);

        $this->addElement('text', 'exporter', [
            'label' => __('Exporter'),
            'value' => $exporter->getLabel(),
            'attribs' => ['disabled' => true],
        ]);

        $this->addElement('text', 'label', [
            'label' => __('Label'),
            'value' => sprintf('%s - %s', $exporter->getLabel(), date('Y-m-d\TH:i:s')),
            'required' => true,
        ]);

        // Add the data sub-form. Here we must set decorators to remove the
        // default dt/dd tags wrapping the fieldset and elements.
        $dataForm = new Zend_Form_SubForm;
        $dataForm->setDecorators(['FormElements']);
        $dataForm->setElementDecorators($this->getDefaultElementDecorators());
        $dataForm->setElementsBelongTo('data');
        $this->addSubform($dataForm, 'data');

        // Add exporter-specific elements.
        $exporter->addElements($dataForm);
    }
}
