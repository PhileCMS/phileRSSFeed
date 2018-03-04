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

        $templateVars = $this->settings;
        $templateVars['pages'] = [] ;

        $pageRespository = new Repository();
        $pages = $pageRespository->findAll();

        for ($i=0; $i < count($pages); $i++) {
            /** @var \Phile\Model\Page $page */
            $page = $pages[$i];
            $meta = $page->getMeta();
            $templateVars['pages'][] = array(
                'title' => $page->getTitle(),
                'url' => $page->getUrl(),
                'content' => $page->getContent(),
                'meta' => $meta,
                'date' => $meta['date']
                );
        }

        $config = Container::getInstance()->get('Phile_Config');
        $templateVars += $config->getTemplateVars();

        function buildSorter($key)
        {
            return function ($a, $b) use ($key) {
                return strnatcmp($b[$key], $a[$key]);
            };
        }

        usort($templateVars['pages'], buildSorter($this->settings['post_key']));
        $content = $this->renderFile('template.php', $templateVars);

        $response = (new \Phile\Core\Response)->createHtmlResponse($content)
            ->withHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $eventData['response'] = $response;
    }

    /**
     * this is a simple function to render a PHP file based on an input array
     *
     * @param string $filename
     * @param array $vars
     * @return string
     */
    private function renderFile($filename, $vars = null)
    {
        if (is_array($vars) && !empty($vars)) {
            extract($vars);
        }
        ob_start();
        include $this->getPluginPath($filename);
        return ob_get_clean();
    }
}
