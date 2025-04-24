# Cumming Group Web Repository

This git repo holds the custom code and theme assets for the [Cumming Group](https://cumming-group.com) web site. It's designed to support reliable testing and development of new features on a developer's local machine using Docker Compose and the official [Wordpress](https://hub.docker.com/_/wordpress)/[MySQL](https://hub.docker.com/_/mysql/) images.

## Setting up local development

The local development process is built on docker and node.js; installers can be downloaded from the [NodeJS](https://nodejs.org/en/download) and [Docker](https://docs.docker.com/engine/install/) home pages.

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

## Custom and third-party plugins

While the final version of the Cumming Group site may include additional third-party plugins added and configured by the Cumming Group team, the following plugins have been configured and tested with the new theme and content.

  - CG Core: Custom plugin containing saved definitions of all Cumming Group custom post types, custom taxonomies, and custom fields. This prevents custom post type and field data from being lost if the Cumming Group theme is changed or disabled.
  - [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/) - Defines custom post types, taxonomies, and fields.
  - [ACF Gravity Forms](https://wordpress.org/plugins/acf-gravityforms-add-on/) - 
  - [ACF Quickedit Fields](https://wordpress.org/plugins/acf-quickedit-fields/) - Adds Gravity Form fields to WP admin pages.
  - [Gravity Forms](https://www.gravityforms.com) - user submittable contact and feedback forms.
  - [Gravity Forms CLI](https://www.gravityforms.com) - allows bulk backup and management of forms from the WP CLI.
  - [Yoast SEO](https://developer.yoast.com) - API-accessible SEO tag and social sharing metadata generation.
  - [Object Cache Pro](https://objectcache.pro) - Redis cache integration, license included with Cloudways hosting.
  - [Query Monitor](https://wordpress.org/plugins/query-monitor/) - Administrator-only tool for troubleshooting and testing.

## Deploying local changes to staging

Deployment to Cloudways is conducted via SFTP to the staging server; the Cumming Group custom theme and support code is located in:

- `public_html/wp-content/plugins/cg-core`
- `public_html/wp-content/themes/cumminggroup`

Deployment consists of copying those two directories to their respective locations on the staging server.

## Deploying a CloudWays staging server to Production

The [CloudWays support web knowledgebase](https://support.cloudways.com/en/collections/3185991-deployment-and-staging-management) documents the process of setting up a staging server, managing content and code on that staging server, and moving its code and content to the live server.

- We *strongly* recommend performing a backup of the CloudWays production server before moving the staging server to production; this ensures that if anything breaks, the previous version can be restored.
- Activate Object Cache Pro to improve Wordpress' performance. Instructions for enabling this plugin are specific to Cloudways, and can be found on [the Cloudways Support Knowledgebase](https://support.cloudways.com/en/articles/5723061-speed-up-your-wordpress-application-using-object-cache-pro)
- Ensure the Yoast SEO plugin is activated
- Using the CloudWays control panel, initiate the migration from staging to production.

