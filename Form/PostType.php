<?php

namespace Wurstpress\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tags = '';

        foreach($options['data']->getTags() as $tag)
            $tags .= $tag . ',';

        $builder
            ->add('title')
            ->add('content','ckeditor', ['config_name' => 'minimal'])
            ->add('tags','text', [ 'mapped' => false, 'data' => $tags, 'required' => false ])
            ->add('collection')
            ->add('created', 'date', [ 'widget' => 'single_text' ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wurstpress\CoreBundle\Entity\Post'
        ));
    }

    public function getName()
    {
        return 'wurstpress_post';
    }
}
