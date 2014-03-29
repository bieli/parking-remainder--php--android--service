Parking remainder app. based on PHP Slim framework with RESTfull webservice example
===================================================================================

General info
------------
This is experiment with RESTfull light web service created with PHP >= 5.3 programming language.


Specification
-------------



GET /locations
----
$ curl -v -H "Content-Type: application/vnd.parking.remainder-v1.0+json" -H "X-Rest-Api-Key: TEST123" "http://parking-remainder.bieli.net.local/index.php/api/locations"

{"1":"place 12","2":"place 13","3":"place 14","4":"place 15","5":"place 16","6":"place 17","7":"place 18"}


GET /position
----
$ curl -v -H "Content-Type: application/json+v1.0" -H "X-Rest-Api-Key: TEST123" "http://parking-remainder.bieli.net.local/index.php/api/position"


{"locationId":3,"modified":"2014-03-22 01:13:00"}


PUT /position - using payload/body for change position
----

$ curl -v --data '{"locationId" : 5}'e: application/json+v1.0" -H "X-Rest-Api-Key: TEST123" "http://parking-remainder.bieli.net.local/index.php/api/position" -X PUT

{"locationId":"5"}

