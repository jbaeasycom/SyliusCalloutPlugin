<?php

declare(strict_types=1);

namespace Setono\SyliusCalloutPlugin\Form\Type;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfigurationCollectionType extends AbstractType
{
    protected ServiceRegistryInterface $registry;

    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $prototypes = [];
        foreach (array_keys($this->registry->all()) as $type) {
            $formBuilder = $builder->create(
                $options['prototype_name'],
                $options['entry_type'],
                array_replace(
                    $options['entry_options'],
                    ['configuration_type' => $type]
                )
            );

            $prototypes[$type] = $formBuilder->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['prototypes'] = [];

        foreach ($form->getConfig()->getAttribute('prototypes') as $type => $prototype) {
            /** @var FormInterface $prototype */
            $view->vars['prototypes'][$type] = $prototype->createView($view);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'error_bubbling' => false,
        ]);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
