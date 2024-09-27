<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Mutation\Inquiry;

use Exception;
use Overblog\GraphQLBundle\Definition\Argument;
use Psr\Log\LoggerInterface;
use Shopsys\FrameworkBundle\Model\Inquiry\InquiryData;
use Shopsys\FrameworkBundle\Model\Inquiry\InquiryDataFactory;
use Shopsys\FrameworkBundle\Model\Inquiry\InquiryFacade;
use Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrontendApiBundle\Model\Mutation\AbstractMutation;
use Shopsys\FrontendApiBundle\Model\Resolver\Products\Exception\ProductNotFoundUserError;

class CreateInquiryMutation extends AbstractMutation
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Shopsys\FrameworkBundle\Model\Inquiry\InquiryDataFactory $inquiryDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Inquiry\InquiryFacade $inquiryFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     */
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly InquiryDataFactory $inquiryDataFactory,
        protected readonly InquiryFacade $inquiryFacade,
        protected readonly ProductFacade $productFacade,
    ) {
    }

    /**
     * @param \Overblog\GraphQLBundle\Definition\Argument $argument
     * @return bool
     */
    public function createInquiryMutation(Argument $argument): bool
    {
        try {
            $inquiryData = $this->createInquiryDataFromArgument($argument);
            $this->inquiryFacade->create($inquiryData);
        } catch (ProductNotFoundException) {
            throw new ProductNotFoundUserError(sprintf('Product with UUID "%s" not found', $argument['input']['productUuid']));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return false;
        }

        return true;
    }

    /**
     * @param \Overblog\GraphQLBundle\Definition\Argument $argument
     * @return \Shopsys\FrameworkBundle\Model\Inquiry\InquiryData
     */
    protected function createInquiryDataFromArgument(Argument $argument): InquiryData
    {
        $input = $argument['input'];

        $inquiryData = $this->inquiryDataFactory->create();
        $product = $this->productFacade->getByUuid($input['productUuid']);

        $inquiryData->firstName = $input['firstName'];
        $inquiryData->lastName = $input['lastName'];
        $inquiryData->email = $input['email'];
        $inquiryData->telephone = $input['telephone'];
        $inquiryData->companyName = $input['companyName'] ?? null;
        $inquiryData->companyNumber = $input['companyNumber'] ?? null;
        $inquiryData->companyTaxNumber = $input['companyTaxNumber'] ?? null;
        $inquiryData->note = $input['note'] ?? null;
        $inquiryData->product = $product;

        return $inquiryData;
    }
}
