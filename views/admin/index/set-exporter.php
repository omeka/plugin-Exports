<?php
echo head(['title' => __('Create Export'), 'bodyclass' => 'exports set-export']);
?>

<form method="post">
    <section class="seven columns alpha">
        <legend><?php echo __('What would you like to export?'); ?></legend>
        <?php foreach ($exporterManager->getExporters() as $exporterName => $exporter): ?>
        <div class="field">
            <label>
                <input type="radio" name="exporter" value="<?php echo html_escape($exporterName); ?>" required>
                <?php echo html_escape(__($exporter->getLabel())); ?>
            </label>
            <?php if ($exporter->getDescription()): ?>
            <div><?php echo __($exporter->getDescription()); ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_set_export', __('Next'), ['class' => 'full-width green button']); ?>
            <a class="re-export full-width red button" href="<?php echo html_escape(url('exports')); ?>"><?php echo __('Cancel'); ?></a>
        </div>
    </section>
</form>

<?php echo foot(); ?>
