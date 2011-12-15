<?php

namespace Ornicar\MessageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MongoDBMigrateThreadMetadataCommand extends ContainerAwareCommand
{
    /**
     * @var \MongoCollection
     */
    private $threadCollection;

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
            ->setName('ornicar:message:mongodb:migrate:thread-metadata')
            ->setDescription('Migrates thread document hash fields to embedded metadata')
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
        $threadClass = $this->getContainer()->getParameter('ornicar_message.thread_class');
        $participantClass = $input->getArgument('participantClass');

        if (!$dm = $registry->getManagerForClass($threadClass)) {
            throw new \RuntimeException(sprintf('There is no DocumentManager for thread class "%s"', $threadClass));
        }

        $this->threadCollection = $registry
            ->getManagerForClass($threadClass)
            ->getDocumentCollection($threadClass)
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
        $cursor = $this->threadCollection->find(
            array('metadata' => array('$exists' => false)),
            array(
                'datesOfLastMessageWrittenByOtherParticipant' => 1,
                'datesOfLastMessageWrittenByParticipant' => 1,
                'isDeletedByParticipant' => 1,
            )
        );

        $updateOptions = array(
            'multiple' => false,
            'safe' => $input->getOption('safe'),
            'fsync' => $input->getOption('fsync'),
        );

        $numProcessed = 0;

        if (!$numTotal = $cursor->count()) {
            $output->writeln('There are no thread documents to migrate.');
            return;
        }

        $printStatus = function() use ($output, &$numProcessed, $numTotal) {
            $output->write(sprintf("Processed: <info>%d</info> / Complete: <info>%d%%</info>\r", $numProcessed, round(100 * ($numProcessed / $numTotal))));
        };

        declare(ticks=100) {
            register_tick_function($printStatus);

            foreach ($cursor as $thread) {
                $this->threadCollection->update(
                    array('_id' => $thread['_id']),
                    array('$set' => array('metadata' => $this->createThreadMetadata($thread))),
                    $updateOptions
                );
                ++$numProcessed;
            }
        }

        $printStatus();
        echo \PHP_EOL;
        $output->writeln(sprintf('Migrated <info>%d</info> thread documents.', $numProcessed));
    }

    /**
     * Create thread metadata array
     *
     * By default, Mongo will not include "$db" when creating the participant
     * reference. We'll add that manually to be consistent with Doctrine.
     *
     * @param array $thread
     * @return array
     */
    private function createThreadMetadata(array $thread)
    {
        $metadata = array();

        $participantIds = array_keys($thread['datesOfLastMessageWrittenByOtherParticipant'] + $thread['datesOfLastMessageWrittenByParticipant'] + $thread['isDeletedByParticipant']);

        foreach ($participantIds as $participantId) {
            $meta = array(
                'isDeleted' => false,
                'participant' => $this->participantCollection->createDBRef(array('_id' => new \MongoId($participantId))) + array('$db' => (string) $this->participantCollection->db),
            );

            if (isset($thread['isDeletedByParticipant'][$participantId])) {
                $meta['isDeleted'] = $thread['isDeletedByParticipant'][$participantId];
            }

            if (isset($thread['datesOfLastMessageWrittenByOtherParticipant'][$participantId])) {
                $meta['lastMessageDate'] = new \MongoDate($thread['datesOfLastMessageWrittenByOtherParticipant'][$participantId]);
            }

            if (isset($thread['datesOfLastMessageWrittenByParticipant'][$participantId])) {
                $meta['lastParticipantMessageDate'] = new \MongoDate($thread['datesOfLastMessageWrittenByParticipant'][$participantId]);
            }

            $metadata[] = $meta;
        }

        return $metadata;
    }
}
