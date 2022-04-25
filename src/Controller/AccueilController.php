<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Produit;
use App\Form\ImageType;
use App\Form\ProduitType;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

//#[Route('/test')]
class AccueilController extends AbstractController
{
    #[Route('/param/{id}', name: 'test')]
    public function index($id, MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('symfony@ein.school')
            ->to('guillaume.thiery@adenformations.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        $url1 = $this->generateUrl('test', array('id' => 'urlTest'));
        $url2 = $this->generateUrl('test', array('id' => 'urlTest'), UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('accueil/index.html.twig', [
            'param' => $id,
            'url1' => $url1,
            'url2' => $url2
        ]);
    }

    #[Route('/param2/{id}',requirements:['id' => "\d+"], name: 'param2')]
    public function index2($id = 1234): Response
    {
        return $this->render('accueil/index2.html.twig', [
            'param' => $id,
        ]);
    }

    #[Route('/param3/{id<\d+>}', name: 'param3')]
    public function index3($id = 1234): Response
    {
        return $this->render('accueil/index2.html.twig', [
            'param' => $id,
        ]);
    }

    #[Route('/', name: 'produits')]
    public function listeProduits(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $listeProduits = $repository->findAll();
        return $this->render('accueil/listeProduits.html.twig', [
            'listeProduits' => $listeProduits,
        ]);
    }

    #[Route('/produit/{id}', name: 'produit')]
    public function afficheProduit($id, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Produit::class);
        $produit = $repository->find($id);
        return $this->render('accueil/afficherProduit.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/mes-produits/{id}', name: 'mes-produit')]
    public function mesProduit($id): Response
    {
        return new Response("Informations sur les produits de l'utilisateur n°$id");
    }

    #[Route('/mes-produits/{id}/{idProduit}', name: 'mes-produit2')]
    public function mesProduit2($id, $idProduit): Response
    {
        return new Response("Informations sur le produit $idProduit de l'utilisateur n°$id");
    }

    #[Route('/mes-achats/{annee}', name: 'mes-achats')]
    public function mesAchats($annee): Response
    {
        return new Response("Liste des produits achetés en $annee");
    }

    #[Route('/mes-achats/{annee}/{idUtilisateur}', name: 'mes-achats2')]
    public function mesAchats2($annee, $idUtilisateur): Response
    {
        return new Response("Liste des produits achetés en $annee par l'utilisateur n°$idUtilisateur");
    }

    #[Route('/newproduit', name: 'newproduit')]
    public function newProduit(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            $produit = $form->getData();
            //Gérer l'upload des images
            $imagesCollection = $form->get('images');
            foreach($imagesCollection as $imageForm){
                /** @var UploadedFile $image */ 
                $image = $imageForm->getData();
                $imageUrl = $imageForm->get('url')->getData();
                if($imageUrl){
                    $newImageName = uniqid().".".$imageUrl->guessExtension();
                    try{
                            $imageUrl->move(
                                $this->getParameter('repimage'),
                                $newImageName
                            );
                    }
                    catch(e){  }
                    $image->setUrl($this->getParameter('prefixeimage').$newImageName);
                }
                $entityManager->persist($image);
            }

            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash("success", "Votre produit a bien été créé");
            return $this->redirectToRoute("produits",[ ]);
        }        
        return $this->render('accueil/newProduit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/newimage', name: 'newimage')]
    public function newImage(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            /** @var UploadedFile $image */ 
            $image = $form->getData();
            $imageUrl = $form->get('file')->getData();
            if($imageUrl){
                $newImageName = uniqid().".".$imageUrl->guessExtension();
                try{
                        $imageUrl->move(
                            $this->getParameter('repimage'),
                            $newImageName
                        );
                }
                catch(e){  }
                $image->setUrl($this->getParameter('prefixeimage').$newImageName);
            }
            $entityManager->persist($image);
            $entityManager->flush();
            $this->addFlash("success", "Votre image a bien été créée");
            return $this->redirectToRoute("produits",[ ]);
        }        
        return $this->render('accueil/newImage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/editproduit/{id}', name: 'editproduit')]
    public function editProduit(Request $request, ManagerRegistry $doctrine, Produit $produit): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            $produit = $form->getData();
            //Gérer l'upload des images
            $imagesCollection = $form->get('images');
            foreach($imagesCollection as $imageForm){
                /** @var UploadedFile $image */ 
                $image = $imageForm->getData();
                $imageUrl = $imageForm->get('file')->getData();
                if($imageUrl){
                    $newImageName = uniqid().".".$imageUrl->guessExtension();
                    try{
                            $imageUrl->move(
                                $this->getParameter('repimage'),
                                $newImageName
                            );
                    }
                    catch(e){  }
                    $image->setUrl($this->getParameter('prefixeimage').$newImageName);
                }
                $entityManager->persist($image);
            }

            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash("success", "Votre produit a bien été modifié");
            return $this->redirectToRoute("produits",[ ]);
        }        
        return $this->render('accueil/newProduit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

    #[IsGranted('ROLE_USER')]
    #[Route('/deleteproduit/{id}', name: 'deleteproduit')]
    public function deleteProduit(Request $request, ManagerRegistry $doctrine, Produit $produit): Response
    {
        //On récupère le produit
        //$repository = $doctrine->getRepository(Produit::class);
        //$produit = $repository->find($id);
        //On récupère l'entityManager
        $entityManager = $doctrine->getManager();
        //On indique à l'entityManager de supprimer le produit de la BDD
        $entityManager->remove($produit);
        $entityManager->flush();
        //On redirige vers la liste des produits
        return $this->redirectToRoute("produits",[ ]);
    }
}
