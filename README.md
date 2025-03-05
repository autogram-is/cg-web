# Cumming Group Web Repository

This git repo holds the custom code and theme assets for the [Cumming Group](https://cumming-group.com) web site. It's designed to support easy testing and development of new features on a developer's local machine using Docker Compose and the official [Wordpress](https://hub.docker.com/_/wordpress)/[MySQL](https://hub.docker.com/_/mysql/) images, with git handling the deployment of new code to a staging or production server.

## Setting up local development

- Install docker
- Optionally import a custom database snapshot
- To start things up, run `docker compose up -d`
- Visit `http://localhost`
- To log into the docker container and noodle around on the command line, use `docker compose exec wordpress bash`
- To *out* of the docker container's command line, use `exit`
- To shut things down, run `docker compose down`

The `upload` directory's contents from the live or staging site should be copied to `public_html/wp-content/uploads` to ensure local image attachments etc work. The `.gitignore` file for this project will ensure it's not committed to the repository.

Similarly, backup snapshots of the site database can be placed in `public_html/wp-content/backups` and loaded using the `wp import` command. A top-level shortcut `npm wordpress:loadsql` can be run without entering the Docker command line, and will immediately import the `db.sql` backup file if it exists.

## Deploying to staging

Deployment to Cloudways is conducted via SFTP to the staging server; in particular the `public_html/wp-content/themes/cumminggroup` and `public_html/wp-content/plugins/cg-core` are critical. During development, the `public_html/wp-content/plugins/cg-migrate` plugin is also used to perform automated migration and bulk content fixes but can be disabled and/or removed before final deployment. 

## Theme / Plugin / Code inventory

This list is in progress -- it's subject to change as the initial configuration plan is tested and validated against the site requirements.

- Existing site plugins
  - [Gravity Forms](https://www.gravityforms.com)
  - [PostSMTP](https://postmansmtp.com)
  - [HSTS-Ready](https://wordpress.org/plugins/hsts-ready/)
  - [Relevanssi](https://wordpress.org/plugins/relevanssi/)
  - [Redirection](https://wordpress.org/plugins/redirection/)
  - [WP Force HTTPS](https://wordpress.org/plugins/wp-force-https/)
  - [Yoast SEO](https://developer.yoast.com) - API-accessible SEO tag and social sharing metadata generation.
  - [Object Cache Pro](https://objectcache.pro) - Redis cache integration, license included with Cloudways hosting
- Removed/Disabled
  - [If-So](https://wordpress.org/plugins/if-so/) - Useful geolocation plugins and related tools, but we want to keep as much of this as possible out regular page-loading.
  - [WP Rocket](https://wp-rocket.me)
  - [BuzzSprout Podcasting](https://wordpress.org/plugins/buzzsprout-podcasting/)
  - [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) - duplicated by WP-CLI
  - [Avada Core/Builder](https://avada.com) - looking to avoid in-CMS page building; alternative being researched
  - [Category AJAX Filter](https://trustyplugins.com) - FacetWP may offer better performance with less front end code
  - [WPCode](https://wordpress.org/plugins/insert-headers-and-footers/) - should be handled by the custom theme
  - [The Events Calendar](https://theeventscalendar.com/products/wordpress-events-calendar) - Replaced by a custom post type and a listing page
  - [HREFLang Tags](https://wordpress.org/plugins/hreflang-tags-by-dcgws/) - deprecated and unsupported
- New plugins
  - [Advanced Custom Fields](https://www.advancedcustomfields.com/)
  - [ACF Gravity Forms](acf-gravityforms-add-on)
- Potential future plugins
  - [hCaptcha for WP](https://wordpress.org/plugins/hcaptcha-for-forms-and-more/)
