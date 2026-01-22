<?php
class Exports_ExportsController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ExportsExport');
    }
}
