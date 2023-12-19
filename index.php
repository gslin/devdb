<?php

call_user_func(function () {
    function work()
    {
        # Use localhost:11211 (memcached server) as database.
        $mc = new Memcached();
        $mc->addServer('127.0.0.1', 11211);
        $ttl = 7200;

        $method = $_SERVER['REQUEST_METHOD'];

        # Use IP address as namespace to separate testing environments.
        $ip = $_SERVER['REMOTE_ADDR'];
        $dbkey = 'devdb';

        $uri = $_SERVER['REQUEST_URI'];
        list($id) = sscanf($uri, '/db/%s');

        if (null === $id) {
            if ('GET' === $method) {
                $dbraw = $mc->get($dbkey) ?: '[]';
                $db = json_decode($dbraw, true);

                return [
                    'code' => 200,
                    'data' => $db,
                ];
            }

            if ('POST' === $method) {
                $input = json_decode(file_get_contents('php://input'), true);
                if (null === $input) {
                    return [
                        'code' => 400,
                        'data' => null,
                        '_reason' => 'json_decode() failed',
                    ];
                }

                $dbraw = $mc->get($dbkey) ?: '[]';
                $db = json_decode($dbraw, true);

                $id = (string) random_int(0, PHP_INT_MAX);
                $input['id'] = $id;

                $db[] = $input;
                $dbraw = json_encode($db);

                $mc->set($dbkey, $dbraw, $ttl);

                return [
                    'code' => 200,
                    'data' => $input,
                ];
            }
        } else {
            $dbraw = $mc->get($dbkey) ?: '[]';
            $db = json_decode($dbraw, true);

            if ('DELETE' === $method) {
                foreach ($db as $k => $item) {
                    if ($item['id'] === $id) {
                        unset($db[$k]);
                        $dbraw = json_encode($db);
                        $mc->set($dbkey, $dbraw, $ttl);

                        return [
                            'code' => 200,
                            'data' => $item,
                        ];
                    }
                }
            }

            if ('GET' === $method) {
                foreach ($db as $item) {
                    if ($item['id'] === $id) {
                        return [
                            'code' => 200,
                            'data' => $item,
                        ];
                    }
                }
            }

            if ('PUT' === $method) {
                $input = json_decode(file_get_contents('php://input'), true);
                if (null === $input) {
                    return [
                        'code' => 400,
                        'data' => null,
                        '_reason' => 'json_decode() failed',
                    ];
                }

                foreach ($db as $k => $item) {
                    if ($item['id'] === $id) {
                        $db[$k] = $input;
                        $db[$k]['id'] = $id;
                        $dbraw = json_encode($db);
                        $mc->set($dbkey, $dbraw, $ttl);

                        return [
                            'code' => 200,
                            'data' => $item,
                        ];
                    }
                }
            }

            return [
                'code' => 404,
                'data' => null,
            ];
        }
    }

    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    $ret = work() ?: [];
    echo json_encode($ret);
});
