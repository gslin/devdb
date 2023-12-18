# devdb

A simple CRUD interface with memcached.

## Installation

You need to set up a memcached server in `localhost:11211`, then create a virtual host and specify nginx settings:

    index index.php index.html;
    try_files $uri $uri/ /index.php?$args;

Put `index.php` into the virtual host document root.

## Usage

CRUD operations with curl:

    curl https://devdb.example.com/
    curl -X POST -H 'Content-Type: application/json' --data '{"a":"b"}' https://devdb.example.com/
    curl -X DELETE https://devdb.example.com/1234567890123456789
    curl -X PUT -H 'Content-Type: application/json' --data '{"a":"b"}' https://devdb.example.com/1234567890123456789

## License

See [LICENSE](LICENSE).
