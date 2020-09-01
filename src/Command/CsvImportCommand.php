<?php

namespace App\Command;

use App\Entity\Appel;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validation;

class CsvImportCommand extends Command
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * bulk number 
     */

    private $bulk_number = 1000;
    /**
     * Array
     */
    private $validations = [];
    /**
     * CsvImportCommand constructor.
     *
     * @param EntityManagerInterface $em
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    /**
     * Configure
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Import dummy CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set("memory_limit", "255M");
        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting import of Feed...');

        $reader = Reader::createFromPath('%kernel.root_dir%/../src/_DATA/tickets_appels_201202.csv');
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);
        $header = $reader->getHeader(); //returns the CSV header record
        $records = $reader->getRecords(); //returns all the CSV records as an Iterator object
        $validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();


        $io->progressStart(iterator_count($records));

        foreach ($records as $key => $record) {
            $old_date = explode('/', $record[$header[3]]);
            $new_data = $old_date[1] . '/' . $old_date[0] . '/' . $old_date[2];
            $new_data = $new_data . ' ' . $record[$header[4]];

            try {
                $appel = new Appel();
                $appel->setCompte($record[$header[0]])
                    ->setNAbonne($record[$header[1]])
                    ->setNFacture($record[$header[2]])
                    ->setDateHeure(new \DateTime('@' . strtotime($new_data)))
                    ->setVolumeReel(floatval($record[$header[5]]))
                    ->setVolumeFacture(floatval($record[$header[6]]))
                    ->setType($record[$header[7]]);
            } catch (Exception $e) {
                array_push($this->validations, [
                    'ligne' => $key,
                    'error' => $e->getMessage()
                ]);
            }

            $errors = $validator->validate($appel);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                array_push($this->validations, [
                    'ligne' => $key,
                    'message' => $errorsString,
                ]);
                $output->writeln(json_encode([
                    'ligne' => $key,
                    'message' => $errorsString,
                ]));
            } else {
                $this->em->persist($appel);
            }

            $io->progressAdvance();
            if ($key % $this->bulk_number == 0) {
                $this->flush();
            }
        }

        if ($key % $this->bulk_number != 0) {
            $this->flush();
        }
        return Command::SUCCESS;
    }

    public function flush()
    {
        try {
            $this->em->flush();
            $this->em->clear();
        } catch (Exception $e) {
            array_push($this->validations, [
                'ligne' => 'x',
                'message' => $e->getMessage(),
            ]);
            echo ($e->getMessage());
        }
    }
}
