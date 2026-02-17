<?php
class ExportsPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'initialize',
        'install',
        'uninstall',
        'config_form',
        'config',
        'define_acl',
    );

    protected $_filters = array(
        'admin_navigation_main',
        'exports_exporters',
    );

    public function hookInitialize()
    {
        $exporterManager = new Exports_Exporter_Manager;
        Zend_Registry::set('exports_exporter_manager', $exporterManager);
    }

    public function hookInstall()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE `$db->ExportsExport` (
            `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
            `owner_id` INT UNSIGNED DEFAULT NULL,
            `exporter_name` VARCHAR(255) NOT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            `label` VARCHAR(255) NOT NULL,
            `status` VARCHAR(255) DEFAULT NULL,
            `data` LONGTEXT NOT NULL,
            `added` DATETIME NOT NULL,
            `modified` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);
    }

    public function hookUninstall()
    {
        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `$db->ExportsExport`");
    }

    public function hookConfigForm($args)
    {
        $exportsDirectoryPath = get_option('exports_directory_path');
        echo get_view()->partial(
            'exports-config-form.php',
            [
                'exports_directory_path' => $exportsDirectoryPath,
            ]
        );
    }

    public function hookConfig($args)
    {
        if (!self::exportsDirectoryPathIsValid($_POST['exports_directory_path'])) {
            throw new Omeka_Plugin_Installer_Exception('Invalid exports directory path. The path must be a directory and must be writable by the web server.');
        }
        set_option('exports_directory_path', $_POST['exports_directory_path']);
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Exports_Index');
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = [
            'label' => __('Exports'),
            'uri' => url('exports'),
            'resource' => ('Exports_Index'),
        ];
        return $nav;
    }

    public function filterExportsExporters($exporters)
    {
        $exporters['records'] = new Exports_Exporter_Records;
        return $exporters;
    }

    public static function exportsDirectoryPathIsValid($path = null)
    {
        $path = $path ?? get_option('exports_directory_path');
        return (is_dir($path) && is_writable($path));
    }

    public static function exportsStorageIsValid()
    {
        $storage = Zend_Registry::get('storage');

        // If using the filesystem storage adapter, the files/exports directory
        // must exist and be writable.
        if ($storage->getAdapter() instanceof Omeka_Storage_Adapter_Filesystem) {
            $dir = sprintf('%s/exports', FILES_DIR);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            return is_writable($dir);
        }

        // Any other storage adapter should work.
        return true;
    }

    public static function canExport()
    {
        return (self::exportsDirectoryPathIsValid() && self::exportsStorageIsValid());
    }
}
