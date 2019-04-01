### (Optional) Remove previously generated local volumes
```
$ docker volume rm acceptance_mysql-data
$ docker volume rm acceptance_wordpress
```
### Start containers
```
$ docker-compose up -d
```

### Configure site on http://localhost
Create a Network with 3 sites, activate migration plugin, install and activate MLP2 then install MLP3. Current status for tests is MLP2 active, 3 sites with a language assigned (en, es and it) and sites not connected to each other.

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
