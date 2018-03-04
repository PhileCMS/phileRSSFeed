<?php

namespace Phile\Plugin\Phile\RssFeed;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Test\TestCase;

class PluginTest extends TestCase
{
    public function testRssFeed()
    {
        $feedUrl = 'rssFeedHere';
        $config = new Config([
            'plugins' => [
                'phile\\rssFeed' => [
                    'active' => true,
                    'feed_url' => $feedUrl,
                    'post_key' =>'title'
                ]
            ]
        ]);

        $core = $this->createPhileCore(null, $config);
        $request = $this->createServerRequestFromArray(['REQUEST_URI' => '/' . $feedUrl]);
        $response = $core->dispatch($request);

        $this->assertStringStartsWith(
            '<?xml version="1.0" encoding="utf-8"?><rss version="2.0" xmlns:atom',
            (string)$response->getBody()
        );
    }
}
