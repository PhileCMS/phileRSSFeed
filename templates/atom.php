<?= '<?xml version="1.0" encoding="utf-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?= $feed_title ?></title>
        <link><?= $base_url ?></link>
        <description><?= $feed_description ?></description>
        <atom:link href="<?= $feed_url ?>" rel="self" type="application/rss+xml" />
        <?php foreach ($pages as $page) : ?>
            <item>
                <title><?= $page['title'] ?></title>
                <description><![CDATA[<?= $page['content'] ?>]]></description>
                <?php if (!empty($page['date'])) : ?>
                    <pubDate><?= date("D, d M Y H:i:s O", strtotime($page['date'])) ?></pubDate>
                <?php endif; ?>
                <link><?= $page['url'] ?></link>
                <guid isPermaLink="true"><?= $page['url'] ?></guid>
            </item>
        <?php endforeach ?>
    </channel>
</rss>