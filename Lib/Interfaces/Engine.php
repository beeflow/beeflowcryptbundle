<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 25.09.16 14:46
 */

namespace Beeflow\BeeflowCryptBundle\Lib\Interfaces;

/**
 * Interface Engine
 *
 * @package AppBundle\Lib\BFCrypt\Interfaces
 */
interface Engine
{
    /**
     * @param string $message
     * @param string $key
     *
     * @return string
     * @throws \Exception
     */
    public function encrypt($message, $key);

    /**
     * @param string $message
     * @param string $key
     *
     * @return string
     * @throws \Exception
     */
    public function decrypt($message, $key);
}