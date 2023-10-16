<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


/**
* @Route("/video")
*/
class VideoController extends AbstractController
{
    /**
     * @Route("/envoyer", name="envoyer_video")
     */
    public function envoyerVideo(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('video', FileType::class, [
                'label' => "Sélectionnez une vidéo :",
                'constraints' => [
                    new File([
                        'maxSize' => '1024M', // Taille maximale du fichier
                        'mimeTypes' => [
                            'video/*', // Types MIME de vidéos acceptés
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier vidéo valide.',
                    ]),
                ],
            ])

            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video = $form->get('video')->getData();

            // Récupérer le nom original du fichier
            $originalFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);

            // Récupérer l'extension du fichier
            $extension = $video->guessExtension(); // Cette méthode utilise FileInfo pour deviner l'extension

            // Gérer l'enregistrement de la vidéo dans le système de fichiers ou en base de données
            $newFilename = uniqid(). '.' . $extension;

            $video->move(
                $this->getParameter('upload_dir'),
                $newFilename
            );
                
            return $this->redirectToRoute('afficher_video', ['video' => substr($newFilename , 0 , -4  )]);

        }

        return $this->render('video/envoyer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/afficher/{video}", name="afficher_video")
     */
    public function afficherVideo(string $video)
    {
        // Récupérer la vidéo depuis le système de fichiers ou la base de données

        return $this->render('video/afficher.html.twig', [
            'video' => $video.".mp4",
        ]);
    }

}