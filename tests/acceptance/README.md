### (Optional) Remove previously generated local volumes
```
$ docker volume rm acceptance_mysql-data
$ docker volume rm acceptance_wordpress
```
### Start containers
```
$ docker-compose up -d
```

### Configure site on http://<HOST_IP>:8081
Create a Network with two sites, install plugins if needed.

### Copy testing plugin `hello-cli.php` file to plugins folder and activate it.

### Add database `dump.sql` to `tests/_data` folder
You can use phpMyAdmin at `http://<HOST_IP>:1234` user/pass is `wordpress` 

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
