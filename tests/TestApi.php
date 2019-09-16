<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\ClickMeetingRestClient;
use App\Hello;

class TestApi extends TestCase {

  protected $apiKey = 'your-api-key';
  protected $api;

  protected function setUp() {
    $this->api = new ClickMeetingRestClient([
      'url' => 'https://api.clickmeeting.com/v1/',
      'api_key' => $this->apiKey,
      'format' => 'json'
    ]);
  }

  /**
   * @test
   */
  public function must_say_hello() {
    $expected = (new Hello())->getMessage();
    $this->assertEquals('hello world!', $expected);
  }

  /**
   * @test
   */
  public function cm_can_be_instanciate() {
    $this->assertInstanceOf(ClickMeetingRestClient::class, $this->api);
  }

  /**
   * @test
   */
  public function cm_can_get_start_date_conference() {
    $roomId = '2502830';
    $result = json_decode($this->api->conference($roomId));
    $actual = $result->conference->starts_at;
    $this->assertEquals($actual, '2019-09-16T17:30:00+02:00');
  }

  /**
   * @test
   */
  public function cm_can_update_start_date_conference() {
    $roomId = '2502830';
    $result = json_decode($this->api->editConference($roomId, ['starts_at' => '2019-09-19 15:30:00']));
    $actual = $result->conference->starts_at;
    // var_dump($actual);
    // fail => the starts_at field is not updated
    $this->assertEquals($actual, '2019-09-19T15:30:00+02:00');
  }

  /**
   * @test
   */
  public function cm_can_get_settings_conference() {
    $roomId = '2502830';
    $result = json_decode($this->api->conference($roomId));
    $this->assertTrue($result->conference->settings->thank_you_emails_enabled);
    $this->assertFalse($result->conference->settings->show_on_personal_page);
    $this->assertFalse($result->conference->settings->connection_tester_enabled);
  }

  /**
   * @test
   */
  public function cm_can_edit_settings_conference() {
    $roomId = '2502830';
    $result = json_decode($this->api->editConference($roomId, ['settings' => [
      'thank_you_emails_enabled' => true,
      'show_on_personal_page' => false,
      'connection_tester_enabled' => true,
      'social_media_sharing_enabled' => true
    ]]));
    $this->assertTrue($result->conference->settings->thank_you_emails_enabled);
    $this->assertFalse($result->conference->settings->show_on_personal_page);
    $this->assertTrue($result->conference->settings->connection_tester_enabled);
    $this->assertObjectHasAttribute('thank_you_emails_enabled', $result->conference->settings);
    $this->assertObjectHasAttribute('show_on_personal_page', $result->conference->settings);
    $this->assertObjectHasAttribute('connection_tester_enabled', $result->conference->settings);
    // Fail
    $this->assertObjectHasAttribute('social_media_sharing_enabled', $result->conference->settings);
    // fail
    // $this->assertTrue($result->conference->settings->social_media_sharing_enabled);
  }

}
