<?php
class ExportsExport extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $owner_id;
    public $process_id;
    public $exporter_name;
    public $name;
    public $label;
    public $data;
    public $added;
    public $modified;

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
    }

    public function getResourceId()
    {
        return 'Exports_Exports';
    }
}
