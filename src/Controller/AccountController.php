<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class AccountController extends AbstractController
{
    /**
     * @param UserRepository $repository
     * @return Response
     */
    #[Route('/account', name: 'app_account')]
    public function index(UserRepository $repository): Response
    {
        $users = $repository->findAll();

        return $this->render('account/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @param UserRepository $repository
     * @param int|null $id
     * @return Response
     */
    #[Route('/account/edit/{id?}', name: 'app_account_edit')]
    public function edit(UserRepository $repository, ?int $id = null): Response
    {
        if (null === $id) {
            $user = new User();
        } else {
            $user = $repository->find($id);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param int|null $id
     * @return Response
     */
    #[Route('/account/update/{id?}', name: 'app_account_store')]
    public function store(Request $request, ManagerRegistry $doctrine, ?int $id = null): Response
    {
        if (null === $id) {
            $user = new User();
            $user->setPassword("123");
        } else {
            $repository = $doctrine->getRepository(User::class);

            /** @var User $user */
            $user = $repository->find($id);
        }

        $user->setName($request->request->get('name'));
        $user->setEmail($request->request->get('email'));

        $manager = $doctrine->getManager();
        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('app_account');
    }


    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    #[Route('/account/delete/{id}', name: 'app_account_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(User::class);

        /** @var User $user */
        $user = $repository->find($id);

        $manager = $doctrine->getManager();
        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('app_account');
    }
}
