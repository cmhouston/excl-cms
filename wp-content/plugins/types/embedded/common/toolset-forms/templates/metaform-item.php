<div class="js-wpt-field-item wpt-field-item">
    <?php echo $out; ?>
    <?php if ( @$cfg['repetitive'] ): ?>
        <div class="wpt-repctl">
            <div class="js-wpt-repdrag wpt-repdrag">&nbsp;</div>
            <a class="js-wpt-repdelete button-secondary"><?php printf(__('Delete %s'), strtolower( $cfg['title'])); ?></a>
        </div>
    <?php endif; ?>
</div>
