<?php


namespace App\Service;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Hateoas\Configuration\Route as routing;
use Symfony\Component\HttpFoundation\Request;
use Hateoas\Representation\Factory\PagerfantaFactory;


class PaginationService
{

    public function paginate(Request $request, $item, int $limit, string $route)
    {
        $adapter = new ArrayAdapter($item);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage((int)($request->get('limit')) ?: $limit);
        $currentPage = (int)($request->get('page')) ?: 1;
        $pagerfanta->setCurrentPage($currentPage);


        $pagerfantaFactory   = new PagerfantaFactory(); // you can pass the page,
        // and limit parameters name


        $paginatedCollection = $pagerfantaFactory->createRepresentation(
            $pagerfanta,
            new routing($route, array())
        );

        return $paginatedCollection;
    }
}
