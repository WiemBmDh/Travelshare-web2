<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Reclamations;
use App\Entity\Users;
use App\Form\ReclamationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        // Crée un objet "Reclamation" pour la réclamation à soumettre
        $reclamation = new Reclamations();
        $form = $this->createForm(ReclamationFormType::class, $reclamation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement de la réclamation dans la base, associée à l'utilisateur qui la soumet
            $reclamation->setUser($this->getUser()); // L'utilisateur qui soumet la réclamation
            $em->persist($reclamation);
            $em->flush();

            // **Récupération de l'admin** (l'utilisateur avec role = 1)
            $admin = $em->getRepository(Users::class)->findOneBy(['role' => 1]);

            // Création de la notification pour l'admin
            $notification = new Notification();
            $notification->setMessage('Une nouvelle réclamation a été envoyée par ' . $this->getUser()->getName()); // Message de la notification
            $notification->setIsRead(false);  // La notification est marquée comme non lue
            $notification->setCreatedAt(new \DateTime()); // Date de création de la notification
            $notification->setUpdatedAt(new \DateTime()); // Date de mise à jour (si besoin)
            $notification->setUser($admin);  // Lier la notification à l'admin, pas à l'utilisateur qui a soumis la réclamation

            // Sauvegarder la notification dans la base
            $em->persist($notification);
            $em->flush();

            // Redirection après soumission réussie
            return $this->redirectToRoute('app_list_rec_user');
        }

        // Récupérer les notifications de l'utilisateur connecté (dans ce cas, l'admin)
        $notifications = $em->getRepository(Notification::class)->findBy([
            'user' => $this->getUser(), // Nous affichons les notifications pour l'admin connecté
        ], ['createdAt' => 'DESC']); // Trier par date de création (descendant)

        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
            'notifications' => $notifications, // Passer les notifications à la vue
        ]);
    }

}


