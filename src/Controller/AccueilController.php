<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//#[Route('/test')]
class AccueilController extends AbstractController
{
    #[Route('/param/{id}', name: 'test')]
    public function index($id): Response
    {
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

    #[Route('/produits', name: 'produits')]
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
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute("produits",[ ]);
        }        
        return $this->render('accueil/newProduit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
