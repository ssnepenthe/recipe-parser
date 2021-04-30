<?php

namespace RecipeScraper\Scrapers;

use Symfony\Component\DomCrawler\Crawler;

/**
 * This site is powered by WordPress but at the time of this writing the recipe post
 * type is hidden from the REST API.
 *
 * Many recipes that include notes bundle them into the instructions section.
 */
class SpryLivingCom extends SchemaOrgMarkup
{
    /**
     * @param  Crawler $crawler
     * @return boolean
     */
    public function supports(Crawler $crawler) : bool
    {
        return parent::supports($crawler)
            && 'spryliving.com' === parse_url($crawler->getUri(), PHP_URL_HOST);
    }

    /**
     * @param  Crawler $crawler
     * @return string[]|null
     */
    protected function extractCategories(Crawler $crawler)
    {
        return $this->extractArray($crawler, 'a[href*="recipes/category/"]');
    }

    /**
     * @param  Crawler $crawler
     * @return string|null
     */
    protected function extractDescription(Crawler $crawler)
    {
        return $this->extractString($crawler, '[name="description"]', ['content']);
    }

    /**
     * @param  Crawler $crawler
     * @return string|null
     */
    protected function extractImage(Crawler $crawler)
    {
        return $this->extractString($crawler, '.main-image > img', ['data-lazy-src']);
    }

    /**
     * @param  Crawler $crawler
     * @return string[]|null
     */
    protected function extractIngredients(Crawler $crawler)
    {
        return $this->extractArrayFromChildren(
            $crawler,
            '[itemprop="ingredients"], .ingredients dt'
        );
    }

    /**
     * @param  Crawler $crawler
     * @return string[]|null
     */
    protected function extractInstructions(Crawler $crawler)
    {
        $selectors = [
            '[itemprop="recipeInstructions"] h4',
            '[itemprop="recipeInstructions"] li',
            '[itemprop="recipeInstructions"] strong',
        ];

        return $this->extractArrayFromChildren($crawler, implode(', ', $selectors));
    }

    /**
     * @param  Crawler $crawler
     * @return string[]|null
     */
    protected function extractNotes(Crawler $crawler)
    {
        // @todo More testing!
        return $this->extractArray(
            $crawler,
            '[itemprop="recipeInstructions"] > div:not(.wp-caption)'
        );
    }

    /**
     * @param  Crawler $crawler
     * @return array|null
     */
    protected function extractNutrition(Crawler $crawler)
    {
        // @todo Find a solution, miss-formatted DOM element property, itemprop=""calories""
        return parent::extractNutrition($crawler);
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
