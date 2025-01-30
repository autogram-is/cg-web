<?php

function cg_register_block_styles() {
  /**
   * Additional Styles for the core heading block
   */
  register_block_style('core/heading',
    array('name' => 'type-display', 'label' => __( 'Display', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/heading',
    array('name' => 'hed-accent', 'label' => __( 'Accented', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Additional Styles for the core heading block
   */
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-deep-steel','label' => __( 'Dark Background', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Additional Styles for the Cumming Group statistics block
   */
  register_block_style('acf/cg-statistics',
    array('name' => 'horizontal','label' => __( 'Horizontal', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'hero','label' => __( 'Hero', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'standout','label' => __( 'Standout', 'textdomain' ), 'is_default' => false)
  );
}

function cg_block_type_variations($variations, $block_type ) {

	if ( 'namespace/block-a' === $block_type->name ) {
		// Add variations to the `$variations` array.
	} elseif ( 'namespace/block-b' === $block_type->name ) {
		// Add more variations to the `$variations` array.
	}

	return $variations;
}

