<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserService
{
    /**
     * @var ServiceEntityRepository
     */
    private $userRepository;

    /**
     * @var CRUDService $crud
     */
    private $crud;

    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    /**
     * ProductService constructor.
     * @param UserRepository $userRepository
     * @param CRUDService $crud
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserRepository $userRepository,
        CRUDService $crud,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->crud = $crud;
    }


    /**
     * @param User $user
     * @return object
     */
    public function create(User $user)
    {
        if (getenv("REGISTER_TOKEN") !== $user->getRegisterToken()) {
            throw new BadRequestHttpException("Wrong register token");
        }

        if ($this->userRepository->findByUsername($user->getUsername())) {
            throw new BadRequestHttpException("Username is already taken!");
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user,$user->getPassword()));
        $this->crud->save($user);
    }


}