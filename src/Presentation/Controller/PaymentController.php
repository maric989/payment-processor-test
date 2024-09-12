<?php

namespace App\Presentation\Controller;

use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\Factory\PaymentProviderFactory;
use App\Application\Request\ProcessPaymentRequest;
use App\Domain\Exception\PaymentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentProviderFactory $paymentProviderFactory
    ) {
    }

    #[Route('/payment/{provider}', name: 'payment', requirements: ['provider' => 'shift4|aci'], methods: ['POST'])]
    public function processPayment(string $provider, ProcessPaymentRequest $request): JsonResponse
    {
        try {
            $paymentProvider = $this->paymentProviderFactory->getProvider($provider);
            $processPaymentDto = new ProcessPaymentDTO(
                amount: $request->getAmount(),
                currency: $request->getCurrency(),
                cardNumber: $request->getCardNumber(),
                cardExpYear: $request->getCardExpYear(),
                cardExpMonth: $request->getCardExpMonth(),
                cardCvv: $request->getCardCvv(),
            );

            $response = $paymentProvider->processPayment($processPaymentDto);

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (PaymentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An unexpected error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}