<?php

namespace RecipeScraperTests\Commands;

use InvalidArgumentException;
use RecipeScraper\Factory;
use RecipeScraperTests\UsesTestData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\VarExporter\VarExporter;

class ResultsUpdateCommand extends Command
{
    use UsesTestData;

    protected function configure()
    {
        $this->setName('results:update')
            ->setDescription('Update result files with content from its corresponding HTML file')
            ->addArgument(
                'fields',
                InputArgument::REQUIRED,
                'A comma separated list of result fields to update'
            )->addArgument('url', InputArgument::REQUIRED, 'Recipe URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $fieldsStr = $input->getArgument('fields');
        $fields = explode(',', $fieldsStr);
        // @todo check if fields exist in stub

        $htmlFile = $this->getHtmlDataFilePathFromUrl($url);

        if (!file_exists($htmlFile)) {
            throw new InvalidArgumentException(sprintf(
                'HTML file [%s] for provided URL [%s] does not exist, please run html:get command firstly',
                $htmlFile,
                $url
            ));
        }

        $resultFile = $this->getResultsDataFilePathFromUrl($url);

        if (!file_exists($resultFile)) {
            // @todo Prompt user to allow overwriting file?
            (new SymfonyStyle($input, $output))->error(sprintf(
                'Results file [%s] for provided URL [%s] does not exist',
                $resultFile,
                $url
            ));
            return 1;
        }

        $currentResultFileContent = (array) static::includeFile($resultFile);

        $scraper = Factory::make();
        $crawler = new Crawler($this->getHtml($url));
        $newHtmlResult = $scraper->scrape($crawler);

        foreach ($fields as $field) {
            if (key_exists($field, $newHtmlResult)) {
                $currentResultFileContent[$field] = $newHtmlResult[$field];
            }
        }

        $newResultContentString = "<?php\n\nreturn " . VarExporter::export($currentResultFileContent) . ";\n";
        file_put_contents($resultFile, $newResultContentString);
    }
}
