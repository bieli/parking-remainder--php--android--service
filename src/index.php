<?php

if (!defined('SLIM_TEST_ENV')) {	
  require '../vendor/autoload.php';
  require '../vendor/slim/slim/Slim/Slim.php';
}

if (!defined('DATA_DALIMETER')) {	
	define('API_KEY_DB', '../data/apikey.db');
	define('LOCATIONS_DB', '../data/locations.db');
	define('POSITION_DB', '../data/position.db');
	define('DATA_DALIMETER', ',');
}

date_default_timezone_set('UTC');

if (!defined('SLIM_TEST_ENV')) {
  $app = new \Slim\Slim();
  $app->config('debug', false);
}

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
//TODO: update integration tests with special auth header
    if (!defined('SLIM_TEST_ENV')) {
        $apiKey = $app->request->headers->get('X-REST-API-Key');
    } else {
        $apiKey = 'TEST123';
    }

    /**
     * @SWG\ResponseMessage(code=400, message="Problem with read API KEY from file!")
     */

    /**
     * @SWG\ResponseMessage(code=401, message="Problem with access by API KEY!")
     */
    if (!defined('SLIM_TEST_ENV')) {
      $appApiKey = file_get_contents(API_KEY_DB);
      if ( false === $appApiKey  ) {
          $app->response()->status(400);
          $app->response()->header('X-Status-Reason', 'Problem with read API KEY from file!');
      } elseif ( trim($apiKey) !== trim($appApiKey) ) {
          $app->response()->status(401);
          $app->response()->header('X-Status-Reason', 'Problem with access by API KEY!');
      }
    }
    /**
     * @SWG\Operation(
     *     method="GET", summary="get all locations", notes="Returns a locations based on ID"
     * )
     */
    $app->get('/locations', function () use ($app) {  

      if (empty($app->response->headers->get('X-Status-Reason'))) {
        $locationsData = file_get_contents(LOCATIONS_DB);
        if ( false === $locationsData  ) {
          throw new Exception('Problem with read locations from file!');
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
          $locationId = file_get_contents(POSITION_DB);
          if ( false === $locationId  ) {
            throw new Exception('Problem with read position from file!');
          }

          echo json_encode(array(
            'locationId' => (integer) $locationId,
            'modified' => (string) date("Y-m-d H:i:s", filemtime(POSITION_DB))
          ));
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
          $locationId = $app->request()->params('locationId');
          if (empty(trim($locationId))) {
            throw new Exception('Need "locationId" key with value !');
          }

          $readLocationId = file_get_contents(POSITION_DB);
          if (false === $readLocationId) {
            throw new Exception('Problem with read position from file!');
          }

          if ((int) $readLocationId === (int) $locationId) {
            throw new Exception('Value "locationId" already exists !', 303); 
          } else {
            if (false === file_put_contents(POSITION_DB, $locationId)) {
              throw new Exception('Problem with update position in file!');
            }

            echo json_encode(array('locationId' => $locationId));
          }
        } catch (Exception $e) {
          $app->response()->status(!empty($e->getCode()) ? $e->getCode() : 400);
          $app->response()->header('X-Status-Reason', $e->getMessage());
        }
      }
    });

});

if (!defined('SLIM_TEST_ENV')) {
  $app->run();
}

