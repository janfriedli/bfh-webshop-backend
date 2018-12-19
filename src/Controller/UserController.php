<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

class UserController extends FOSRestController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService= $userService;
    }


    /**
     * Creates a User
     * @param User $user
     * @param ConstraintViolationListInterface $validationErrors
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Rest\Post("/register")
     * @return View
     *
     * @SWG\Tag(name="User")
     * @SWG\Response(
     *     response=201,
     *     description="Creates a new User",
     *     @SWG\Schema(ref=@Model(type=User::class))
     * )
     * @SWG\Parameter(
     * 		name="User",
     * 		in="body",
     * 		required=true,
     * 		@SWG\Schema(ref=@Model(type=User::class)),
     * )
     */
    public function postUser(User $user, ConstraintViolationListInterface $validationErrors): View
    {
        if (count($validationErrors) > 0) {
            return View::create($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        $this->userService->create($user);
        return View::create([],Response::HTTP_CREATED);
    }

}