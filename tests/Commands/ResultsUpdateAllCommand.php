<?php

namespace RecipeScraperTests\Commands;

use InvalidArgumentException;
use RecipeScraper\Factory;
use RecipeScraperTests\UsesTestData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\VarExporter\VarExporter;

class ResultsUpdateAllCommand extends Command
{
    use UsesTestData;

    protected function configure()
    {
        $this->setName('results:update-all')
            ->setDescription('Update all result files with content from their corresponding HTML file')
            ->addArgument(
                'fields',
                InputArgument::REQUIRED,
                'A comma separated list of result fields to update'
            )->addArgument(
                'host',
                InputArgument::REQUIRED,
                'Host to update results for, use all for all hosts'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getArgument('host');

        $urls = [];
        if ($host !== 'all') {
            $file = $this->getUrlsDataFilePath($host);

            if (! file_exists($file)) {
                throw new \InvalidArgumentException(sprintf(
                    'URLs file [%s] for provided host [%s] does not exist',
                    $file,
                    $host
                ));
            }
            $urls = (array) static::includeFile($file);
        } else {
            $urls = $this->getTestUrls();
        }

        shuffle($urls);

        $io = new SymfonyStyle($input, $output);

        $io->progressStart(count($urls));

        $resultUpdateCommand = $this->getApplication()->find('results:update');

        $fieldsParam = $input->getArgument('fields');
        // @todo check if fields exist in stub

        foreach ($urls as $url) {
            $arguments = [
                'fields' => $fieldsParam,
                'url' => $url
            ];

            $resultUpdateInput = new ArrayInput($arguments);

            // @todo Error handling base on return code?
            $returnCode = $resultUpdateCommand->run($resultUpdateInput, $output);

            $io->progressAdvance();
        }

        $io->progressFinish();
    }
}
