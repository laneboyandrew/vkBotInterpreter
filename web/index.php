<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));

// Our web handlers

$app->get('/', function () use ($app) {
    return "Hello home";
});

$app->post('/bot', function () use ($app) {
    $data = json_decode(file_get_contents('php://input'));
    if (!$data) {
        return "Missed data";
    }
    if (!$data->secret !== getenv('VK_SECRET_KEY') && $data->type !== 'confirmation')
        return "Getenv or dataType error";

    switch ($data->type) {
        case 'confirmation':
            return getenv('VK_CONFIRAMTION_CODE');
            break;

        case 'message_new':
            $request_params = [
                'user_id' => $data->object->userId,
                'message' => 'тест',
                'access_token' => getenv('VK_TOKEN'),
                'v' => '5.68'
            ];

            file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
            return 'ok';
            break;
    }
    return "Slomalos'!!!";
});

$app->run();
