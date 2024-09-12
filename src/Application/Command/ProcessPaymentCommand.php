<?php

namespace App\Application\Command;

use App\Application\DTO\Input\ProcessPaymentDTO;
use App\Application\Factory\PaymentProviderFactory;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessPaymentCommand extends Command
{
    protected static $defaultName = 'app:process-payment';

    public function __construct(
        private readonly PaymentProviderFactory $paymentProviderFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Processes a payment using AciProvider')
            ->addArgument('provider', InputArgument::REQUIRED, 'The amount to charge')
            ->addOption('amount', null, InputOption::VALUE_REQUIRED, 'The amount to charge.')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'The currency of the payment.')
            ->addOption('cardNumber', null, InputOption::VALUE_REQUIRED, 'The card number.')
            ->addOption('cardExpYear', null, InputOption::VALUE_REQUIRED, 'The card expiry year.')
            ->addOption('cardExpMonth', null, InputOption::VALUE_REQUIRED, 'The card expiry month.')
            ->addOption('cardCvv', null, InputOption::VALUE_REQUIRED, 'The card CVV.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $providerName = $input->getArgument('provider');
        $requiredOptions = ['amount', 'currency', 'cardNumber', 'cardExpYear', 'cardExpMonth', 'cardCvv'];
        foreach ($requiredOptions as $option) {
            if (empty($input->getOption($option))) {
                throw new InvalidArgumentException(sprintf('The required option "--%s" is missing.', $option));
            }
        }
        $paymentDTO = new ProcessPaymentDTO(
            amount: $input->getOption('amount'),
            currency: $input->getOption('currency'),
            cardNumber: $input->getOption('cardNumber'),
            cardExpYear: $input->getOption('cardExpYear'),
            cardExpMonth: $input->getOption('cardExpMonth'),
            cardCvv: $input->getOption('cardCvv'),
        );
        $provider = $this->paymentProviderFactory->getProvider($providerName);

        try {
            $response = $provider->processPayment($paymentDTO);

            $output->writeln(sprintf('<info>Payment processed: %s</info>', $response->transactionId));
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>Payment processing failed: %s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}