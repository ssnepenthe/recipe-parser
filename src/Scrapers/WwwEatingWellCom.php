<?php

namespace RecipeScraper\Scrapers;

use Symfony\Component\DomCrawler\Crawler;

class WwwEatingWellCom extends SchemaOrgMarkup
{
    /**
     * @param  Crawler $crawler
     * @return boolean
     */
    public function supports(Crawler $crawler) : bool
    {
        return parent::supports($crawler)
            && 'www.eatingwell.com' === parse_url($crawler->getUri(), PHP_URL_HOST);
    }

    /**
     * @param  Crawler $crawler
     * @return string|null
     */
    protected function extractAuthor(Crawler $crawler)
    {
        return $this->extractString($crawler, '[itemprop="author"] [itemprop="name"]');
    }

    /**
     * @param  Crawler $crawler
     * @return string[]|null
     */
    protected function extractCategories(Crawler $crawler)
    {
        // Maybe not all appropriate - they are all nutrition-related categories.
        return $this->extractArray($crawler, '.nutritionTag');
    }

    /**
     * @param  Crawler $crawler
     * @return string|null
     */
    protected function extractDescription(Crawler $crawler)
    {
        // Look for a direct descendent of the recipe to avoid capturing description from video.
        return $this->extractString(
            $crawler,
            '[itemtype*="schema.org/Recipe"] > [itemprop="description"]',
            ['content']
        );
    }

    /**
     * @param  Crawler $crawler
     * @return string|null
     */
    protected function extractImage(Crawler $crawler)
    {
        // Look for a direct descendant of the recipe to avoid capturing image from video element.
        return $this->extractString(
            $crawler,
            '[itemtype*="schema.org/Recipe"] > [itemprop="image"]',
            ['content']
        );
    }

    /**
     * @param  Crawler $crawler
     * @return string[]|null
     */
    protected function extractInstructions(Crawler $crawler)
    {
        return $this->extractArray($crawler, '[itemprop="recipeInstructions"] li');
    }

    protected function extractName(Crawler $crawler)
    {
        return $this->extractString($crawler, '.recipeDetailHeader');
    }

    /**
     * @param  Crawler $crawler
     * @return string[]|null
     */
    protected function extractNotes(Crawler $crawler)
    {
        // @todo More tests! On allrecipes.com we are filtering out headers... I haven't seen any
        // headers on eatingwell.com which is basically the same template. Should we do it anyway?
        return $this->extractArray($crawler, '.recipeFootnotes li');
    }

    /**
     * @param  Crawler $crawler
     * @return string|null
     */
    protected function extractUrl(Crawler $crawler)
    {
        return $this->extractString($crawler, '[rel="canonical"]', ['href']);
    }
}
