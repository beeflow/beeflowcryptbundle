<?php

namespace Beeflow\BeeflowCryptBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Description of BeeflowCryptListener
 *
 * @author        Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright (c) 2016 Beeflow Ltd
 */
class BeeflowCryptListener implements EventSubscriberInterface
{

    public function __construct()
    {

    }

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
        dump( $event->getRequest()->query->get());
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default BeeflowCryptListener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }


}
