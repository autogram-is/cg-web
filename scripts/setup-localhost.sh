wp user create admin localhost@autogram.is --role=administrator --allow-root
wp user update admin --user_pass="admin" --skip-email --allow-root

wp option set siteurl http://localhost --allow-root
wp option set home http://localhost --allow-root
wp search-replace https://wordpress-243686-4130740.cloudwaysapps.com http://localhost --skip-columns=guid --allow-root

wp cache flush --allow-root