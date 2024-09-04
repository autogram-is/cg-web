# Cumming Group Web Repository

This git repo holds the custom code and theme assets for the [Cumming Group](https://cumming-group.com) web site. It's designed to support easy testing and development of new features on a developer's local machine using Docker Compose and the official [Wordpress](https://hub.docker.com/_/wordpress)/[MySQL](https://hub.docker.com/_/mysql/) images, with git handling the deployment of new code to a staging or production server.

## Setting up local development

- Install docker
- Optionally import a custom database snapshot
- To start things up, run `docker compose up -d`
- Visit `http://localhost`
- To shut things down, run `docker compose stop`

We'll be updating the `docker-compose.yml` file to add [WP-CLI](https://wp-cli.org) shortly. In addition, we'll have some default Visual Studio config to get things running quickly, though debugging inside the docker container will probably take a bit of experimenting.

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
  - [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/)
  - [WP Force HTTPS](https://wordpress.org/plugins/wp-force-https/)
- Candidates for removal
  - [Avada Core/Builder](https://avada.com) - looking to avoid in-CMS page building; alternative being researched
  - [If-So](https://wordpress.org/plugins/if-so/) - Avoid if possible, punishing impact on performance
  - [Category AJAX Filter](https://trustyplugins.com) - FacetWP may offer better performance with less front end code
  - [WPCode](https://wordpress.org/plugins/insert-headers-and-footers/) - should be handled by the custom theme
  - [The Events Calendar](https://theeventscalendar.com/products/wordpress-events-calendar) - use a simpler listing page rather than a calendar view; event venues and managers aren't needed
  - [HREFLang Tags](https://wordpress.org/plugins/hreflang-tags-by-dcgws/) - deprecated and unsupported
- New plugins
  - [Timber](https://upstatement.com/timber/)
  - [Advanced Custom Fields](https://www.advancedcustomfields.com/)
- Under consideration
  - [Pods Framework](https://pods.io) to replace ACF for table-based storage of field data
    - [Pods Address/Map Field](https://github.com/JoryHogeveen/pods-address-maps)
  - [FacetWP](https://facetwp.com) for faceted search, if deemed necessary
  - [Advanced Views Framework](https://wordpress.org/plugins/acf-views/) - longshot, but may allow quick definition of rule based related content queries

If any of the current site plugins are removed, we'll add notes explaining why, and what's replaced them if applicable.
