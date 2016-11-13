<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 13.11.16 08:58
 */

namespace Beeflow\BeeflowCryptBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('beeflow:crypt:client:create')
            ->setDescription('Command to create new client API key')
            ->addOption('client', null, InputOption::VALUE_REQUIRED, 'client name')
            ->addOption('method', null, InputOption::VALUE_REQUIRED, 'encryption method', 'AES256')
            ->addOption('cert', null, InputOption::VALUE_REQUIRED, '/some/path/to/your/cert');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getContainer()->get('logger');

        $client = $input->getOption('client');
        $method = $input->getOption('method');
        $cert = $input->getOption('cert');

        if (!file_exists($cert)) {
            $this->logger->error('There is no such cert as ' . $cert);
            exit(-1);
        }

        $certContent = file_get_contents($cert);

        if (mb_strlen($certContent, '8bit') !== 32) {
            $this->logger->error("Needs a 256-bit key!");
            exit(-1);
        }

        $filename = \uniqid();
        $apiKey = \uniqid() . '_' . \uniqid() . '_' . \uniqid();

        $methodName = '\Beeflow\BeeflowCryptBundle\Lib\Engines\\' . $method;
        try {
            $ob = new $methodName();
            $ob->installCertFile(base64_encode($certContent), $filename);
        } catch (\Exception $ex) {
            $this->logger->error("There is no such encryption method as " . $method);
            exit(-1);
        }

        $api = new ApiKeys();
        $api
            ->setId($apiKey)
            ->setCertFile($filename)
            ->setClient($client)
            ->setCryptType($method);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($api);
        $em->flush($api);

        $output->writeln($apiKey);
    }

}