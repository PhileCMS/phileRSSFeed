<?php
/**
 * config file
 */

use Phile\Core\Container;

$global = Container::getInstance()->get('Phile_Config');

$config = [];

/*
 * RSS-feed-URL relative to base-URL
 */
$config ['feed_url'] = 'feed';

/**
 * Only pages with this attribute are included in the feed.
 */
$config['post_key'] = 'date';

/**
 * Pages are sorted by this page attribute
 */
$config['sort_key'] = $config['post_key'];

/**
 * RSS feed title
 */
$config['feed_title'] = $global->get('site_title');

/**
 * RSS-feed description
 */
$config['feed_description'] = 'RSS-feed for ' . $global->get('site_title');

return $config;
