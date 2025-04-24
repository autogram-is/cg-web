# Cumming Group Web Repository

This git repo holds the custom code and theme assets for the [Cumming Group](https://cumming-group.com) web site. It's designed to support reliable testing and development of new features on a developer's local machine using Docker Compose and the official [Wordpress](https://hub.docker.com/_/wordpress)/[MySQL](https://hub.docker.com/_/mysql/) images.

## Setting up local development

This process is built on docker and node.js; installers can be downloaded from the [NodeJS](https://nodejs.org/en/download) and [Docker](https://docs.docker.com/engine/install/) home pages.

### Running the Cumming Group component library locally

- To start a local copy of the component library, run `npm start`
- Visit `http://localhost:8080`

Running the `start` command compiles the design's CSS, JS, Fonts, and other static assets for use in Wordpress, then copies those files to the `public_html/wp-content/themes/cumminggroup/assets/` directory.

### Running Wordpress locally

- To start the local wordpress server, run `npm wordpress:start`
- Visit `http://localhost`
- To log into wordpress docker container (to run WP CLI commands, for example), use `docker compose exec wordpress bash`
- To exit the docker container's command line, use `exit`
- To shut down the local wordpress server, run `npm wordpress:stop`

The `upload` directory's contents from the live or staging site should be copied to `public_html/wp-content/uploads` to ensure local image attachments etc work. The `.gitignore` file for this project will ensure it's not committed to the repository.

Similarly, backup snapshots of the site database can be placed in `public_html/wp-content/backups` and loaded using the `wp import` command. A top-level shortcut `npm wordpress:loadsql` can be run without entering the Docker command line, and will immediately import the `db.sql` backup file if it exists.

## Deploying to staging

Deployment to Cloudways is conducted via SFTP to the staging server; copying the complete `public_html/wp-content/themes/cumminggroup` and `public_html/wp-content/plugins/cg-core` directories to their respective locations on the staging server will . During development, the `public_html/wp-content/plugins/cg-migrate` plugin is also used to perform automated migration and bulk content fixes but can be disabled and/or removed before final deployment.

## Wordpress Theme & Plugins

While the final version of the Cumming Group site may include additional third-party plugins added and configured by the Cumming Group team, the following plugins have been configured and tested by the 

### Cumming Group custom code

  - CG Core: 
  - CG Migrate: 

### Third-party plugins

  - [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/) 
  - [ACF Gravity Forms](https://wordpress.org/plugins/acf-gravityforms-add-on/)
  - [ACF Quickedit Fields](https://wordpress.org/plugins/acf-quickedit-fields/)
  - [Gravity Forms](https://www.gravityforms.com)
  - [Gravity Forms CLI](https://www.gravityforms.com)
  - [Yoast SEO](https://developer.yoast.com) - API-accessible SEO tag and social sharing metadata generation.
  - [Object Cache Pro](https://objectcache.pro) - Redis cache integration, license included with Cloudways hosting
