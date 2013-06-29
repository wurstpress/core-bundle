<?php

namespace Wurstpress\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file','file')
        ;

        if($options['collection_type'])
            $builder->add('collection', $options['collection_type'], [ 'data' => $options['collection_id'] ]);
        else
            $builder->add('collection');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wurstpress\CoreBundle\Entity\Document',
            'csrf_protection' => false,
            'collection_type' => null,
            'collection_id' => null
        ));
    }

    public function getName()
    {
        return 'wurstpress_document';
    }
}
