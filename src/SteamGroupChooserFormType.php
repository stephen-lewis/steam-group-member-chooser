<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SteamGroupChooserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupName', TextType::class, [
                'label' => 'Steam Group Name',
            ]);
    }
}
