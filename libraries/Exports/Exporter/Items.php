<?php
class Exports_Exporter_Items implements Exports_Exporter_ExporterInterface
{
    public function getLabel()
    {
        return __('Items');
    }

    public function getDescription()
    {
        return __('Export a file containing data about selected items.');
    }

    public function getForm($view)
    {
        $form = <<<'FORM'
        <div class="field">
            <div class="two columns alpha">
                <label for="label">%s</label>
            </div>
            <div class="inputs five columns omega">
                %s
            </div>
        </div>
        FORM;
        return sprintf(
            $form,
            __('Search Query'),
            $view->formText('data[query]', '', [])
        );
    }
}
