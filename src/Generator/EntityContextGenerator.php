<?php

namespace AiContextBundle\Generator;

use Doctrine\Persistence\ManagerRegistry;

class EntityContextGenerator
{
    public function __construct(
        private readonly array $entityPaths,
        private readonly ManagerRegistry $doctrine
    )
    {
    }

    public function generate(): array
    {
        $entityManager = $this->doctrine->getManager();
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();

        $result = [];

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
