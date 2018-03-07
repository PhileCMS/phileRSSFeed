<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\RssFeed;

use Phile\Core\Container;
use Phile\Gateway\EventObserverInterface;
use Phile\Plugin\AbstractPlugin;
use Phile\Repository\Page as Repository;

/**
 * Phile RSS Feed Plugin
 * converted from https://github.com/gilbitron/Pico-RSS-Plugin
 */
class Plugin extends AbstractPlugin implements EventObserverInterface
{
    protected $events = ['request_uri' => 'createFeed'];

    public function createFeed($eventData)
    {
        $feedUrl = ltrim($this->settings['feed_url'], '/');
        if ($eventData['uri'] != $feedUrl) {
            return;
        }

        $router = Container::getInstance()->get('Phile_Router');
        $config = Container::getInstance()->get('Phile_Config');

        $templateVars = $this->settings + $config->getTemplateVars();
        $templateVars += [
            'feed_url' => $router->urlForPage($feedUrl),
            'pages' => $this->getPages()
        ];

        $content = $this->renderFile('templates/atom.php', $templateVars);

        $response = (new \Phile\Core\Response)
            ->createHtmlResponse($content)
            ->withHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $eventData['response'] = $response;
    }

    private function getPages(): array
    {
        $pageRespository = new Repository();
        $allPages = $pageRespository->findAll();

        $pages = [];
        foreach ($allPages as $page) {
            $meta = $page->getMeta();
            if (empty($meta[$this->settings['post_key']])) {
                continue;
            }
            $pages[] = [
                'title' => $page->getTitle(),
                'url' => $page->getUrl(),
                'content' => $page->getContent(),
                'date' => $meta->get('date'),
                'meta' => $meta->getAll()
            ];
        }
        $sortKey = $this->settings['sort_key'];
        $sorter = function ($a, $b) use ($sortKey) {
            return strnatcmp($b[$sortKey], $a[$sortKey]);
        };
        usort($pages, $sorter);
        return $pages;
    }

    /**
     * this is a simple function to render a PHP file based on an input array
     *
     * @param string $filename
     * @param array $vars
     * @return string
     */
    private function renderFile(string $filename, array $vars = []): string
    {
        extract($vars);
        ob_start();
        include $this->getPluginPath($filename);
        return ob_get_clean();
    }
}
