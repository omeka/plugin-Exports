<?php
abstract class Job_AbstractExports extends Omeka_Job_AbstractJob
{
    protected $_export;
    protected $_exportsDirectoryPath;
    protected $_exportDirectoryPath;

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
    public function deleteExportDirectory()
    {
        $path = $this->getExportDirectoryPath();
        if (is_dir($path) && is_writable($path)) {
            $command = sprintf('rm -r %s', escapeshellarg($path));
            $this->execute($command);
        }
    }

    /**
     * Delete the export ZIP file from the server.
     */
    public function deleteExportZip()
    {
        $path = sprintf('%s.zip', $this->getExportDirectoryPath());
        if (is_file($path) && is_writable($path)) {
            $command = sprintf('rm -r %s', escapeshellarg($path));
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
        return $this->_options['export_name'] ?? $this->getExport()->getName();
    }

    /**
     * Get the web root of the site.
     *
     * Background processes run from the command line, where Omeka can't detect
     * the web root, so WEB_ROOT is wrong there. Use the web root passed by the
     * request that dispatched the job.
     */
    public function getWebRoot()
    {
        return $this->_options['web_root'] ?? WEB_ROOT;
    }

    /**
     * Set the web URL of local file storage.
     *
     * Unless configured, local file storage derives its web URL from WEB_ROOT,
     * which is wrong in background processes.
     */
    public function setStorageWebDir()
    {
        $storageAdapter = Zend_Registry::get('storage')->getAdapter();
        if ($storageAdapter instanceof Omeka_Storage_Adapter_Filesystem
            && WEB_FILES === $storageAdapter->getOptions()['webDir']
        ) {
            $storageAdapter->setWebDir(sprintf('%s/files', $this->getWebRoot()));
        }
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
