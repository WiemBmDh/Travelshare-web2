<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Reclamations;
use App\Entity\Users;
use App\Form\ReclamationFormType;
use App\Service\ProfanityFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function ajouter(Request $request, EntityManagerInterface $em, ProfanityFilter $profanityFilter): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $reclamation = new Reclamations();
        $form = $this->createForm(ReclamationFormType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $reclamation->getTitle();
            $description = $reclamation->getDescription();

            if ($profanityFilter->containsProfanity($title) || $profanityFilter->containsProfanity($description)) {
                // Stockez les mots inappropriés trouvés pour les afficher
                $badWords = array_intersect(
                    array_merge(
                        $profanityFilter->findProfanities($title),
                        $profanityFilter->findProfanities($description)
                    ),
                    $profanityFilter->getProfanityWords()
                );

                $this->addFlash('warning', sprintf(
                    'Votre message contient des termes inappropriés (%s). Veuillez reformuler.',
                    implode(', ', array_unique($badWords))
                ));

                return $this->redirectToRoute('app_reclamation');
            }

            // Enregistrement normal si pas de mots inappropriés
            $reclamation->setUser($this->getUser());
            $reclamation->setDateReclamation(new \DateTime());

            $em->persist($reclamation);
            $em->flush();

            $this->addFlash('success', 'Votre réclamation a été envoyée avec succès!');
            return $this->redirectToRoute('app_list_rec_user');
        }

        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
            'profanity_words' => $profanityFilter->getProfanityWords()
        ]);
    }
}