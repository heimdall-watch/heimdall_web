<?php

namespace App\Handler;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class EntityIdHandler implements SubscribingHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'EntityId',
                'method' => 'deserialize',
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'EntityId',
                'method' => 'serialize',
            ],
        ];
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param object|null $entity
     * @param array $type
     * @param Context $context
     * @return mixed
     * @throws \Exception
     */
    public function serialize(JsonSerializationVisitor $visitor, $entity, array $type, Context $context)
    {
        $entityName = $this->getEntityName($type);

        if ($entity === null) {
            return null;
        }

        if (!is_object($entity)) {
            throw new \Exception(sprintf('Value of @JMS\Type("EntityId<\'%s\'>") must me object of null, %s given.', $entityName, gettype($entity)));
        }

        $identifier = $this->getEntityIdentifier($type);
        $getter = 'get' . ucfirst($identifier);
        $id = $entity->$getter();

        return $id;
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param $id
     * @param array $type
     * @param Context $context
     * @return object|null
     * @throws \Exception
     */
    public function deserialize(JsonDeserializationVisitor $visitor, $id, array $type, Context $context)
    {
        if ($id === null) {
            return null;
        }

        $entityName = $this->getEntityName($type);
        if ($entity = $this->em->find($entityName, $id)) {
            return $entity;
        }

        return null;
    }

    /**
     * @param array $type
     * @return string
     * @throws \Exception
     */
    private function getEntityName(array $type)
    {
        if (!\is_array($type['params']) || \count($type['params']) === 0) {
            throw new \Exception('You must specify entityName in @JMS\Type("EntityId<\'entity:name\'>") annotation.');
        }

        $entityName = $type['params'][0]['name'];

        return $entityName;
    }

    /**
     * @param array $type
     * @return string
     * @throws \Exception
     */
    private function getEntityIdentifier(array $type)
    {
        $entityName = $this->getEntityName($type);

        if (!$classMetadata = $this->em->getClassMetadata($entityName)) {
            throw new \Exception(sprintf('Can\'t find metadata for class %s', $entityName));
        }

        $identifiers = $classMetadata->getIdentifier();
        if (count($identifiers) != 1) {
            throw new \Exception(sprintf('@JMS\Type("EntityId<>") supports entities with only one identifier, %s contains %s identifier(s).', $entityName, count($identifiers)));
        }

        $identifier = $identifiers[0];

        return $identifier;
    }
}