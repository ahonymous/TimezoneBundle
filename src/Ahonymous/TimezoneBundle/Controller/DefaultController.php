<?php

namespace Ahonymous\TimezoneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        var_dump($this->get('timezone')->getTimeZoneName('Asia/Bahrain'));
        var_dump($this->get('timezone')->getTimeZoneName('Europe/London'));
        exit;

        return new JsonResponse();
    }
}
