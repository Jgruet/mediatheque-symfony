<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;


class UserType extends AbstractType
{

    private $role;

    const SUPER_ADMIN = "ROLE_ADMIN";

    //public function __construct(TokenStorageInterface $utts)
    public function __construct(Security $security)
    {
        //$this->role = $utts->getToken()->getUser()->getRoles();
        $this->role = $security->getUser()->getRoles();
    }



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            /* ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'user' => 'ROLE_USER',
                    'employee' => 'ROLE_EMPLOYEE',
                    'librarian' => 'ROLE_LIBRARIAN',
                    'admin' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
            ]) */
            ->add('password')
            ->add('email')
            ->add('isVerified');

        // si super_admin... va faloir utiliser un service symfony
        if (in_array(self::SUPER_ADMIN, $this->role)) {
            $builder->add('roles', ChoiceType::class, [
                'choices'  => [
                    'user' => 'ROLE_USER',
                    'employee' => 'ROLE_EMPLOYEE',
                    'librarian' => 'ROLE_LIBRARIAN',
                    'admin' => 'ROLE_ADMIN',
                ],
                /* 'expanded' => true, */
                'multiple' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
