#!/bin/sh

SERVICE_URL=http://parking-remainder.bieli.net.local/index.php

curl --header "X-REST-API-Key: TEST123" -v $SERVICE_URL/api/locations
curl --header "X-REST-API-Key: TEST123" -v $SERVICE_URL/api/position

