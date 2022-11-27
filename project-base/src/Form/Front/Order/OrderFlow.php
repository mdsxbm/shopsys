<?php

declare(strict_types=1);

namespace App\Form\Front\Order;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\StepInterface;

class OrderFlow extends FormFlow
{
    /**
     * @var bool
     */
    protected $allowDynamicStepNavigation = true;

    /**
     * @var int
     */
    private $domainId;

    /**
     * @param int $domainId
     */
    public function setDomainId(int $domainId): void
    {
        $this->domainId = $domainId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'order';
    }

    /**
     * @return mixed[]
     */
    protected function loadStepsConfig(): array
    {
        return [
            [
                'skip' => true, // the 1st step is the shopping cart
                'form_options' => ['js_validation' => false],
            ],
            [
                'form_type' => TransportAndPaymentFormType::class,
                'form_options' => ['domain_id' => $this->domainId],
            ],
            [
                'form_type' => PersonalInfoFormType::class,
                'form_options' => ['domain_id' => $this->domainId],
            ],
        ];
    }

    /**
     * @return string
     */
    protected function determineInstanceId(): string
    {
        // Make instance ID constant as we do not want multiple instances of OrderFlow.
        return $this->getInstanceId();
    }

    /**
     * @param int $step
     * @param array $options
     * @return mixed[]
     */
    public function getFormOptions($step, array $options = []): array
    {
        $options = parent::getFormOptions($step, $options);

        // Remove default validation_groups by step.
        // Otherwise FormFactory uses is instead of FormType's callback.
        if (isset($options['validation_groups'])) {
            unset($options['validation_groups']);
        }

        return $options;
    }

    public function saveSentStepData(): void
    {
        $stepData = $this->retrieveStepData();

        foreach ($this->getSteps() as $step) {
            $stepForm = $this->createFormForStep($step->getNumber());
            if ($this->getRequest()->request->has($stepForm->getName())) {
                $stepData[$step->getNumber()] = $this->getRequest()->request->get($stepForm->getName());
            }
        }

        $this->saveStepData($stepData);
    }

    /**
     * @return bool
     */
    public function isBackToCartTransition(): bool
    {
        return $this->getRequestedStepNumber() === 2
            && $this->getRequestedTransition() === self::TRANSITION_BACK;
    }

    /**
     * @param mixed $formData
     */
    public function bind($formData): void
    {
        parent::bind($formData); // load current step number

        $firstInvalidStep = $this->getFirstInvalidStep();
        if ($firstInvalidStep === null || $this->getCurrentStepNumber() <= $firstInvalidStep->getNumber()) {
            return;
        }

        $this->changeRequestToStep($firstInvalidStep);

        parent::bind($formData); // load changed step
    }

    /**
     * @return \Craue\FormFlowBundle\Form\StepInterface|null
     */
    private function getFirstInvalidStep(): ?StepInterface
    {
        foreach ($this->getSteps() as $step) {
            if (!$this->isStepValid($step)) {
                return $step;
            }
        }

        return null;
    }

    /**
     * @param \Craue\FormFlowBundle\Form\StepInterface $step
     * @return bool
     */
    private function isStepValid(StepInterface $step): bool
    {
        $stepNumber = $step->getNumber();
        $stepsData = $this->retrieveStepData();
        if (array_key_exists($stepNumber, $stepsData)) {
            $stepForm = $this->createFormForStep($stepNumber);
            $stepForm->submit($stepsData[$stepNumber]); // the form is validated here
            return $stepForm->isValid();
        }

        return $step->getFormType() === null;
    }

    /**
     * @param \Craue\FormFlowBundle\Form\StepInterface $step
     */
    private function changeRequestToStep(StepInterface $step): void
    {
        $stepsData = $this->retrieveStepData();
        if (array_key_exists($step->getNumber(), $stepsData)) {
            $stepData = $stepsData[$step->getNumber()];
        } else {
            $stepData = [];
        }

        $request = $this->getRequest()->request;
        $requestParameters = $request->all();
        $requestParameters['flow_order_step'] = $step->getNumber();
        $requestParameters[$step->getFormType()] = $stepData;
        $request->replace($requestParameters);
    }
}
