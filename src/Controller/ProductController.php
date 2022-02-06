<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CacheService;
use OpenApi\Annotations as OA;
use App\Service\PaginationService;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Security as OASecurity;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="api_products_index", methods="GET")
     * @OA\Get(summary="Get list of products")
     * @OA\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Returns the list of products"
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="page number",
     *     @OA\Schema(type="int", default = "1")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of items by page",
     *     @OA\Schema(type="int", default = 10)
     * )
     * 
     * @OA\Tag(name="Products")
     */
    public function index(ProductRepository $productRepository, SerializerInterface $serializer, PaginationService $pagination, Request $request, CacheService $cache): JsonResponse
    {
        $products = $productRepository->findAll();

        $paginatedCollection = $pagination->paginate($request, $products, 10, 'api_products_index');
        $json = $serializer->serialize($paginatedCollection, 'json');
        $response = new JsonResponse($json, 200, [], true);

        return $cache->cache($request, $response);
    }

    /**
     * @Route("/api/products/{id<\d+>}", name="api_products_show", methods="GET")
     * @OA\Get(summary="Get one product by his ID")
     * 
     * 
     * @OA\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Returns a product"
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="Product not found"
     * )
     * 
     * @OA\Tag(name="Products") 
     *      
     * 
     */
    public function show(ProductRepository $productRepository, $id, SerializerInterface $serializer): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw new JsonException("This product don't exist", JsonResponse::HTTP_NOT_FOUND);
        }
        $json = $serializer->serialize($product, 'json');
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }
}
