wp plugin deactivate if-so if-so-geolocation fusion-builder fusion-core category-ajax-filter-pro custom-post-type-ui styles-and-layouts-for-gravity-forms hreflang-tags-by-dcgws redirection regenerate-thumbnails relevanssi the-events-calendar events-calendar-pro blogvault-real-time-backup insert-headers-and-footers duplicate-post wordpress-seo --allow-root
wp plugin activate cg-core cg-migrate advanced-custom-fields-pro admin-columns-for-acf-fields acf-gravityforms-add-on query-monitor query-monitor-twig-profile --allow-root
wp theme activate cumminggroup --allow-root

wp post delete $(wp post list --post_type=revision --format=ids --allow-root) --force --allow-root
wp post delete $(wp post list --post_status=draft --format=ids --allow-root) --force --allow-root
