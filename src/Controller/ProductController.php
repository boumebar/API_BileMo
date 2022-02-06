<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CacheService;
use App\Service\PaginationService;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="api_products_index", methods="GET")
     */
    public function index(ProductRepository $productRepository, SerializerInterface $serializer, PaginationService $pagination, Request $request, CacheService $cache): JsonResponse
    {
        $products = $productRepository->findAll();

        $paginatedCollection = $pagination->paginate($request, $products, 5, 'api_products_index');
        $json = $serializer->serialize($paginatedCollection, 'json');
        $response = new JsonResponse($json, 200, [], true);

        return $cache->cache($request, $response);
    }

    /**
     * @Route("/api/products/{id<\d+>}", name="api_products_show", methods="GET")
     */
    public function show(ProductRepository $productRepository, $id, SerializerInterface $serializer): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw new JsonException("This product don't exist", JsonResponse::HTTP_BAD_REQUEST);
        }
        $json = $serializer->serialize($product, 'json');
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }
}
