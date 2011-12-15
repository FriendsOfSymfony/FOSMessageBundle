<?php

namespace Ornicar\MessageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MongoDBMigrateMessageMetadataCommand extends ContainerAwareCommand
{
    /**
     * @var \MongoCollection
     */
    private $messageCollection;

    /**
     * @var \MongoCollection
     */
    private $participantCollection;

    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('ornicar:message:mongodb:migrate:message-metadata')
            ->setDescription('Migrates message document hash fields to embedded metadata')
            ->addArgument('participantClass', InputArgument::REQUIRED, 'Participant class')
            ->addOption('safe', null, InputOption::VALUE_OPTIONAL, 'Mongo update option', false)
            ->addOption('fsync', null, InputOption::VALUE_OPTIONAL, 'Mongo update option', false)
        ;
    }

    /**
     * @see Symfony\Bundle\FrameworkBundle\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $registry = $this->getContainer()->get('doctrine.odm.mongodb');
        $messageClass = $this->getContainer()->getParameter('ornicar_message.message_class');
        $participantClass = $input->getArgument('participantClass');

        if (!$dm = $registry->getManagerForClass($messageClass)) {
            throw new \RuntimeException(sprintf('There is no DocumentManager for message class "%s"', $messageClass));
        }

        $this->messageCollection = $registry
            ->getManagerForClass($messageClass)
            ->getDocumentCollection($messageClass)
            ->getMongoCollection();
        $this->participantCollection = $registry
            ->getManagerForClass($participantClass)
            ->getDocumentCollection($participantClass)
            ->getMongoCollection();
    }

    /**
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cursor = $this->messageCollection->find(
            array('metadata' => array('$exists' => false)),
            array('isReadByParticipant' => 1)
        );

        $updateOptions = array(
            'multiple' => false,
            'safe' => $input->getOption('safe'),
            'fsync' => $input->getOption('fsync'),
        );

        $numProcessed = 0;

        if (!$numTotal = $cursor->count()) {
            $output->writeln('There are no message documents to migrate.');
            return;
        }

        $printStatus = function() use ($output, &$numProcessed, $numTotal) {
            $output->write(sprintf("Processed: <info>%d</info> / Complete: <info>%d%%</info>\r", $numProcessed, round(100 * ($numProcessed / $numTotal))));
        };

        declare(ticks=100) {
            register_tick_function($printStatus);

            foreach ($cursor as $message) {
                $this->messageCollection->update(
                    array('_id' => $message['_id']),
                    array('$set' => array('metadata' => $this->createMessageMetadata($message))),
                    $updateOptions
                );
                ++$numProcessed;
            }
        }

        $printStatus();
        echo \PHP_EOL;
        $output->writeln(sprintf('Migrated <info>%d</info> message documents.', $numProcessed));
    }

    /**
     * Create message metadata array
     *
     * By default, Mongo will not include "$db" when creating the participant
     * reference. We'll add that manually to be consistent with Doctrine.
     *
     * @param array $message
     * @return array
     */
    private function createMessageMetadata(array $message)
    {
        $metadata = array();

        foreach ($message['isReadByParticipant'] as $participantId => $isRead) {
            $metadata[] = array(
                'isRead' => $isRead,
                'participant' => $this->participantCollection->createDBRef(array('_id' => new \MongoId($participantId))) + array('$db' => (string) $this->participantCollection->db),
            );
        }

        return $metadata;
    }
}
