wp plugin deactivate fusion-builder fusion-core category-ajax-filter-pro custom-post-type-ui styles-and-layouts-for-gravity-forms hreflang-tags-by-dcgws redirection regenerate-thumbnails relevanssi the-events-calendar events-calendar-pro blogvault-real-time-backup insert-headers-and-footers duplicate-post wordpress-seo --allow-root
wp plugin activate advanced-custom-fields-pro admin-columns-for-acf-fields cg-core cg-migrate query-monitor query-monitor-twig-profile --allow-root
wp theme activate cumminggroup --allow-root

wp post delete $(wp post list --post_type=revision --format=ids --allow-root) --force --allow-root
wp post delete $(wp post list --post_status=draft --format=ids --allow-root) --force --allow-root
