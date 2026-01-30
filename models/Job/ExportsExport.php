<?php
class Job_ExportsExport extends Job_AbstractExports
{
    /**
     * Perform the export.
     */
    public function perform()
    {
        try {
            $this->setStatus(Process::STATUS_IN_PROGRESS);

            // Make the export directory.
            $this->makeDirectory('');

            // Delegate the export to the exporter.
            $this->getExport()->getExporter()->export($this);

            // Cancel the export if the export directory is empty.
            if (2 === count(scandir($this->getExportDirectoryPath()))) {
                $this->deleteExportDirectory();
                throw new Exception('Nothing found in export directory.');
            }

            // Create the export ZIP file.
            $command = sprintf(
                'cd %s && zip --recurse-paths ../%s .',
                sprintf('%s/%s', $this->getExportsDirectoryPath(), $this->getExportName()),
                sprintf('%s.zip', $this->getExportName()),
                $this->getExportName()
            );
            $this->execute($command);

            // Delete leftover server artifacts.
            $this->deleteExportDirectory();

            $this->setStatus(Process::STATUS_COMPLETED);

        } catch (Exception $e) {
            $this->setStatus(Process::STATUS_ERROR);
            _log($e->getMessage(), Zend_Log::ERR);
        }
    }
}
