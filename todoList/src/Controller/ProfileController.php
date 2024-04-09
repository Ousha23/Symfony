<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfileFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/profil/{id}', name: 'app_profile')]
    public function index(Users $user, EntityManagerInterface $entityManager, UsersRepository $usersRepository, Request $request,): Response
    {
        $userId = $request->attributes->get('id');
        $user= $usersRepository->findOneBy(['id' => $userId]);
        return $this->renderForm('profile/index.html.twig', [
            'user' => $user,]);
    }

    #[Route('/profil/edit/{id}', name: 'profil_edit')]
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $profileForm = $this->createForm(ProfileFormType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $oldPassword = $profileForm->get('oldPassword')->getData();;
            $newPassword = $profileForm->get('plainPassword')->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('danger', 'Ancien mot de passe incorrect');
                return $this->redirectToRoute('profil_edit', ['id' => $user->getId()]);
            }
            if ($newPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été modifié avec succès');
            return $this->redirectToRoute('app_profile', ['id'=> $user->getId()]);
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $profileForm->createView(),
        ]);
    }
}


    