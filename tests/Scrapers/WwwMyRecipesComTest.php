<?php

namespace RecipeScraperTests\Scrapers;

use RecipeScraper\Scrapers\TimeInc;
use RecipeScraperTests\ScraperTestCase;

class WwwMyRecipesComTest extends ScraperTestCase
{
    protected function getHost()
    {
        return 'www.myrecipes.com';
    }

    protected function makeScraper()
    {
        return new TimeInc;
    }
}
