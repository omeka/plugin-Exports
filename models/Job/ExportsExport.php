<?php
class Job_ExportsExport extends Omeka_Job_AbstractJob
{
    protected $_export;
    protected $_exportsDirectoryPath;
    protected $_exportDirectoryPath;

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

    /**
     * Make a directory in the export directory.
     */
    public function makeDirectory($directoryPath)
    {
        mkdir(sprintf('%s/%s', $this->getExportDirectoryPath(), $directoryPath), 0755, true);
    }

    /**
     * Make a file in the export directory.
     */
    public function makeFile($filePath, $content = '')
    {
        file_put_contents(
            sprintf('%s/%s', $this->getExportDirectoryPath(), $filePath),
            $content
        );
    }

    /**
     * Delete the export directory from the server.
     */
    public function deleteExportDirectory(): void
    {
        $path = $this->getExportDirectoryPath();
        if (is_dir($path) && is_writable($path)) {
            $command = sprintf(
                'rm -r %s',
                escapeshellarg($path)
            );
            $this->execute($command);
        }
    }

    /**
     * Get the directory path where the exports are created.
     *
     * @return string
     */
    public function getExportsDirectoryPath()
    {
        if (null === $this->_exportsDirectoryPath) {
            $exportsDirectoryPath = get_option('exports_directory_path');
            if (!ExportsPlugin::exportsDirectoryPathIsValid($exportsDirectoryPath)) {
                throw new Exception\RuntimeException('Invalid directory path');
            }
            $this->_exportsDirectoryPath = $exportsDirectoryPath;
        }
        return $this->_exportsDirectoryPath;
    }

    /**
     * Get the directory path of the export.
     *
     * @return string
     */
    public function getExportDirectoryPath()
    {
        if (null === $this->_exportDirectoryPath) {
            $this->_exportDirectoryPath = sprintf(
                '%s/%s',
                $this->getExportsDirectoryPath(),
                $this->getExportName()
            );
        }
        return $this->_exportDirectoryPath;
    }

    /**
     * Get the export record.
     *
     * @return ExportsExport
     */
    public function getExport()
    {
        if (null === $this->_export) {
            $exportId = $this->_options['export_id'];
            $this->_export = $this->_db->getTable('ExportsExport')->find($exportId);
        }
        return $this->_export;
    }

    /**
     * Get the export name.
     */
    public function getExportName()
    {
        return $this->_options['export_name'] ?? $this->getExport()->name;
    }

    /**
     * Set the status of the export process.
     */
    public function setStatus($status)
    {
        $this->getExport()->setStatus($status);
        $this->getExport()->save();
    }

    /**
     * Execute a command.
     */
    public function execute($command)
    {
        $output = shell_exec($command);
        if (false === $output) {
            // Stop the job.
            throw new Exception(sprintf('Invalid command: %s', $command));
        }
    }
}
