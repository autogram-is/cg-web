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

- New plugins
  - [Timber](https://upstatement.com/timber/)
  - [Advanced Custom Fields](https://www.advancedcustomfields.com/) (may transition to [Pods Framework](https://pods.io), pending performance tests)
- Existing plugins
  - [BuzzSprout Podcasting](https://wordpress.org/plugins/buzzsprout-podcasting/)
  - [Gravity Forms](https://www.gravityforms.com)
  - https://postmansmtp.com
  - https://wordpress.org/plugins/performance-lab/
  - https://wordpress.org/plugins/hsts-ready/
  - https://wordpress.org/plugins/relevanssi/