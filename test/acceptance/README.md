### Start containers
```
$ docker-compose up -d
```

### Configure site on http://localhost
- Convert single site to Multisite, install and activate MLP2 (from wp.org), activate migration plugin, then install MLP3.
- With MLP2 active create 2 sites more, language assigned should be: `en`, `es` and `it`. Sites are not connected to each other.
- Copy migration plugin to `wordpress-site` plugins folder (better use a version without dev dependencies).
- Copy `fake-post-type` to `wordpress-site` to plugins folder, activate it.
- Install and activate Classic Editor.

### Add database `dump.sql` to `tests/_data` folder
You can use phpMyAdmin at `http://localhost:1234` user/pass is `wordpress` 

### Edit `wp-config.php` in `wordpress-site`
Replace `<HOST_IP>` for your host ip.
```php
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    define( 'DB_HOST', '<HOST_IP>:8082' );
} else {
    define( 'DB_HOST', 'mysql' );
}
```

### Run tests
`$ ../../vendor/bin/codecept run`
