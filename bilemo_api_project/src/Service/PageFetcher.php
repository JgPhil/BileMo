<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;


class PageFetcher
{

    public function getPage(Request $request):int
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        return $page;
    }
}