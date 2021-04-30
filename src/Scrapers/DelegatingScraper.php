<?php

namespace RecipeScraper\Scrapers;

use Symfony\Component\DomCrawler\Crawler;

class DelegatingScraper implements ScraperInterface
{
    const EMPTY_RECIPE = [
        'author' => null,
        'categories' => null,
        'cookingMethod' => null,
        'cookTime' => null,
        'cuisines' => null,
        'description' => null,
        'image' => null,
        'ingredients' => null,
        'instructions' => null,
        'name' => null,
        'notes' => null,
        'prepTime' => null,
        'publisher' => null,
        'totalTime' => null,
        'url' => null,
        'yield' => null,
        'nutrition' => null
    ];

    /**
     * @var ScraperResolverInterface
     */
    protected $resolver;

    public function __construct(ScraperResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param Crawler $crawler
     * @return array
     */
    public function scrape(Crawler $crawler): array
    {
        if (false === $scraper = $this->resolver->resolve($crawler)) {
            return self::EMPTY_RECIPE;
        }

        return $scraper->scrape($crawler);
    }

    /**
     * @param Crawler $crawler
     * @return boolean
     */
    public function supports(Crawler $crawler): bool
    {
        return false !== $this->resolver->resolve($crawler);
    }
}
