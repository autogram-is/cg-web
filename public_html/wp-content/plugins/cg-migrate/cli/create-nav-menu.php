<?php
function cg_cli_build_nav_menus($dry_run = false) {
  $locations = get_theme_mod('nav_menu_locations');

  $locations['primary'] = cg_cli_build_nav_menu('cg-primary-nav', 'na', $dry_run);
  $locations['primary-eu'] = cg_cli_build_nav_menu('cg-primary-nav-eu', 'eu', $dry_run);
  $locations['footer'] = cg_cli_build_nav_menu('cg-footer-links', $dry_run);
  $locations['fine-print'] = cg_cli_build_nav_menu('cg-fine-print', $dry_run);
  
  set_theme_mod('nav_menu_locations', $locations);
}

function cg_cli_build_nav_menu(string $menu, $locale = false, $dry_run = false) {
  $menu_object = wp_get_nav_menu_object($menu);
  if ($menu_object) {
    if (!$dry_run) {
      wp_delete_nav_menu($menu_object->term_id);
    }
    WP_CLI::log(($dry_run ? "Dry-Run: " : "") . "Deleted old '$menu'  ($menu_object->term_id)");
  }
  if (!$dry_run) {
    $menu_id = wp_create_nav_menu($menu);
    WP_CLI::log(($dry_run ? "Dry-Run: " : "") . "Created new '$menu' ($menu_id)");
  }

  if (!$menu_id) {
    return FALSE;
  }

  $order = 0;
  $parents = [];
  $items = load_migration_csv($menu . '.csv');
  $sectors = object_by_locale('sector');
  $services = object_by_locale('service');

  foreach ($items as $item) {
    if ($item['type'] === 'custom') {
      if (!$dry_run) {
        $id = wp_update_nav_menu_item($menu_id, 0, array(
          'menu-item-title' => wp_slash($item['title']),
          'menu-item-url' => $item['url'],
          'menu-item-description' => wp_slash($item['description']),
          'menu-item-type' => 'custom',
          'menu-item-status' => 'publish',
          'menu-item-position' => $order++,
        ));
        if ($item['url'] === '#') {
          $parents[$item['title']] = $id;
        }
        WP_CLI::log(($dry_run ? "Dry-Run: " : "") . "Added '" . $item['title'] . "' ($id) to $menu");
      }
    } elseif($item['type'] === 'taxonomy')  {
      $term = get_term_by('name', $item['object-slug'], $item['object']);
      if ($term) {
        if (!$dry_run) {
          $id = wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => wp_slash($item['title'] ?? $term->name),
            'menu-item-url' => $item['url'],
            'menu-item-description' => wp_slash($item['description']),
            'menu-item-object' => $term->taxonomy,
            'menu-item-object-id' => $term->term_id,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => $parents[$item['parent']] ?? NULL,
            'menu-item-position' => $order++,
          ));
        }
        WP_CLI::log(($dry_run ? "Dry-Run: " : "") . ($item['parent'] ? '  ' : '') . "Added '" . $item['title'] . "' ($id) to $menu");  
      }
    } elseif($item['type'] === 'post_type')  {
      $post = get_post_by_name($item['object-slug'], $item['object']);
      if (!$post) {
        $post_id = wp_insert_post(array(
          'post_title' => $item['title'],
          'post_name' => $item['object-slug'],
          'post_type' => $item['object'],
          'post_status' => 'publish',
        ));
        $post = get_post($post_id);
        WP_CLI::log(($dry_run ? "Dry-Run: " : "") . "Created " . $post->post_title . " '" . $post->ID . "'");
      }

      if (!$dry_run) {
        $id = wp_update_nav_menu_item($menu_id, 0, array(
          'menu-item-title' => wp_slash($item['title'] ?? $post->post_title),
          'menu-item-url' => $item['url'],
          'menu-item-description' => wp_slash($item['description']),
          'menu-item-object' => $post->post_type,
          'menu-item-object-id' => $post->ID,
          'menu-item-type' => 'post_type',
          'menu-item-status' => 'publish',
          'menu-item-parent-id' => $parents[$item['parent']] ?? NULL,
          'menu-item-position' => $order++,
        ));
        WP_CLI::log(($dry_run ? "Dry-Run: " : "") . ($item['parent'] ? '  ' : '') . "Added '" . $item['title'] . "' ($id) to $menu");
      }

      if ($locale) {
        if ($item['title'] === 'Services') {
          foreach ($services[$locale] as $service) {
            $sid = wp_update_nav_menu_item($menu_id, 0, array(
              'menu-item-title' => wp_slash($service->post_title),
              'menu-item-object-id' => $service->ID,
              'menu-item-object' => 'service',
              'menu-item-type' => 'post_type',
              'menu-item-status' => 'publish',
              'menu-item-parent-id' => $id,
              'menu-item-position' => $order++,
            ));
            WP_CLI::log(($dry_run ? "Dry-Run: " : "") . "  Added '" . $service->post_title . "' ($sid) to $menu");
          };
        } else if ($item['title'] === 'Sectors') {
          foreach ($sectors[$locale] as $sector) {
            $sid = wp_update_nav_menu_item($menu_id, 0, array(
              'menu-item-title' => wp_slash($sector->post_title),
              'menu-item-object-id' => $sector->ID,
              'menu-item-object' => 'sector',
              'menu-item-type' => 'post_type',
              'menu-item-status' => 'publish',
              'menu-item-parent-id' => $id,
              'menu-item-position' => $order++,
            ));
            WP_CLI::log(($dry_run ? "Dry-Run: " : "") . "  Added '" . $sector->post_title . "' ($sid) to $menu");
          };
        }
      }
    }
  }

  return $menu_id;
}

function object_by_locale(string $post_type = 'post') {
  $output = [];
  $query = new WP_Query(array(
    'post_type' => $post_type,
    'fields' => 'ids',
    'posts_per_page' => -1,
  ));

  $ids = $query->posts;
  foreach ($ids as $id) {
    $post = get_post($id);
    $locale = get_field('locale', $post->ID);
    if ($locale !== 'eu') {
      $output['na'][] = $post;
    }
    if ($locale !== 'na') {
      $output['eu'][] = $post;
    }
  }
  return $output;
}
