# devdb

A simple CRUD interface with memcached.

## Installation

You need to set up a memcached server in `localhost:11211`, then create a virtual host and specify nginx settings:

    index index.php index.html;
    try_files $uri $uri/ /index.php?$args;

Put `index.php` into the virtual host document root.

## License

See [LICENSE](LICENSE).
