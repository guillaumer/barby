<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class LoginType
 *
 * @package App\Form\Type
 */
class LoginType extends AbstractType {
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$app = $options['app'];

		$builder
			->add('_username', TextType::class, array(
				'label'       => 'Username',
				'constraints' => array(
					new Assert\NotBlank(),
				),
				'data'        => $app['session']->get('_security.last_username'),
				'attr' => array(
					'class' => 'form-control',
                    'placeholder' => 'Username'
				)
			))
			->add('_password', PasswordType::class, array(
				'label'       => 'Password',
				'constraints' => array(
					new Assert\NotBlank()
				),
				'attr' => array(
					'class' => 'form-control',
                    'placeholder' => 'Password'
				)
			));
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		parent::setDefaultOptions($resolver);
		$resolver->setRequired(array("app"));
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'login';
	}

	/**
	 * Supprime le nom du form dans les noms de champs (pour matcher provider sécurité)
	 *
	 * @return null
	 */
	public function getBlockPrefix()
	{
		return null;
	}
}
