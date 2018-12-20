<?php
/*
 * Copyright (C) 2017-present, Facebook, Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace FacebookPixelPlugin\Tests\Integration;

use FacebookPixelPlugin\Integration\FacebookWordpressContactForm7;
use FacebookPixelPlugin\Tests\FacebookWordpressTestBase;

final class FacebookWordpressContactForm7Test extends FacebookWordpressTestBase {
  public function testInjectPixelCode() {
    \WP_Mock::expectActionAdded('wpcf7_contact_form',
      array(FacebookWordpressContactForm7::class, 'injectLeadEventHook'), 11);
    FacebookWordpressContactForm7::injectPixelCode();
    $this->assertHooksAdded();
  }

  public function testInjectLeadEventHook() {
    \WP_Mock::expectActionAdded('wp_footer',
      array(FacebookWordpressContactForm7::class, 'injectLeadEvent'),
      11);
    FacebookWordpressContactForm7::injectLeadEventHook();
    $this->assertHooksAdded();
  }

  public function testInjectLeadEventWithoutAdmin() {
    self::mockIsAdmin(false);

    $mocked_fbpixel = \Mockery::mock('alias:FacebookPixelPlugin\Core\FacebookPixel');
    $mocked_fbpixel->shouldReceive('getPixelLeadCode')
      ->with(array(), FacebookWordpressContactForm7::TRACKING_NAME, false)
      ->andReturn('contact-form-7');
    FacebookWordpressContactForm7::injectLeadEvent();
    $this->expectOutputRegex('/contact-form-7/');
    $this->expectOutputRegex('/wpcf7submit/');
  }

  public function testInjectLeadEventWithAdmin() {
    self::mockIsAdmin(true);

    FacebookWordpressContactForm7::injectLeadEvent();
    $this->expectOutputString("");
  }
}