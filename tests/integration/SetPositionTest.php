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
    $this->get('/api/position');
    
    $this->assertEquals(200, $this->response->status());
    preg_match(
      '~{"locationId":(\d+),"modified":"(\d\d\d\d[-]\d\d[-]\d\d \d\d[:]\d\d[:]\d\d)"}~',
      $this->response->body(),
      $results        
    );

    $this->assertTrue(0 < $results[1]);

    // given
    $parameters = array('locationId' => (int) $results[1]);                                                    

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

