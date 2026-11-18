<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Domain\Collection\DinosaursCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\UseCase\CreateDinosaur\Input;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class DinosaurResolver
{
    public function __construct(
        private DinosaursCollection $dinosaurs,
        private CommandBus          $commandBus,
        private Security           $security,
    )
    {
    }

    public function dinosaurs(?string $search): array
    {
        return $this->dinosaurs->search($search);
    }

    public function dinosaur(string $id): Dinosaur
    {
        $user = $this->security->getUser() ?? throw new \RuntimeException('User not authenticated');

        $isGranted = $this->security->isGranted('VIEW_DINOSAURS', $user);

        if (!$isGranted) {
            throw new \RuntimeException('User not granted');
        }

        return $this->dinosaurs->find($id)
            ?? throw new DinosaurNotFoundException($id);
    }

    public function createDinosaur(array|\ArrayObject $input): Dinosaur
    {
        $output = $this->commandBus->dispatch(
            new Input(
                $input['name'],
                $input['gender'],
                $input['speciesId'],
                $input['age'],
                $input['eyesColor'],
                new \DateTime()
            ),
        );

        return $output->dinosaur;
    }
}
