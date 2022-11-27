<?php

namespace Shopsys\FrameworkBundle\Form\Admin\PromoCode;

use Shopsys\FrameworkBundle\Form\DisplayOnlyType;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PromoCodeFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeFacade
     */
    private $promoCodeFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode|null
     */
    private $promoCode;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeFacade $promoCodeFacade
     */
    public function __construct(PromoCodeFacade $promoCodeFacade)
    {
        $this->promoCodeFacade = $promoCodeFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->promoCode = $options['promo_code'];

        if ($this->promoCode instanceof PromoCode) {
            $builder->add('formId', DisplayOnlyType::class, [
                'label' => t('ID'),
                'data' => $this->promoCode->getId(),
            ]);
        }

        $builder
            ->add('code', TextType::class, [
                'label' => t('Code'),
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter code',
                    ]),
                    new Constraints\Callback([$this, 'validateUniquePromoCode']),
                ],
            ])
            ->add('percent', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter discount percentage',
                    ]),
                    new Constraints\Range([
                        'min' => 0,
                        'max' => 100,
                    ]),
                ],
                'invalid_message' => 'Please enter whole number.',
                'label' => t('Discount (%)'),
            ]);

        $builder->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['promo_code'])
            ->setAllowedTypes('promo_code', [PromoCode::class, 'null'])
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }

    /**
     * @param string $promoCodeValue
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    public function validateUniquePromoCode(string $promoCodeValue, ExecutionContextInterface $context): void
    {
        if ($this->promoCode === null || $promoCodeValue !== $this->promoCode->getCode()) {
            $promoCode = $this->promoCodeFacade->findPromoCodeByCode($promoCodeValue);

            if ($promoCode !== null) {
                $context->addViolation('Promo code with this code already exists');
            }
        }
    }
}
