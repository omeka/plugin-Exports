<?php
$export = $exports_export;
$exporter = $export->getExporter();
echo head([
    'title' => sprintf(__('Export: "%s"'), $export->getLabel()),
    'bodyclass' => 'exports show',
]);
echo flash();
?>

<section class="seven columns alpha">
    <a class="re-export" href="<?php echo html_escape(url('exports')); ?>"><?php echo __('Browse All'); ?></a>
    <h2><?php echo __('Exporter'); ?></h2>
    <p><?php echo __($exporter->getLabel()); ?></p>
    <h2><?php echo __('Status'); ?></h2>
    <p><?php echo $export->getStatus() ? __($export->getStatus()) : __('unknown'); ?></p>
    <h2><?php echo __('Export Path'); ?></h2>
    <p><?php echo sprintf('%s/%s.zip', get_option('exports_directory_path'), $export->getName()); ?></p>
    <h2><?php echo __('Data'); ?></h2>
    <pre style="font-size: 12px;"><?php echo json_encode($export->getData(), JSON_PRETTY_PRINT); ?></pre>
</section>

<section class="three columns omega">
    <div class="panel">
        <a class="re-export full-width green button" href="<?php echo html_escape(url(sprintf('exports/index/export/exporter/%s/id/%s', $exporter->getName(), $export->getId()))); ?>"><?php echo __('Re-export'); ?></a>
        <?php if (in_array($export->getStatus(), [Process::STATUS_COMPLETED, Process::STATUS_ERROR])): ?>
        <a class="full-width red button delete-confirm" href="<?php echo html_escape(record_url($export, 'delete-confirm', 'export')); ?>"><?php echo __('Delete'); ?></a>
        <?php endif; ?>
    </div>
</section>

<?php echo foot(); ?>
