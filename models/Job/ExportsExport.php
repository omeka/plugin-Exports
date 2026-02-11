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

            if (!ExportsPlugin::canExport()) {
                throw new Exception('Invalid working and/or storage directory.');
            }

            // Make the export directory.
            $this->makeDirectory('');

            // Delegate the export to the exporter.
            $this->getExport()->getExporter()->export($this);

            // Cancel the export if the export directory is empty.
            if (2 === count(scandir($this->getExportDirectoryPath()))) {
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

            // Copy the export ZIP file to Omeka storage.
            $storage = Zend_Registry::get('storage');
            $storage->store(
                sprintf('%s/%s.zip', $this->getExportsDirectoryPath(), $this->getExportName()),
                sprintf('exports/%s.zip', $this->getExportName())
            );

            $this->setStatus(Process::STATUS_COMPLETED);

        } catch (Exception $e) {
            $this->setStatus(Process::STATUS_ERROR);
            _log($e->getMessage(), Zend_Log::ERR);

        } finally {
            // Always attempt to delete working directory artifacts.
            $this->deleteExportDirectory();
            $this->deleteExportZip();
        }
    }
}
