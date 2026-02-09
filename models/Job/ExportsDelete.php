<?php
class Job_ExportsDelete extends Job_AbstractExports
{
    /**
     * Delete the export.
     */
    public function perform()
    {
        $this->deleteExportDirectory();
        $this->deleteExportZip();
    }
}
