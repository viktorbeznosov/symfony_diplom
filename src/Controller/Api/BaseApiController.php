<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BaseApiController extends AbstractController
{
    protected function tokenNotFound(Request $request)
    {
        return !$request->headers->has('Authorization') || !0 === strpos($request->headers->get('Authorization'), 'Bearer');
    }
}
