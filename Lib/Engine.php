<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 25.09.16 21:15
 */

namespace Beeflow\BeeflowCryptBundle\Lib;

use Beeflow\BeeflowCryptBundle\Lib\Interfaces\EngineInterface;

abstract class Engine implements EngineInterface
{
    protected $certDir = __DIR__ . '/../Resources/certs/';
    protected $certFile;

    /**
     * @param string $certFile
     *
     * @return $this
     */
    public function setCertFile($certFile)
    {
        $this->certFile = $certFile;

        return $this;
    }

    /**
     * @param string $cert in base64
     * @param string $fileName
     *
     * @return $this
     */
    public function installCertFile($cert, $fileName)
    {
        file_put_contents($this->certDir . $fileName, base64_decode($cert));
        $this->setCertFile($fileName)->prepareCerts();

        return $this;
    }

    abstract public function prepareCerts();
}