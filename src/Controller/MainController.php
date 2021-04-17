<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return new Response('<h1>Welcome Home</h1>');
    }

    /**
     * @Route("/custom/{name?}", name="custom")
     */
    public function custom(Request $request)
    {
        $name = $request->get('name');
        return new Response('<h1>Welcome ' . $name . '! </h1>');
    }
}
