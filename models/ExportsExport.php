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

    protected $_related = [
        'Data' => 'getData',
    ];

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
    }

    public function getResourceId()
    {
        return 'Exports_Exports';
    }

    protected function beforeSave($args)
    {
        $post = $args['post'];

        // The export name is a union of the exporter name, the timestamp when
        // the job was started (to ensure uniqueness and consistent file sorting),
        // and a random string (to further ensure uniqueness).
        $this->name = sprintf(
            '%s_%s_%s',
            $post['exporter_name'],
            time(),
            substr(md5(rand()), 0, 4)
        );

        // Encode the data before saving to the database.
        $this->data = json_encode($post['data']);
    }

    protected function afterSave($args)
    {
        // @todo: Dispatch export process
    }

    public function getData()
    {
        return json_decode($this->data, true);
    }
}
