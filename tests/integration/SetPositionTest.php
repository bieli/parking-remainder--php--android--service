<?php

class SetPositionTest extends Slim_Framework_TestCase
{
  public function testShouldSetPosition()
  {
    // given
    $expectedValue = $value = time();
    $parameters = array('locationId' =>  $value);                                                    

    // when
    $this->put(
      '/api/position',
      $parameters,
      array('CONTENT_TYPE' => 'application/x-www-form-urlencoded')
    ); 

    // then
    $this->assertEquals(200, $this->response->status());

    preg_match('~{"locationid":"(\d+)"}~sim', $this->response->body(), $results);
    $this->assertSame($results[0], $this->response->body());
    $this->assertSame((int) $results[1], $expectedValue);
    $this->assertTrue(empty($this->response->header('X-Status-Reason')));
  }

  public function testShouldNotSetPositionWithEmptyPayload()
  {
    // when
    $this->put(
      '/api/position',
      array(),
      array('CONTENT_TYPE' => 'application/x-www-form-urlencoded')
    );

    // then
    $this->assertEquals(400, $this->response->status());
    $this->assertSame('Need "locationId" key with value !', $this->response->header('X-Status-Reason'));
  }

  public function testShouldNotSetPositionWithExistsLocationId()
  {
    // given
    $locationId = 123;

    if (false === file_put_contents(POSITION_DB, $locationId)) {
      throw new Exception('Problem with update position in file!');
    }

    $parameters = array('locationId' => $locationId);                                                    

    // when
    $this->put(
      '/api/position',
      $parameters,
      array('CONTENT_TYPE' => 'application/x-www-form-urlencoded')
    ); 

    // then
    $this->assertEquals(303, $this->response->status());
    $this->assertSame('Value "locationId" already exists !', $this->response->header('X-Status-Reason'));
  }

  public function testShouldNotSetPosition()
  {
    // when
    $this->put('/api/position/other');
    
    // then
    $this->assertEquals(404, $this->response->status());
  }
}

