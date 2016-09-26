<?php

namespace Beeflow\BeeflowCryptBundle\EventListener;

use Beeflow\BeeflowCryptBundle\Lib\BFCrypt;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Description of BeeflowCryptListener
 *
 * @author        Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright (c) 2016 Beeflow Ltd
 */
class BeeflowCryptListener implements EventSubscriberInterface
{

    /**
     * @var
     */
    protected $doctrine;

    /**
     * @var string
     */
    protected $apiKey = null;

    protected $BFCrypt;

    /**
     * BeeflowCryptListener constructor.
     *
     * @param $doctrine
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        $jsonContent = $event->getRequest()->getContent();
        $content = json_decode($jsonContent, true);

        if (empty($content) || !is_array($content)) {
            return;
        }

        if (!isset($content['api_key'])) {
            return;
        }

        $this->apiKey = $content['api_key'];
        try {
            $this->BFCrypt = new BFCrypt($this->apiKey, $this->doctrine);
        } catch (\Exception $ex) {
            return;
        }

        if (!isset($content['crypted_request'])) {
            return;
        }

        try {
            $message = $this->BFCrypt->decrypt($content['crypted_request']);
        } catch(\Exception $ex) {
            return;
        }

        if (!is_array($message)) {
            $event->getRequest()->attributes->set('message', $message);
        } else {
            foreach ($message as $key => $value) {
                $event->getRequest()->attributes->set($key, $value);
            }
        }

    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (empty($this->apiKey) || !($this->BFCrypt instanceof BFCrypt)) {
            return;
        }

        $content = $response->getContent();
        try {
            $resp = [
                'encrypted_response' => $this->BFCrypt->encrypt($content)
            ];
            $response->setContent(json_encode($resp));
        } catch (\Exception $ex) {
            return;
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default BeeflowCryptListener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }


}
