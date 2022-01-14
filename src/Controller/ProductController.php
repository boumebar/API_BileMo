<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="api_products", methods="GET")
     */
    public function index(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findAll();
        $json = $serializer->serialize($products, 'json');
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }
}
