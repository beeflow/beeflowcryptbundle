<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 13.11.16 10:37
 */

namespace Beeflow\BeeflowCryptBundle\Command;

use Beeflow\BeeflowCryptBundle\Entity\ApiKeys;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beeflow:crypt:client:delete')
            ->setDescription('Command to delete client with API key')
            ->addOption('key', null, InputOption::VALUE_REQUIRED, 'ApiKey');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getContainer()->get('logger');

        $client = $input->getOption('key');

        $api = $this->getContainer()->get('doctrine')->getRepository('BeeflowCryptBundle:ApiKey')->find($client);
        if (!($api instanceof ApiKeys)) {
            $this->logger->error('There is no client fo this ApiKey');
            exit(-1);
        }


        $methodName = '\Beeflow\BeeflowCryptBundle\Lib\Engines\\' . $api->getCryptType();
        try {
            $ob = new $methodName();
            $ob->deleteCertFile($api->getCertFile());
        } catch (\Exception $ex) {
            $this->logger->error("There is no such encryption method as " . $api->getCryptType());
            exit(-1);
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->delete($api);
        $em->flush();

        $output->writeln('Api key removed');
    }
}