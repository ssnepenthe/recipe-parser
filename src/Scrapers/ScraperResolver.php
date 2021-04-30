<?php

namespace RecipeScraper\Scrapers;

use Symfony\Component\DomCrawler\Crawler;

class ScraperResolver implements ScraperResolverInterface
{
    /**
     * @var ScraperInterface[]
     */
    protected $scrapers = [];

    public function __construct(array $scrapers = [])
    {
        foreach ($scrapers as $scraper) {
            $this->add($scraper);
        }
    }

    /**
     * @param ScraperInterface $scraper
     * @return void
     */
    public function add(ScraperInterface $scraper)
    {
        $this->scrapers[] = $scraper;
    }

    /**
     * @param Crawler $crawler
     * @return ScraperInterface|false
     */
    public function resolve(Crawler $crawler)
    {
        foreach ($this->scrapers as $scraper) {
            if ($scraper->supports($crawler)) {
                return $scraper;
            }
        }

        return false;
    }
}
