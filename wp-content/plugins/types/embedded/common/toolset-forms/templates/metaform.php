<?php
$child_div_classes = array( 'js-wpt-field-items' );
if ( $cfg['use_bootstrap'] && in_array( $cfg['type'], array( 'date', 'select' ) ) ) {
    $child_div_classes[] = 'form-inline';
}
?><div class="js-wpt-field wpt-field js-wpt-<?php echo $cfg['type']; ?> wpt-<?php echo $cfg['type']; ?><?php if ( @$cfg['repetitive'] ) echo ' js-wpt-repetitive wpt-repetitive'; ?><?php do_action('wptoolset_field_class', $cfg); ?>" data-wpt-type="<?php echo $cfg['type']; ?>" data-wpt-id="<?php echo $cfg['id']; ?>">
    <div class="<?php echo implode( ' ', $child_div_classes ); ?>">
    <?php foreach ( $html as $out ): include 'metaform-item.php';
    endforeach; ?>
    </div>
    <?php if ( @$cfg['repetitive'] ): ?>
        <a href="#" class="js-wpt-repadd wpt-repadd button-primary"><?php printf(__('Add new %s'), $cfg['title']); ?></a>
<?php endif; ?>
</div>
