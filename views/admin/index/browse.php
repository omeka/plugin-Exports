<?php
echo head([
    'title' => __('Browse Exports') . ' ' . __('(%s total)', $total_results),
    'bodyclass' => 'exports browse',
]);
$sortLinks = [
    __('Label') => 'label',
    __('Exporter') => 'exporter_name',
    __('Status') => null,
    __('Owner') => null,
    __('Created') => 'created',
];
echo flash();
?>
<?php echo pagination_links(['attributes' => ['aria-label' => __('Top pagination')]]); ?>

<a href="<?php echo html_escape(url(['action' => 'set-exporter'])); ?>" class="set-exporter full-width-mobile button green"><?php echo __('Create an Export'); ?></a>

<?php if ($total_results): ?>

<div class="table-responsive">
    <table id="exports">
        <thead>
            <tr>
                <?php echo browse_sort_links($sortLinks, ['link_tag' => 'th scope="col"', 'list_tag' => '']); ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach (loop('ExportsExport') as $export): ?>
            <?php
            $exporter = $export->getExporter();
            $owner = $export->getOwner()
            ?>
            <tr>
                <td><span class="title"><?php echo link_to($export, 'show', $export->getLabel()); ?></span></td>
                <td><?php echo $exporter->getLabel(); ?></td>
                <td><?php echo $export->getStatus() ? __($export->getStatus()) : __('unknown'); ?></td>
                <td><?php echo $owner ? sprintf('%s (%s)', $owner->name, $owner->username) : ''; ?></td>
                <td><?php echo format_date($export->created, Zend_Date::DATETIME_MEDIUM); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php else: ?>

<h2><?php echo __('No exports found.'); ?></h2>

<?php endif; ?>

<?php echo pagination_links(['attributes' => ['aria-label' => __('Bottom pagination')]]); ?>

<?php echo foot(); ?>
