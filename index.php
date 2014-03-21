<?php

require 'vendor/autoload.php';
require 'vendor/slim/slim/Slim/Slim.php';


define(LOCATIONS_DB, './locations.db');
define(POSITION_DB, './position.db');


$app = new \Slim\Slim();


$app->group('/api', function () use ($app) {

    $app->response()->header('Content-Type', 'application/vnd.parking.remainder-v1.0+json');

    $app->get('/locations', function () use ($app) {  

      $locationsData = file_get_contents(LOCATIONS_DB);
      if ( false === $locationsData  ) {
        throw new Exception('Problem with read position from file!');
      }

      $locationsRows = explode("\n", $locationsData);
	      
      $locations = array();

      foreach ($locationsRows as $location) {
        $location = trim($location);

        if ('' !== $location) {   
          $locations[] = $location;
        } 
      }
      
      echo json_encode($locations);
    });

    $app->get('/position', function () use ($app) {    
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
    });

    $app->put('/position', function () use ($app) {    
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
    });

});


$app->run();

