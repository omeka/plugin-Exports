<?php
class ExportsPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
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
    }

    public function hookConfig($args)
    {
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Exports_Exports');
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = [
            'label' => __('Exports'),
            'uri' => url('exports'),
            'resource' => ('Exports_Exports'),
        ];
        return $nav;
    }

    public function filterExportsExporters($exporters)
    {
        $exporters['items'] = new Exports_Exporter_Items;
        return $exporters;
    }
}
