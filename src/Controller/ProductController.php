<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="api_products_index", methods="GET")
     */
    public function index(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $products = $productRepository->findAll();
        $context = SerializationContext::create()->setGroups(["product:index"]);
        $json = $serializer->serialize($products, 'json', $context);
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/products/{id<\d+>}", name="api_products_show", methods="GET")
     */
    public function show(ProductRepository $productRepository, $id, SerializerInterface $serializer): JsonResponse
    {
        $product = $productRepository->find($id);
        $context = SerializationContext::create()->setGroups(["product:show"]);
        $json = $serializer->serialize($product, 'json', $context);
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }
}
