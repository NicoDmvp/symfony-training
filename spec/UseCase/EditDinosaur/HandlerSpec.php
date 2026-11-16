<?php

namespace spec\Domain\UseCase\EditDinosaur;

use Domain\Collection\DinosaursCollection;
use Domain\Collection\SpeciesCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\Model\Species;
use Domain\UseCase\EditDinosaur\Output;
use Domain\UseCase\EditDinosaur\Handler;
use Domain\UseCase\EditDinosaur\Input;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class HandlerSpec extends ObjectBehavior
{
    function let(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species
    ): void {

        $this->beConstructedWith(
            $dinosaurs,
            $species,
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Handler::class);
    }

    function it_update_a_dinosaur(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
        Dinosaur $dinosaur,
    ): void
    {
        $specie = new Species('Tyrannosaurus', ['forest'], 'carnivore');

        $dinosaurs->find(1)->willReturn(
            $dinosaur
        );

        $species->find(1)->willReturn(
            $specie
        );

        $birthdate = new \DateTime();

        $dinosaur->setName('Tyrannosaurus')->shouldBeCalledOnce();
        $dinosaur->setGender('male')->shouldBeCalledOnce();
        $dinosaur->setSpecies($specie)->shouldBeCalledOnce();
        $dinosaur->setAge(12)->shouldBeCalledOnce();
        $dinosaur->setEyesColor('#ff0000')->shouldBeCalledOnce();
        $dinosaur->setBirthdate($birthdate)->shouldBeCalledOnce();

        $this(new Input(1, 'Tyrannosaurus', 'male', '1', 12, '#ff0000', $birthdate))
            ->shouldHaveType(Output::class);
    }

    function it_rejects_an_unknown_species(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
    ): void {
        $dinosaurs->find(42)->willReturn(null);
        $species->find('1')->willReturn(null);

        $this
            ->shouldThrow(DinosaurNotFoundException::class)
            ->during('__invoke', [
                new Input(42, 'Rex', 'male', '1', 12, '#ff0000', new \DateTime()),
            ]);
    }
    function it_changes_the_birthdate(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
    ): void {
        $trex = new Species('T-Rex', ['forest'], 'carnivore');
        $dinosaur = new Dinosaur('Rex', 'male', $trex, 10, '#0f0');
        $dinosaurs->find('42')->willReturn($dinosaur);
        $species->find('1')->willReturn($trex);

        $birthdate = new \DateTime('2016-05-01');

        $this(new Input(
            '42', 'Rex', 'male', '1', 10, '#0f0', $birthdate,
        ));

        if ($birthdate != $dinosaur->getBirthdate()) {
            throw new FailureException('The birthdate was not changed');
        }
    }

}
