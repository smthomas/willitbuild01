<?php

/**
 * @file
 * Post update functions for the gatsby module.
 */

/**
 * Updates the module weight to 100.
 */
function gatsby_post_update_001() {
  // Gatsby's hook_node_insert must run after all other modules have modified
  // the entity.
  module_set_weight('gatsby', 100);
}
