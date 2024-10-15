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

A copy of the `upload` directory's contents, to ensure old contents' image and media links still work, [is on Dropbox](https://www.dropbox.com/s/cjtdaulpldwx7b4/wp-uploads.zip?dl=0). The `.gitignore` file for this project will ensure it's not committed if you copy its contents to `public_html/wp-content/uploads`.

## Deploying to staging

This section will be updated with details based on the Cloudways configuration and deployment details. The most important detail is ensuring that any necessary migration code (for example, creating a storage table for field data if Pods is set to use dedicated tables) are run on the staging server (and eventually the production server).

Changes that rely on custom pieces of content or other non-code data will require a different deployment path, or can be created on the staging server directly.

## Theme / Plugin / Code inventory

This list is in progress -- it's subject to change as the initial configuration plan is tested and validated against the site requirements.

- Existing site plugins
  - [BuzzSprout Podcasting](https://wordpress.org/plugins/buzzsprout-podcasting/)
  - [Gravity Forms](https://www.gravityforms.com)
  - [PostSMTP](https://postmansmtp.com)
  - [HSTS-Ready](https://wordpress.org/plugins/hsts-ready/)
  - [Relevanssi](https://wordpress.org/plugins/relevanssi/)
  - [WP Rocket](https://wp-rocket.me)
  - [Redirection](https://wordpress.org/plugins/redirection/)
  - [WP Force HTTPS](https://wordpress.org/plugins/wp-force-https/)
  - [If-So](https://wordpress.org/plugins/if-so/) - Useful geolocation plugins and related tools, but we want to keep as much of this as possible out regular page-loading.
  - [Yoast SEO](https://developer.yoast.com) - API-accessible SEO tag and social sharing metadata generation.
  - [Object Cache Pro](https://objectcache.pro) - Redis cache integration, license included with Cloudways hosting
- Candidates for removal
  - [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) - duplicated by WP-CLI
  - [Avada Core/Builder](https://avada.com) - looking to avoid in-CMS page building; alternative being researched
  - [Category AJAX Filter](https://trustyplugins.com) - FacetWP may offer better performance with less front end code
  - [WPCode](https://wordpress.org/plugins/insert-headers-and-footers/) - should be handled by the custom theme
  - [The Events Calendar](https://theeventscalendar.com/products/wordpress-events-calendar) - use a simpler listing page rather than a calendar view; event venues and managers aren't needed
  - [HREFLang Tags](https://wordpress.org/plugins/hreflang-tags-by-dcgws/) - deprecated and unsupported
- New plugins
  - [Timber](https://upstatement.com/timber/)
  - [Advanced Custom Fields](https://www.advancedcustomfields.com/)
  - [ACF Gravity Forms](acf-gravityforms-add-on)
  - [Timber ACF Blocks](https://github.com/palmiak/timber-acf-wp-blocks/), used to integrate theme components with block editor
- Under consideration
  - [FacetWP](https://facetwp.com) for faceted search, if deemed necessary

If any of the current site plugins are removed, we'll add notes explaining why, and what's replaced them if applicable.
