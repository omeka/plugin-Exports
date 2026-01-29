<?php
class Job_ExportsExport extends Job_AbstractStaticSite
{
    protected $_export;

    public function perform()
    {
        try {
            $this->setStatus(Process::STATUS_IN_PROGRESS);
            // @todo: do the export
            $this->setStatus(Process::STATUS_COMPLETED);
        } catch (Exception $e) {
            $this->setStatus(Process::STATUS_ERROR);
            _log($e->getMessage(), Zend_Log::ERR);
        }
    }

    public function getExport()
    {
        if (null === $this->_export) {
            $exportId = $this->_options['export_id'];
            $this->_export = $this->_db->getTable('ExportsExport')->find($exportId);
        }
        return $this->_export;
    }

    public function setStatus($status)
    {
        $this->getExport()->setStatus($status);
        $this->getExport()->save();
    }
}
