<?php
echo head([
    'title' => __('Exports') . ' ' . __('(%s total)', $total_results),
    'bodyclass' => 'exports browse',
]);
$sortLinks = [
    __('Label') => 'label',
    __('Exporter') => 'exporter_name',
    __('Status') => null,
    __('Owner') => null,
    __('Created') => 'created',
];
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
            <?php $exporter = $exporterManager->get($export->exporter_name); ?>
            <tr>
                <td><?php echo $export->label; ?></td>
                <td><?php echo $exporter->getLabel(); ?></td>
                <td></td>
                <td><?php
                    $owner = $export->getOwner();
                    echo $owner
                        ? sprintf('%s', link_to($owner, 'edit', $owner->username, ['class'=>'edit']))
                        : sprintf('[%s]', __('unknown')); ?>
                </td>
                <td><?php echo format_date($export->created); ?></td>
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
