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
use OpenApi\Annotations as OA;

class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentProviderFactory $paymentProviderFactory
    ) {
    }

    /**
     * @OA\Post(
     *     path="/payment/{provider}",
     *     summary="Process a payment",
     *     description="Processes a payment using the specified provider (shift4 or aci).",
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         description="The payment provider to use (shift4 or aci)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"shift4", "aci"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="amount", type="number", example=152.45),
     *             @OA\Property(property="currency", type="string", example="EUR"),
     *             @OA\Property(property="card_number", type="string", example="4200000000000000"),
     *             @OA\Property(property="card_exp_year", type="integer", example=2026),
     *             @OA\Property(property="card_exp_month", type="integer", example=12),
     *             @OA\Property(property="card_cvv", type="string", example="444")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment processed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="transactionId", type="string", example="txn_12345"),
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Invalid card number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
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