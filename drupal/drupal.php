<?php
/**
 * @file
 * Drupal's PHP snippets.
 */

/**
 * Get table rows count.
 *
 * @param string $table_name
 *   Table name.
 *
 * @return int|null
 *   Count of table rows or NULL.
 */
function drupal_get_table_rows_count($table_name) {
  if (db_table_exists($table_name)) {
    return db_query("SELECT COUNT(*) FROM {$table_name}")->fetchField();
  }
  return NULL;
}

/**
 * Get variables data array, provided by module.
 *
 * @param string $module_name
 *   Module name.
 *
 * @return array|null
 *   Variables data array or NULL.
 */
function drupal_get_module_vars($module_name) {
  if (is_string($module_name)) {
    // Get variables data from DB.
    $query = "SELECT name, value FROM {variable} WHERE name LIKE :pattern";
    $vars = db_query($query, array(':pattern' => db_like($module_name) . '%'))
      ->fetchAllAssoc('name');
    if (!empty($vars)) {
      $return = array();
      foreach ($vars as $value) {
        $return[$value->name] = unserialize($value->value);
      }
    }
  }
  return !isset($return) ? NULL : $return;
}

/**
 * Redirect page with a clean URL(without any query parameters).
 */
function drupal_redirect_to_clean_url() {
  $url = $GLOBALS['base_path'] . request_path();
  drupal_redirect_to_url($url);
}

/**
 * Redirect page to URL.
 *
 * @param string $url
 *   URL path.
 */
function drupal_redirect_to_url($url) {
  header('Location: ' . $url);
}

/**
 * Getting term depth.
 *
 * @param int $tid
 *   Taxonomy term ID.
 *
 * @return int
 *   Depth value.
 */
function drupal_get_term_depth($tid) {
  $query = "SELECT parent FROM {taxonomy_term_hierarchy} WHERE tid = :tid";
  $parent = db_query($query, array(':tid' => $tid))->fetchField();
  return ($parent == 0) ? 1 : 1 + drupal_get_term_depth($parent);
}

/**
 * Get Vocabulary ID by machine name.
 *
 * @param string $machine_name
 *   Taxonomy vocabulary machine name.
 *
 * @return int|null
 *   Taxonomy vocabulary ID.
 */
function drupal_get_vid_by_machine_name($machine_name) {
  $vid = db_select('taxonomy_vocabulary', 'v')
    ->fields('v', array('vid'))
    ->condition('machine_name', $machine_name)
    ->execute()
    ->fetchField();
  return !empty($vid) ? $vid : NULL;
}

/**
 * Get Node ID from previous path alias.
 *
 * @return int|null
 *   Node ID.
 */
function drupal_get_nid_from_previous_path_alias() {
  $server = &$_SERVER;
  $alias = str_replace($GLOBALS['base_url'] . '/', '', $server['HTTP_REFERER']);
  $path = drupal_get_normal_path($alias);
  $node = menu_get_object("node", 1, $path);
  return is_object($node) ? $node->nid : NULL;
}

/**
 * Load node by title.
 *
 * @param string $title
 *   Node's title.
 *
 * @param string $node_type
 *   Node type(e.g. Content type).
 *
 * @return object|null
 *   Node object.
 */
function drupal_node_load_by_title($title, $node_type) {
  $query = new EntityFieldQuery();
  $nodes = $query->entityCondition('entity_type', 'node')
    ->propertyCondition('type', $node_type)
    ->propertyCondition('title', $title)
    ->propertyCondition('status', 1)
    ->range(0, 1)->execute();
  if (isset($nodes['node']) && !empty($nodes['node'])) {
    $ids = array_keys($nodes['node']);
    if (isset($ids[0])) {
      $node = node_load($ids[0]);
    }
  }
  return isset($node) ? $node : NULL;
}

/**
 * Get the tid or term object by the term name and (optional) vocabulary name.
 *
 * @param string $term_name
 *   Taxonomy term name.
 *
 * @param bool $load
 *   If TRUE, returns the loaded object.
 *
 * @param null|string $vocabulary
 *   Vocabulary machine name.
 *
 * @return int|null|object
 *   Taxonomy term ID, taxonomy term object or NULL.
 */
function drupal_get_term_by_name($term_name, $load = FALSE, $vocabulary = NULL) {
  if (!empty($vocabulary)) {
    $select = db_select('taxonomy_vocabulary', 'v');
    $select->fields('v', array('vid'));
    $select->condition('machine_name', $vocabulary);
    $select->join('taxonomy_term_data', 't', 't.vid = v.vid');
    $select->fields('t', array('tid'));
    $select->condition('t.name', $term_name);
    $tid = $select->execute()->fetchAllAssoc('tid');
    if (!empty($tid)) {
      if (is_array($tid)) {
        $tid = array_keys($tid);
        $tid = array_shift($tid);
      }
    }
  }
  else {
    $select = db_select('taxonomy_term_data', 't')
      ->fields('t', array('tid'))
      ->condition('name', $term_name)
      ->execute();
    $tid = $select->fetchField();
  }
  if (!empty($tid)) {
    $return = !$load ? $tid : taxonomy_term_load($tid);
  }
  return isset($return) ? $return : NULL;
}

/**
 * Reindex Search API index.
 *
 * @param string $index_name
 *   Index name.
 */
function drupal_search_api_reindex($index_name) {
  if (function_exists('search_api_index_load')
    && function_exists('search_api_index_items')) {
    // Load Search API Index.
    $search_api_index = search_api_index_load($index_name);
    // Clear the index.
    if (method_exists($search_api_index, 'clear')) {
      $search_api_index->clear();
    }
    // Run!
    search_api_index_items($search_api_index, -1);
  }
}

/**
 * Get all the (previous) statuses for a commerce order.
 *
 * @param int $order_id
 *   The commerce order id.
 *
 * @return array
 *   All the statuses that a order have had, including the current one.
 */
function drupal_get_all_previous_order_statuses($order_id) {
  $statuses = db_select('commerce_order_revision', 'r')
    ->distinct()
    ->fields('r', array('status'))
    ->condition('order_id', $order_id)
    ->execute()
    ->fetchCol();
  return $statuses;
}

/**
 * Create path alias if it isn't exist.
 *
 * @param string $source
 *   Source URL(example: 'node/1').
 *
 * @param string $alias
 *   Alias URL(example: 'content/my_content').
 *
 * @return bool
 *   TRUE - if alias has been created, FALSE otherwise.
 */
function drupal_create_path_alias($source, $alias) {
  if (!drupal_valid_path($source)) {
    return FALSE;
  }
  $path = array(
    'source' => $source,
    'alias'  => $alias,
  );
  $alias = drupal_lookup_path('alias', $path['source']);
  if (!empty($alias)) {
    return FALSE;
  }
  path_save($path);
  return TRUE;
}

/**
 * Get the block.
 *
 * @param string $module
 *   Module, which provide this block.
 *
 * @param string $delta
 *   Block's delta.
 *
 * @param int $return
 *   Optional param for returning value.
 *    1 - block object;
 *    2 - render-able array;
 *    3 - rendered HTML;
 *   Defaults to 1.
 *
 * @return array|string
 *   Block's object, render-able block array
 *   or rendered block's HTML.
 */
function drupal_get_block($module, $delta, $return = 1) {
  if (!($return >= 1 && $return <= 3)) {
    return NULL;
  }
  $block = NULL;
  // Ensure block module is enabled.
  if (function_exists('_block_load_blocks')) {
    // Load blocks information from the database.
    foreach (_block_load_blocks() as $region) {
      foreach ($region as $key => $obj) {
        // Catch the block by module and delta.
        if ($key == $module . '_' . $delta) {
          // Return the block's object.
          if ($return == 1) {
            return $obj;
          }
          $block = $obj;
          // Break the cycle.
          break;
        }
      }
    }
    if (is_object($block)) {
      // Build the render-able block's array.
      $block = _block_get_renderable_array(_block_render_blocks(array($block)));
    }
  }
  if (!empty($block)) {
    return $return != 2 ? render($block) : $block;
  }
  return NULL;
}
