<div class="field">
    <div class="two columns alpha">
        <label for="exports_directory_path"><?php echo __('Exports directory path'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __('Enter the path to the directory where your exports will be saved. The path must exist and be writable by the web server.'); ?></p>
        <?php echo $this->formText(
            'exports_directory_path',
            get_option('exports_directory_path')
        ); ?>
    </div>
</div>
