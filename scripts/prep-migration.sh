wp plugin deactivate if-so if-so-geolocation ifso-audience-self-selection-form-extension category-ajax-filter-pro custom-post-type-ui styles-and-layouts-for-gravity-forms events-calendar-pro fusion-builder fusion-core buzzsprout-podcasting custom-post-type-ui the-events-calendar --allow-root 
wp plugin activate cg-core cg-migrate advanced-custom-fields-pro admin-columns-for-acf-fields acf-gravityforms-add-on --allow-root
wp theme activate cumminggroup --allow-root

wp post delete $(wp post list --post_type=revision --format=ids --allow-root) --force --allow-root
wp post delete $(wp post list --post_status=draft --format=ids --allow-root) --force --allow-root
