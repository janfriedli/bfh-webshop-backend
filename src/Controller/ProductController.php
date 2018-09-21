<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends FOSRestController
{
    /**
     * Creates an Article resource
     * @Rest\Get("/ping")
     * @param Request $request
     * @return View
     */
    public function ping(Request $request): View
    {
        return View::create(['test' => 'pong'], Response::HTTP_OK);
    }
}