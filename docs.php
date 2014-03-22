<?php

require 'vendor/autoload.php';
require 'vendor/slim/slim/Slim/Slim.php';
require 'vendor/zircote/swagger-php/library/Swagger/Swagger.php';

define('API_KEY_DB', './apikey.db');
define('LOCATIONS_DB', './locations.db');
define('POSITION_DB', './position.db');


$app = new \Slim\Slim();

use Swagger\Swagger;
use Swagger\Annotations as SWG;


$swagger = new Swagger('//home/test/Pulpit/projects/parking-reminder/rest-server');
header("Content-Type: application/json");
echo $swagger->getResource('/api', array('output' => 'json'));

