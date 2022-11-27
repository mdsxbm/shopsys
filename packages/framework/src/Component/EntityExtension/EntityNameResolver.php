<?php

namespace Shopsys\FrameworkBundle\Component\EntityExtension;

use ReflectionObject;

class EntityNameResolver
{
    /**
     * @var array<class-string, class-string>
     */
    protected $entityExtensionMap;

    /**
     * @param array<class-string, class-string> $entityExtensionMap
     */
    public function __construct(array $entityExtensionMap)
    {
        $this->entityExtensionMap = $entityExtensionMap;
    }

    /**
     * @param string $entityName
     * @return class-string
     */
    public function resolve(string $entityName): string
    {
        return $this->entityExtensionMap[$entityName] ?? $entityName;
    }

    /**
     * @param mixed $subject
     * @return mixed
     */
    public function resolveIn(mixed $subject): mixed
    {
        if (is_string($subject)) {
            return $this->resolveInString($subject);
        }

        if (is_array($subject)) {
            return $this->resolveInArray($subject);
        }

        if (is_object($subject)) {
            $this->resolveInObjectProperties($subject);
        }

        return $subject;
    }

    /**
     * Replace every occurrence of the original FQNs with word borders on both sides and not followed by a back-slash
     *
     * @param string $string
     * @return string
     */
    protected function resolveInString(string $string): string
    {
        foreach ($this->entityExtensionMap as $originalEntityName => $extendedEntityName) {
            $pattern = '~\b' . preg_quote($originalEntityName, '~') . '\b(?!\\\\)~u';
            $string = preg_replace($pattern, $extendedEntityName, $string);
        }

        return $string;
    }

    /**
     * @param mixed[] $array
     * @return mixed[]
     */
    protected function resolveInArray(array $array): array
    {
        return array_map([$this, 'resolveIn'], $array);
    }

    /**
     * Resolve entity names recursively in all properties of the subject (even private ones)
     *
     * @param object $object
     */
    protected function resolveInObjectProperties(object $object): void
    {
        $reflection = new ReflectionObject($object);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            $resolvedValue = $this->resolveIn($value);
            $property->setValue($object, $resolvedValue);
        }
    }
}
