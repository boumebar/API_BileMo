<?php


namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PaginationService
{

    public function paginate($query, int $limit = 3, int $page = 1)
    {


        $paginator = new Paginator($query);
        $offset = (int) ($page - 1) * $limit;
        $data = $paginator
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();

        if ($page <= $this->lastPage($paginator)) {

            return $data;
        }

        throw new NotFoundHttpException("Only " . $this->lastPage($paginator) . " pages available");
    }

    public function total(Paginator $paginator)
    {
        return $paginator->count();
    }

    public function lastPage(Paginator $paginator)
    {
        return ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
    }
}
