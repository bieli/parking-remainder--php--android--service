<?php

class GetLocationsTest extends Slim_Framework_TestCase
{
  public function testShouldGetLocations()
  {
    // when
    $this->get('/api/locations');

    // then
    $this->assertEquals(200, $this->response->status());
    $this->assertTrue(10 < strlen($this->response->body()));
    $this->assertTrue(is_array(json_decode($this->response->body(), true)));
    $this->assertTrue(0 < count(json_decode($this->response->body(), true)));
    $this->assertTrue(empty($this->response->header('X-Status-Reason')));
  }

  public function testShouldNotGetLocations()
  {
    // when
    $this->get('/api/locations/other');

    // then
    $this->assertEquals(404, $this->response->status());
  }
}

