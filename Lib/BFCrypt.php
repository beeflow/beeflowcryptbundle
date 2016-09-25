<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 25.09.16 14:45
 */

namespace Beeflow\BeeflowCryptBundle\Lib;


use Beeflow\BeeflowCryptBundle\Lib\Interfaces\Engine;

class BFCrypt
{
    /**
     * @var Engine
     */
    private $engine;


    /**
     * BFCrypt constructor.
     *
     * @param string $encryptionType
     *
     * @throws \Exception
     */
    public function __construct($encryptionType)
    {
        $engineClass = '\AppBundle\Lib\BFCrypt\Engines\\' . $encryptionType;

        try {
            $this->engine = new $engineClass();
            if (!($this->engine instanceof Engine)) {
                throw new \Exception('-- There is no such engine as ' . $encryptionType);
            }
        } catch (\Exception $ex) {
            throw new \Exception('There is no such engine as ' . $encryptionType);
        }
    }


    /**
     * @param string $message
     * @param string $key
     *
     * @return string
     * @throws \Exception
     */
    public function encrypt($message, $key)
    {
        return $this->engine->encrypt($message, $key);
    }

    /**
     * @param string $message
     * @param string $key
     *
     * @return string
     * @throws \Exception
     */
    public function decrypt($message, $key)
    {
        return $this->engine->decrypt($message, $key);
    }

}