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
    <a class="re-export" href="<?php echo html_escape(url('exports')); ?>"><?php echo __('Browse Exports'); ?></a>
    <h2><?php echo __('Exporter'); ?></h2>
    <p><?php echo __($exporter->getLabel()); ?></p>
    <h2><?php echo __('Status'); ?></h2>
    <p><?php echo $export->getStatus() ? __($export->getStatus()) : __('unknown'); ?></p>
    <?php if ('completed' === $export->getStatus()): ?>
    <h2><?php echo __('Download'); ?></h2>
    <p><a href="<?php echo html_escape($export->getUri()); ?>"><?php echo sprintf('%s.zip', $export->getName()); ?></a></p>
    <?php endif; ?>
    <h2><?php echo __('Name'); ?></h2>
    <p><?php echo $export->getName(); ?></p>
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
