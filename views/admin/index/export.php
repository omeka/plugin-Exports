<?php
echo head(['title' => __('Create export'), 'bodyclass' => 'exports export']);
echo flash();
?>

<form method="post">
    <?php echo $csrf; ?>
    <?php echo $this->formHidden('exporter_name', $exporterName); ?>
    <section class="seven columns alpha">
        <div class="field">
            <div class="two columns alpha">
                <label for="exporter"><?php echo __('Exporter'); ?></label>
            </div>
            <div class="inputs five columns omega">
                <?php echo $this->formText('exporter', $exporter->getLabel(), ['disabled' => true]); ?>
            </div>
        </div>
        <div class="field">
            <div class="two columns alpha">
                <label for="label"><?php echo __('Label'); ?></label>
            </div>
            <div class="inputs five columns omega">
                <?php echo $this->formText('label', sprintf('%s - %s', $exporter->getLabel(), date('Y-m-d\TH:i:s')), []); ?>
            </div>
        </div>
        <?php echo $exporter->getForm($this); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Begin Export'), ['class' => 'full-width green button']); ?>
        </div>
    </section>
</form>

<?php echo foot(); ?>
