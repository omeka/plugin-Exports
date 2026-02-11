<?php if (!ExportsPlugin::exportsStorageIsValid()): ?>
<div id="flash">
    <ul>
        <li class="error"><?php echo sprintf(__('Invalid storage directory. This is the directory where exports will be stored and available for download. You will not be able to create exports until the "%s" directory exists and is writable by the web server.'), sprintf('%s/exports', FILES_DIR)); ?></li>
    </ul>
</div>
<?php endif; ?>
<div class="field">
    <div class="two columns alpha">
        <label for="exports_directory_path"><?php echo __('Working directory'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('Enter the path to the directory where your exports will be built. The directory must exist and be writable by the web server.'); ?></p>
        <?php echo $this->formText(
            'exports_directory_path',
            get_option('exports_directory_path')
        ); ?>
    </div>
</div>
