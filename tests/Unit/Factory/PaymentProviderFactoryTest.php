<?php

namespace App\Tests\Unit\Factory;

use App\Application\Factory\PaymentProviderFactory;
use App\Application\Service\Payment\AciProvider;
use App\Application\Service\Payment\Shift4Provider;
use App\Domain\Exception\InvalidPaymentProviderException;
use PHPUnit\Framework\TestCase;

class PaymentProviderFactoryTest extends TestCase
{
    private AciProvider $aciProvider;
    private Shift4Provider $shift4Provider;
    private PaymentProviderFactory $factory;

    protected function setUp(): void
    {
        $this->aciProvider = $this->createMock(AciProvider::class);
        $this->shift4Provider = $this->createMock(Shift4Provider::class);

        $this->factory = new PaymentProviderFactory($this->aciProvider, $this->shift4Provider);
    }

    public function testGetAciProvider()
    {
        $provider = $this->factory->getProvider('aci');
        $this->assertSame($this->aciProvider, $provider);
    }

    public function testGetShift4Provider()
    {
        $provider = $this->factory->getProvider('shift4');
        $this->assertSame($this->shift4Provider, $provider);
    }

    public function testInvalidPaymentProvider()
    {
        $this->expectException(InvalidPaymentProviderException::class);
        $this->factory->getProvider('invalid_provider');
    }
}