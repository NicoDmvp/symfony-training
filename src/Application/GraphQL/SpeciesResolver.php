<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Domain\Collection\SpeciesCollection;
use Domain\Exception\SpeciesNotFoundException;
use Domain\Model\Species;
use Domain\UseCase\CreateSpecies\Input;

final readonly class SpeciesResolver
{
    public function __construct(
        private SpeciesCollection $species,
        private CommandBus          $commandBus,
    )
    {
    }

    public function speciesSearch(?string $search): array
    {
        return $this->species->search($search);
    }

    public function species(string $id): Species
    {
        return $this->species->find($id)
            ?? throw new SpeciesNotFoundException($id);
    }

    public function createSpecies(array|\ArrayObject $input): Species
    {
        $output = $this->commandBus->dispatch(
            new Input(
                $input['name'],
                $input['habitats'],
                $input['feeding'],
            ),
        );

        return $output->species;
    }
}
