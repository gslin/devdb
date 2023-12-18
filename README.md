# devdb

A simple CRUD interface with memcached.

## Installation

You would need to specify nginx settings:

    index index.php index.html;
    try_files $uri $uri/ /index.php?$args;
