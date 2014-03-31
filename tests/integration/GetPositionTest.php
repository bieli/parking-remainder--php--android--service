<?php

class GetPositionTest extends Slim_Framework_TestCase
{
  public function testShouldGetLocationIdAndModifiedDateTime()
  {
    // when
    $this->get('/api/position');
    
    // then
    $this->assertEquals(200, $this->response->status());
    preg_match(
      '~{"locationId":(\d+),"modified":"(\d\d\d\d[-]\d\d[-]\d\d \d\d[:]\d\d[:]\d\d)"}~',
      $this->response->body(),
      $results        
    );
    $this->assertSame($results[0], $this->response->body());
    $this->assertTrue(0 < (int) $results[1]);
    $this->assertTrue(0 < strtotime($results[2]));
    $this->assertTrue(empty($this->response->header('X-Status-Reason')));
  }

  public function testShouldNotGetLocationIdAndModifiedDateTime()
  {
    // when
    $this->get('/api/position/other');
    
    // then
    $this->assertEquals(404, $this->response->status());
  }
}

