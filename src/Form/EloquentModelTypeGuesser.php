<?php

namespace WouterJ\EloquentBundle\Form;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess;

class EloquentModelTypeGuesser implements FormTypeGuesserInterface
{
    public function guessType($class, $property)
    {
        $model = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
        if (!$model instanceof Model) {
            return null;
        }

        $casts = $model->getCasts();
        switch ($casts[$property] ?? 'other') {
            case 'int':
            case 'integer':
                return new Guess\TypeGuess(IntegerType::class, [], Guess\TypeGuess::HIGH_CONFIDENCE);
            case 'real':
            case 'float':
            case 'double':
                return new Guess\TypeGuess(NumberType::class, [], Guess\TypeGuess::MEDIUM_CONFIDENCE);
            case 'string':
                return new Guess\TypeGuess(TextType::class, [], Guess\TypeGuess::MEDIUM_CONFIDENCE);
            case 'bool':
            case 'boolean':
                return new Guess\TypeGuess(CheckboxType::class, [], Guess\TypeGuess::HIGH_CONFIDENCE);
            case 'date':
                return new Guess\TypeGuess(DateType::class, [], Guess\TypeGuess::HIGH_CONFIDENCE);
            case 'datetime':
                return new Guess\TypeGuess(DateTimeType::class, [], Guess\TypeGuess::HIGH_CONFIDENCE);
            case 'timestamp':
                return new Guess\TypeGuess(TimeType::class, [], Guess\TypeGuess::MEDIUM_CONFIDENCE);
            default:
                return new Guess\TypeGuess(TextType::class, [], Guess\TypeGuess::LOW_CONFIDENCE);
        }
    }

    public function guessRequired($class, $property)
    {
        return null;
    }

    public function guessMaxLength($class, $property)
    {
        return null;
    }

    public function guessPattern($class, $property)
    {
        return null;
    }
}
