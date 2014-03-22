<?php

require 'vendor/autoload.php';
require 'vendor/slim/slim/Slim/Slim.php';
require 'vendor/zircote/swagger-php/library/Swagger/Swagger.php';

define('API_KEY_DB', './apikey.db');
define('LOCATIONS_DB', './locations.db');
define('POSITION_DB', './position.db');
define('DATA_DALIMETER', ',');

$app = new \Slim\Slim();

//use Swagger\Swagger;
//use Swagger\Annotations as SWG;

//$swagger = new Swagger('/api/docs');
//header("Content-Type: application/json");
//echo $swagger->getResource('/locations', array('output' => 'json'));

/**
 * @SWG\Resource(
 *     apiVersion="1.0",
 *     swaggerVersion="1.1",
 *     resourcePath="/api",
 *     basePath="http://domain/api"
 * )
 */
$app->group('/api', function () use ($app) {

    $app->response()->header('Content-Type', 'application/vnd.parking.remainder-v1.0+json');

    $apiKey = $app->request->headers->get('X-REST-API-Key');

    /**
     * @SWG\ResponseMessage(code=400, message="Problem with read API KEY from file!")
     */

    /**
     * @SWG\ResponseMessage(code=401, message="Problem with access by API KEY!")
     */

    $appApiKey = file_get_contents(API_KEY_DB);
    if ( false === $appApiKey  ) {
        $app->response()->status(400);
        $app->response()->header('X-Status-Reason', 'Problem with read API KEY from file!');
    } elseif ( trim($apiKey) !== trim($appApiKey) ) {
        $app->response()->status(401);
        $app->response()->header('X-Status-Reason', 'Problem with access by API KEY!');
    }
/*
    $app->get('/docs', function () use ($app) {
      use Swagger\Swagger;

      $swagger = new Swagger('/api/docs');

      header("Content-Type: application/json");

      echo $swagger->getResource('/locations', array('output' => 'json'));
    });
*/

    /**
     * @SWG\Operation(
     *     method="GET", summary="get all locations", notes="Returns a locations based on ID"
     * )
     */
    $app->get('/locations', function () use ($app) {  

      if (empty($app->response->headers->get('X-Status-Reason'))) {
        $locationsData = file_get_contents(LOCATIONS_DB);
        if ( false === $locationsData  ) {
          throw new Exception('Problem with read position from file!');
        }

        $locationsRows = explode("\n", $locationsData);
	        
        $locations = array();

        foreach ($locationsRows as $location) {
          $location = trim($location);

          if ('' !== $location) {   
            $locationRecord = explode(DATA_DALIMETER, $location);
            $locations[ $locationRecord[0] ] = $locationRecord[1];
          } 
        }
        
        echo json_encode($locations);
      }
    });

    /**
     * @SWG\Operation(
     *     method="GET", summary="get current position", notes="Returns a position ID"
     * )
     */
    $app->get('/position', function () use ($app) {    

      if (empty($app->response->headers->get('X-Status-Reason'))) {
        try {
          $positionId = file_get_contents(POSITION_DB);
          if ( false === $positionId  ) {
            throw new Exception('Problem with read position from file!');
          }

          echo json_encode(array('positionId' => (integer) $positionId));
        } catch (Exception $e) {
          $app->response()->status(400);
          $app->response()->header('X-Status-Reason', $e->getMessage());
        }
      }
    });

    /**
     * @SWG\Operation(
     *     method="PUT", summary="change current position", notes="Changes a position ID"
     * )
     */
    $app->put('/position', function () use ($app) {    

      if (empty($app->response->headers->get('X-Status-Reason'))) {
        try {
          $request = $app->request();
          $body = $request->getBody();
          $input = json_decode($body); 

          $locationId = (integer) $input->locationId;

          if ( false === file_put_contents(POSITION_DB, $locationId) ) {
            throw new Exception('Problem with update position in file!');
          }

          echo json_encode(array('locationId' => $locationId));
        } catch (Exception $e) {
          $app->response()->status(400);
          $app->response()->header('X-Status-Reason', $e->getMessage());
        }
      }
    });

});


$app->run();

