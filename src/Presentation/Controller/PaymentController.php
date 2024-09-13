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
use OpenApi\Attributes as OA;

class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentProviderFactory $paymentProviderFactory
    ) {
    }

    #[Route('/payment/{provider}', name: 'payment', requirements: ['provider' => 'shift4|aci'], methods: ['POST'])]
    #[OA\Post(
        summary: 'Process a payment',
        description: 'Processes a payment using the specified provider (shift4 or aci).',
        parameters: [
            new OA\Parameter(
                name: 'provider',
                in: 'path',
                description: 'The payment provider to use (shift4 or aci)',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['shift4', 'aci'])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'amount', type: 'number', example: 152.45),
                    new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                    new OA\Property(property: 'card_number', type: 'string', example: '4200000000000000'),
                    new OA\Property(property: 'card_exp_year', type: 'integer', example: 2026),
                    new OA\Property(property: 'card_exp_month', type: 'integer', example: 12),
                    new OA\Property(property: 'card_cvv', type: 'string', example: '444')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment processed successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'transactionId', type: 'string', example: 'txn_12345'),
                        new OA\Property(property: 'date', type: 'string', example: '13.09.2024'),
                        new OA\Property(property: 'amount', type: 'string', example: '154.53'),
                        new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                        new OA\Property(property: 'cardBin', type: 'string', example: '420000')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Invalid card number')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'An unexpected error occurred')
                    ]
                )
            )
        ]
    )]
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