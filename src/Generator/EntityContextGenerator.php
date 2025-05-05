<?php

namespace AiContextBundle\Generator;

use Doctrine\Persistence\ManagerRegistry;

class EntityContextGenerator
{
    /**
     * @param array<int, string> $entityPaths
     */
    public function __construct(
        // @phpstan-ignore-next-line
        private readonly array $entityPaths,
        private readonly ManagerRegistry $doctrine
    )
    {
    }

    /**
     * @return array<int, array{
     *     entity: class-string,
     *     fields: array<string, string|null>,
     *     associations: array<string, string>
     * }>
     */
    public function generate(): array
    {
        $entityManager = $this->doctrine->getManager();
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();

        $result = [];

        /** @var \Doctrine\ORM\Mapping\ClassMetadata<object> $classMetadata */
        foreach ($metaData as $classMetadata) {
            $entityName = $classMetadata->getName();
            $fields = [];
            foreach ($classMetadata->getFieldNames() as $field) {
                $fields[$field] = $classMetadata->getTypeOfField($field);
            }

            $associations = [];

            foreach ($classMetadata->getAssociationMappings() as $assocName => $assocData) {
                $type = match ($assocData['type']) {
                    1 => 'OneToOne',
                    2 => 'ManyToOne',
                    4 => 'OneToMany',
                    8 => 'ManyToMany',
                    default => 'Unknown',
                };
                $associations[$assocName] = $type . ' => ' . $assocData['targetEntity'];
            }

            $result[] = [
                'entity' => $entityName,
                'fields' => $fields,
                'associations' => $associations,
            ];
        }

        return $result;
    }
}
