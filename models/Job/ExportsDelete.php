<?php
class Job_ExportsDelete extends Job_AbstractExports
{
    /**
     * Delete the export.
     */
    public function perform()
    {
        // Delete working directory artifacts
        $this->deleteExportDirectory();
        $this->deleteExportZip();

        // Delete the export ZIP file from Omeka storage.
        $storage = Zend_Registry::get('storage');
        $storage->delete(
            sprintf('exports/%s.zip', $this->getExportName())
        );
    }
}
