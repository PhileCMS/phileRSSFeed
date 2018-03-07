<?php

namespace Phile\Plugin\Phile\RssFeed;

use Phile\Core\Config;
use Phile\Test\TestCase;

class PluginTest extends TestCase
{
    public function testRssFeed()
    {
        $config = new Config([
            'plugins' => [
                'phile\\rssFeed' => [
                    'active' => true,
                    'feed_url' => 'url/feed.rss',
                    'post_key' => 'title',
                    'feed_title' => 'F6',
                    'feed_description' => '81',
                ]
            ]
        ]);

        $core = $this->createPhileCore(null, $config);
        $request = $this->createServerRequestFromArray(['REQUEST_URI' => '/url/feed.rss']);
        $response = $this->createPhileResponse($core, $request);
        $body = (string)$response->getBody();

        $this->assertStringStartsWith(
            '<?xml version="1.0" encoding="utf-8"?><rss version="2.0" xmlns:atom',
            $body
        );
        $this->assertContains('<title>F6</title>', $body);
        $this->assertContains('<description>81</description>', $body);
    }
}
