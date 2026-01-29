<?php
echo head([
    'title' => __('Create export'),
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
        </div>
    </section>
</form>

<?php echo foot(); ?>
