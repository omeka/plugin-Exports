<?php
echo head([
    'title' => __('Create Export'),
    'bodyclass' => 'exports export',
]);
echo flash();
?>

<form method="post">
    <section class="seven columns alpha">
        <?php echo $csrf; ?>
        <?php foreach ($form as $element): ?>
        <?php echo $element; ?>
        <?php endforeach; ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Begin Export'), ['class' => 'full-width green button']); ?>
            <a class="re-export full-width red button" href="<?php echo html_escape(url('exports')); ?>"><?php echo __('Cancel'); ?></a>
        </div>
    </section>
</form>

<?php echo foot(); ?>
