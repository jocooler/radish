<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

//all routes start with API.

// Define app routes
require('routes/customer.php');
require('routes/product.php');
require('routes/report.php');
require('routes/source.php');
require('routes/store.php');
require('routes/transaction.php');
require('routes/user.php');

// Run app
$app->run();



?>
