<?php

namespace Beeflow\BeeflowCryptBundle\Entity;

/**
 * ApiKeys
 */
class ApiKeys
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $client;

    /**
     * @var string
     */
    private $cert_file;

    /**
     * @var string
     */
    private $crypt_type;


    /**
     * Set id
     *
     * @param string $id
     *
     * @return ApiKeys
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param string $client
     *
     * @return ApiKeys
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set certFile
     *
     * @param string $certFile
     *
     * @return ApiKeys
     */
    public function setCertFile($certFile)
    {
        $this->cert_file = $certFile;

        return $this;
    }

    /**
     * Get certFile
     *
     * @return string
     */
    public function getCertFile()
    {
        return $this->cert_file;
    }

    /**
     * Set cryptType
     *
     * @param string $cryptType
     *
     * @return ApiKeys
     */
    public function setCryptType($cryptType)
    {
        $this->crypt_type = $cryptType;

        return $this;
    }

    /**
     * Get cryptType
     *
     * @return string
     */
    public function getCryptType()
    {
        return $this->crypt_type;
    }
}

