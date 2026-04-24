<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CalculatePriceRequest;
use App\DTO\PurchaseRequest;
use App\Enum\PaymentProcessor;
use App\Repository\ProductRepository;
use App\Service\Payment\PaymentProcessorService;
use App\Service\PricingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
final class PaymentController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PricingService $pricingService,
        private readonly PaymentProcessorService $paymentProcessorService
    ) {}

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(
        #[MapRequestPayload] CalculatePriceRequest $request
    ): JsonResponse {
        $product = $this->productRepository->findOrFail($request->product);

        $finalPrice = $this->pricingService->calculateFinalPrice(
            $product->getPriceAsFloat(),
            $request->taxNumber,
            $request->couponCode
        );

        return $this->json(['price' => $finalPrice->toFloat()], Response::HTTP_OK);
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(
        #[MapRequestPayload] PurchaseRequest $request
    ): JsonResponse {
        $product = $this->productRepository->findOrFail($request->product);

        $finalPrice = $this->pricingService->calculateFinalPrice(
            $product->getPriceAsFloat(),
            $request->taxNumber,
            $request->couponCode
        );

        $processor = $this->paymentProcessorService->getProcessor(PaymentProcessor::from($request->paymentProcessor));
        $processor->process($finalPrice);

        return $this->json([
            'message' => 'Payment successful',
            'price' => $finalPrice->toFloat()
        ], Response::HTTP_OK);
    }
}