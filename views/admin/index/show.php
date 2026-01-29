<?php
echo head([
    'title' => sprintf(__('Export: "%s"'), $exports_export->getLabel()),
    'bodyclass' => 'exports show',
]);
echo flash();
?>
<?php echo foot(); ?>
