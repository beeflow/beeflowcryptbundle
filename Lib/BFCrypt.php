<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 25.09.16 14:45
 */

namespace Beeflow\BeeflowCryptBundle\Lib;


use Beeflow\BeeflowCryptBundle\Entity\ApiKeys;
use Beeflow\BeeflowCryptBundle\Lib\Interfaces\EngineInterface;

class BFCrypt
{
    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * BFCrypt constructor.
     *
     * @param string $apiKey
     * @param        $doctrine
     *
     * @throws \Exception
     */
    public function __construct($apiKey, $doctrine)
    {
        if (empty($apiKey)) {
            return;
        }

        $apiKeyEntity = $doctrine->getRepository('BeeflowCryptBundle:ApiKeys')->find($apiKey);
        if (!($apiKeyEntity instanceof ApiKeys)) {
            throw new \Exception('There is no such ApiKey as ' . $apiKey);
        }

        $encryptionType = $apiKeyEntity->getCryptType();
        $this->loadEngine($encryptionType);

        $certFile = $apiKeyEntity->getCertFile();
        $this->engine->setCertFile($certFile)->prepareCerts();
    }

    /**
     * Rejestruje nowego klienta
     *
     * @param $cert
     * @param $client
     * @param $encryptionType
     *
     * @return string
     * @throws \Exception
     */
    public function register($cert, $client, $encryptionType)
    {
        $this->loadEngine($encryptionType);

        $apiKeyEntity = $this->doctrine->getRepository('BeeflowCryptBundle:ApiKeys')->findOneBy(['client' => strtoupper($client)]);
        if (!($apiKeyEntity instanceof ApiKeys)) {
            $fileName = $apiKey = \uniqid();

            $apiKeyEntity = new ApiKeys();
            $apiKeyEntity->setClient(strtoupper($client))
                ->setCryptType($encryptionType)
                ->setId($apiKey)
                ->setCertFile($fileName);
        } else {
            $fileName = $apiKeyEntity->getCertFile();
        }

        $this->engine->installCertFile($cert, $fileName);

        $em = $this->doctrine->getManager();

        $em->persist($apiKeyEntity);
        $em->flush();

        $response = json_encode(['api_key' => $apiKeyEntity->getId()]);

        return $this->encrypt($response);
    }

    /**
     * @param string $message
     *
     * @return string
     * @throws \Exception
     */
    public function encrypt($message)
    {
        return $this->engine->encrypt($message);
    }

    /**
     * @param string $message
     *
     * @return string
     * @throws \Exception
     */
    public function decrypt($message)
    {
        return $this->engine->decrypt($message);
    }

    /**
     * @param $encryptionType
     *
     * @throws \Exception
     */
    protected function loadEngine($encryptionType)
    {
        $engineClass = '\Beeflow\BeeflowCryptBundle\Lib\Engines\\' . $encryptionType;

        if (class_exists($engineClass)) {
            try {
                $this->engine = new $engineClass();
                if (!($this->engine instanceof EngineInterface)) {
                    throw new \Exception('There is no such engine as ' . $encryptionType);
                }
            } catch (\Exception $ex) {
                throw $ex;
            }
        } else {
            throw new \Exception('There is no such engine as ' . $encryptionType);
        }
    }

}