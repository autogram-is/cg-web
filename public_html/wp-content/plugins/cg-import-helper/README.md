# Wordpress Import Workflow

Wordpress's standard post import tool provides a number of useful filter and action hooks. After parsing the incoming export file, it handles categories, then tags, then posts, then metadata, then attachments.

They hooks fire in the following order:

- `wp_import_categories` (list of categories)
- `wp_import_tags` (list of tags)
- `wp_import_terms` (list of terms)
  - `wp_import_term_meta` (metadata for an individual term)
  - `import_term_meta_key` (remap/ignore specific meta properties)
- `wp_import_posts` (list of posts)
  - `wp_import_post_data_raw` (raw import values for one post)
  - `wp_import_existing_post` (id and post of existing posts; return 0 to force import)
  - `wp_import_post_data_processed` (processed post, ready for insertion)
  - `wp_import_post_terms` (list of terms the post is tagged with)
  - `wp_import_post_comments` (comments for each post)
  - `wp_import_post_meta` (meta properties for post)
  - `import_post_meta_key` (remap/ignore specific meta properties)

In addition, the following filters can be used to disable parts of the process:

- `import_allow_create_users` (defaults to true)
- `import_allow_fetch_attachments` (defaults to true)
- `import_attachment_size_limit` (defaults to 0; ie no limit)

Finally, `global $wp_import` gives access to the active WP_Import instance, with the raw unprocessed data and other goodies.
