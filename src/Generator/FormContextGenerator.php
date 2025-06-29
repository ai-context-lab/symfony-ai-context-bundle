<?php

namespace AiContextBundle\Generator;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Finder\Finder;

class FormContextGenerator
{
    /**
     * @param string[] $formPaths
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        private readonly array                $formPaths,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function generate(): array
    {
        $forms = [];

        foreach ($this->getFormTypeClasses() as $formClass) {
            try {
                $forms[] = $this->extract($formClass);
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $forms;
    }

    /**
     * @return array<string, mixed>
     */
    public function extract(string $formClass): array
    {
        if (!class_exists($formClass)) {
            throw new \InvalidArgumentException("Form type '$formClass' does not exist.");
        }

        /** @var FormTypeInterface<mixed> $formType */
        $formType = new $formClass();

        /** @var class-string<FormTypeInterface<mixed>> $formClass */
        $form = $this->formFactory->create($formClass);

        $fields = [];
        foreach ($form as $name => $child) {
            $config = $child->getConfig();

            $fields[$name] = [
                'type'     => $this->getShortTypeName($config->getType()->getInnerType()::class),
                'required' => $config->getRequired(),
                'mapped'   => $config->getMapped(),
                'options'  => $this->normalizeOptions($config->getOptions()),
            ];
        }

        $resolver = new OptionsResolver();
        $formType->configureOptions($resolver);
        $options = $resolver->resolve([]);

        return [
            'class'      => $formClass,
            'data_class' => $options['data_class'] ?? null,
            'fields'     => $fields,
        ];
    }

    /**
     * @return array<class-string>
     */
    private function getFormTypeClasses(): array
    {
        $finder = new Finder();
        $finder->files()->in($this->formPaths)->name('*.php');

        $classes = [];
        foreach ($finder as $file) {
            $fqcn = $this->resolveClassFromFile($file->getRealPath());

            if (
                $fqcn &&
                class_exists($fqcn) &&
                is_subclass_of($fqcn, \Symfony\Component\Form\AbstractType::class)
            ) {
                $classes[] = $fqcn;
            }
        }

        return $classes;
    }

    private function resolveClassFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);

        if ($contents === false) {
            return null;
        }

        if (!preg_match('/namespace (.+);/', $contents, $namespaceMatch)) {
            return null;
        }

        if (!preg_match('/class (\w+)/', $contents, $classMatch)) {
            return null;
        }

        return $namespaceMatch[1] . '\\' . $classMatch[1];
    }

    private function getShortTypeName(string $fqcn): string
    {
        return str_contains($fqcn, '\\') ? substr($fqcn, strrpos($fqcn, '\\') + 1) : $fqcn;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function normalizeOptions(array $options): array
    {
        $normalized = [];

        foreach ($options as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $normalized[$key] = $value;
            } elseif (is_array($value)) {
                $normalized[$key] = array_map(fn($v) => is_scalar($v) ? $v : gettype($v), $value);
            } elseif (is_object($value)) {
                $normalized[$key] = get_class($value);
            } else {
                $normalized[$key] = gettype($value);
            }
        }

        return $normalized;
    }
}
