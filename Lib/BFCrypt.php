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

    public function __construct($apiKey, $doctrine)
    {
        $apiKeyEntity = $doctrine->getRepository('AppBundle:ApiKeys')->find($apiKey);
        if (!($apiKeyEntity instanceof ApiKeys)) {
            throw new \Exception('There is no such ApiKey as ' . $apiKey);
        }

        $encryptionType = $apiKeyEntity->getCryptType();
        $engineClass = '\AppBundle\Lib\BFCrypt\Engines\\' . $encryptionType;

        try {
            $this->engine = new $engineClass();
            if (!($this->engine instanceof EngineInterface)) {
                throw new \Exception('There is no such engine as ' . $encryptionType);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }

        $certFile = $apiKeyEntity->getCertFile();
        $this->engine->setCertFile($certFile)->prepareCerts();
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

}