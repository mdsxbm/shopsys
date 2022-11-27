<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FileAllowedExtension extends Constraint
{
    /**
     * @var string
     */
    public $message = 'File extension {{ value }} is not between allowed extension. Allowed extensions are {{ extensions }}.';

    /**
     * @var string[]
     */
    public $extensions;

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return [
            'extensions',
        ];
    }

    /**
     * @return string
     */
    public function getDefaultOption(): string
    {
        return 'extensions';
    }
}
