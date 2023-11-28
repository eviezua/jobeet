<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\CategoryService;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:create-category',
    description: 'This command creates new category',
)]
class CreateCategoryCommand extends Command
{
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command helps you to add new category in db!')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of your category: ')
        ;
    }
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getArgument('name')) {
            $question = new Question('<question>Please choose a name: </question>');
            $question->setValidator(function ($name) {
                if (empty($name)) {
                    throw new \Exception('Name can not be empty');
                }

                return $name;
            });

            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('name', $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*$io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        */
        $output->writeln([
            'Category Creator',
            '============',
            '',
        ]);
        $output->writeln('Hey!');
        $output->writeln('Are you going to create new category?');
        $output->writeln(sprintf('Name: %s', $input->getArgument('name')));
        $this->categoryService->create($input->getArgument('name'));
        $output->writeln('<fg=green> Category successfully created! </>');
        return Command::SUCCESS;
    }
}
