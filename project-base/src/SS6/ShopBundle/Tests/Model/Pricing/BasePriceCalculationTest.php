<?php

namespace SS6\ShopBundle\Tests\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class BasePriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePriceProvider() {
		return array(
			array(
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
				'inputPrice' => '6999',
				'vatPercent' => '21',
				'basePriceWithoutVat' => '6998.78',
				'basePriceWithVat' => '8469',
				'basePriceVatAmount' => '1470.22',
			),
			array(
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
				'inputPrice' => '6999.99',
				'vatPercent' => '21',
				'basePriceWithoutVat' => '5784.8',
				'basePriceWithVat' => '7000',
				'basePriceVatAmount' => '1215.2',
			),
		);
	}

	/**
	 * @dataProvider testCalculatePriceProvider
	 */
	public function testCalculatePrice(
		$inputPriceType,
		$inputPrice,
		$vatPercent,
		$basePriceWithoutVat,
		$basePriceWithVat,
		$basePriceVatAmount
	) {
		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->setMethods(array('getRoundingType'))
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock
			->expects($this->any())->method('getRoundingType')
				->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

		$rounding = new Rounding($pricingSettingMock);
		$priceCalculation = new PriceCalculation($rounding);
		$basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

		$vat = new Vat(new VatData('vat', $vatPercent));

		$price = $basePriceCalculation->calculatePrice($inputPrice, $inputPriceType, $vat);

		$this->assertEquals(round($basePriceWithoutVat, 6), round($price->getPriceWithoutVat(), 6));
		$this->assertEquals(round($basePriceWithVat, 6), round($price->getPriceWithVat(), 6));
		$this->assertEquals(round($basePriceVatAmount, 6), round($price->getVatAmount(), 6));
	}

	public function testApplyCoefficientProvider() {
		return array(
			array(
				'priceWithVat' => '100',
				'vatPercent' => '20',
				'coefficient' => '2',
				'resultPriceWithVat' => '200',
				'resultPriceWithoutVat' => '167',
				'resultVatAmount' => '33',
			),
			array(
				'priceWithVat' => '100',
				'vatPercent' => '10',
				'coefficient' => '1',
				'resultPriceWithVat' => '100',
				'resultPriceWithoutVat' => '91',
				'resultVatAmount' => '9',
			),
			array(
				'priceWithVat' => '100',
				'vatPercent' => '20',
				'coefficient' => '0.6789',
				'resultPriceWithVat' => '68',
				'resultPriceWithoutVat' => '57',
				'resultVatAmount' => '11',
			),
		);
	}

	/**
	 * @dataProvider testApplyCoefficientProvider
	 */
	public function testApplyCoefficient(
		$priceWithVat,
		$vatPercent,
		$coefficient,
		$resultPriceWithVat,
		$resultPriceWithoutVat,
		$resultVatAmount
	) {
		$rounding = $this->getMock(
			Rounding::class,
			['roundPriceWithVat', 'roundPriceWithoutVat', 'roundVatAmount'],
			[],
			'',
			false
		);
		$rounding->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function ($value) {
			return round($value);
		});
		$rounding->expects($this->any())->method('roundPriceWithoutVat')->willReturnCallback(function ($value) {
			return round($value);
		});
		$rounding->expects($this->any())->method('roundVatAmount')->willReturnCallback(function ($value) {
			return round($value);
		});
		$priceCalculation = new PriceCalculation($rounding);
		$basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

		$price = new Price(0, $priceWithVat, 0);
		$vat = new Vat(new VatData('vat', $vatPercent));
		$resultPrice = $basePriceCalculation->applyCoefficient($price, $vat, $coefficient);

		$this->assertEquals(round($resultPriceWithVat, 6), round($resultPrice->getPriceWithVat(), 6));
		$this->assertEquals(round($resultPriceWithoutVat, 6), round($resultPrice->getPriceWithoutVat(), 6));
		$this->assertEquals(round($resultVatAmount, 6), round($resultPrice->getVatAmount(), 6));
	}

}
