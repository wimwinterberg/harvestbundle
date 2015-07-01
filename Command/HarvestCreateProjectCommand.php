<?php
namespace WeAreBuilders\HarvestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use WeAreBuilders\HarvestBundle\Services\Harvest;

/**
 * Class HarvestDumpDayEntriesCommand
 *
 * @package WeAreBuilders\AutomateBundle\Command
 */
class HarvestCreateProjectCommand extends ContainerAwareCommand
{
    /**
     * New client reference
     *
     */
    const NEW_CLIENT_REFERENCE = 9999999;

    /**
     * Arguments
     *
     */
    const ARGUMENT_PROJECT_NAME = 'project-name';

    /**
     * Options
     *
     */
    const OPTION_CLIENT_REFERENCE = 'client-reference';
    const OPTION_CLIENT_NAME      = 'client-name';

    /**
     * Configures the current command.
     *
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('harvest:project:create')
             ->setDescription('Create a project in the harvest.')
             ->addArgument(self::ARGUMENT_PROJECT_NAME, InputArgument::OPTIONAL, 'Name of the project to create.')
             ->addOption(self::OPTION_CLIENT_REFERENCE, null, InputOption::VALUE_OPTIONAL, 'Client reference to create project for.')
             ->addOption(self::OPTION_CLIENT_NAME, null, InputOption::VALUE_OPTIONAL, 'Client name to create project for -> overrules option client-reference')
        ;
    }

    /**
     * Retrieve harvest api interface
     *
     * @return Harvest
     */
    protected function getWrbHarvest()
    {
        return $this->getContainer()->get('wrb_harvest');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int     null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $retValue         = 1;
        $clientReferences = array();
        $projectsOfClient = array();
        $projectName      = $input->getArgument(self::ARGUMENT_PROJECT_NAME);
        $clientName       = $input->getOption(self::OPTION_CLIENT_NAME);
        $clientReference  = $input->getOption(self::OPTION_CLIENT_REFERENCE);
        $harvest          = $this->getWrbHarvest();
        $questionHelper   = $this->getQuestionHelper();

        // load existing clients
        foreach ($harvest->getClients() as $client) {
            $clientReferences[$client->getId()] = $client->getName();
        }

        // region verify optional client info or add new client
        if ($clientReference !== null || $clientName !== null) {
            if ($clientName === null) {
                if (!array_key_exists($clientReference, $clientReferences)) {
                    $output->writeln(sprintf('<error>Client reference \'%1$s\' does not exists</error>', $clientReference));
                    $clientReference = null;
                } else {
                    $clientName = $clientReferences[$clientReference];
                }
            } else {
                // try to find client name
                $normalizedValue = strtolower(trim($clientName));
                $clientReference = null;
                foreach ($clientReferences as $key => $value) {
                    if ($normalizedValue == strtolower(trim($value))) {
                        $clientReference = $key;
                        break;
                    }
                }

                if ($clientReference === null) {
                    $output->writeln(sprintf('<error>Client <enteredValue>%1$s</enteredValue> does not exists</error>', $clientName));

                    if ($questionHelper->ask($input, $output, new ConfirmationQuestion(sprintf('<question>Create client \'%1$s\'?</question> <defaultValues>[<defaultValue>y</defaultValue>]</defaultValues> ', $clientName), true))) {
                        $clientReference                    = $harvest->addNewClient($clientName);
                        $clientReferences[$clientReference] = $clientName;;
                    } else {
                        $clientName = null;
                    }
                }
            }
        }
        // endregion

        // region ask for client reference of existing client or create new client
        if ($clientName === null) {
            $choices                             = $clientReferences;
            $choices[self::NEW_CLIENT_REFERENCE] = '* New Client';
            $question                            = new ChoiceQuestion('<question>Please select Id of the client to use for creating new project <defaultValues>(default to <defaultValue>new client</defaultValue>)</defaultValues>:</question> ', $choices, self::NEW_CLIENT_REFERENCE);
            $question->setErrorMessage('Client %1$s is invalid');
            $question->setAutocompleterValues(array_merge($clientReferences, array_keys($clientReferences)));
            $question->setNormalizer(
                function ($value) use ($choices) {
                    $retValue        = $value;
                    $normalizedValue = strtolower(trim($value));
                    foreach ($choices as $key => $choice) {
                        if ($normalizedValue == strtolower(trim($choice))) {
                            $retValue = $key;
                            break;
                        }
                    }

                    return $retValue;
                }
            )
            ;
            $clientName      = $questionHelper->ask($input, $output, $question);
            $clientReference = array_search($clientName, $choices);

            if ($clientReference == self::NEW_CLIENT_REFERENCE) {

                $question = new Question('<question>Please enter name of the client to create:</question> ');
                $question->setValidator(
                    function ($answer) use ($clientReferences) {
                        $answer = strtolower(trim($answer));

                        if (strlen($answer) == 0) {
                            throw new \RuntimeException(sprintf('Client name cannot be empty', $answer));
                        }

                        foreach ($clientReferences as $reference => $name) {
                            if (strtolower(trim($name)) == $answer) {
                                throw new \RuntimeException(sprintf('Given client \'%1$s\' already exists', $answer));
                            }
                        }

                        return $answer;
                    }
                )
                ;

                $clientName                         = $questionHelper->ask($input, $output, $question);
                $clientReference                    = $harvest->addNewClient($clientName);
                $clientReferences[$clientReference] = $clientName;
            }
        }
        // endregion

        // region add project for client
        if ($clientReference !== null) {
            // fetch all project for client
            foreach ($harvest->getProjectsByClientReference($clientReference) as $project) {
                $projectsOfClient[$project->getId()] = $project->getName();
            }

            // ask for project when not given
            if ($projectName === null) {
                $question = new Question(sprintf('<question>Please enter name of the project to create for <enteredValue>%1$s</enteredValue> <defaultValues>[<defaultValue>release-1.0.0</defaultValue>]</defaultValues></question>: ', $clientName), 'release-1.0.0');
                $question->setValidator(
                    function ($answer) use ($projectsOfClient) {
                        $answer = strtolower(trim($answer));

                        if (strlen($answer) == 0) {
                            throw new \RuntimeException(sprintf('Project name cannot be empty', $answer));
                        }

                        foreach ($projectsOfClient as $name) {
                            if (strtolower(trim($name)) == $answer) {
                                throw new \RuntimeException(sprintf('Given project \'%1$s\' already exists', $answer));
                            }
                        }

                        return $answer;
                    }
                )
                ;
                $projectName = $questionHelper->ask($input, $output, $question);

                if (!$questionHelper->ask($input, $output, new ConfirmationQuestion(sprintf('<question>Create project <enteredValue>%1$s</enteredValue> for <enteredValue>%2$s</enteredValue>? <defaultValues>[<defaultValue>y</defaultValue>]</defaultValues></question> ', $projectName, $clientReferences[$clientReference]), true))) {
                    $projectName = null;
                }
            }

            if ($projectName !== null) {
                if (in_array($projectName, $projectsOfClient)) {
                    $output->writeln(sprintf('<error>Given project \'%1$s\' already exists</error>', $projectName));
                } else {
                    $output->writeln(sprintf('<info>Creating new project <enteredValue>%1$s</enteredValue> for client <enteredValue>%2$s</enteredValue>...</info>', $projectName, $clientName));
                    $harvest->addNewProject($projectName, $clientReference);
                    $retValue = 0;
                }
            }
        }

        // endregion

        return $retValue;
    }

    /**
     * Retrieve question helper
     *
     * @return QuestionHelper
     */
    protected function getQuestionHelper()
    {
        return $this->getHelper('question');
    }
}