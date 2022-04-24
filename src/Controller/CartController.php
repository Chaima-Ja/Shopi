<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index")
     */
    public function index(SessionInterface $session, SongRepository $productsRepository)
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $product = $productsRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getLength() * $quantite;
        }

        return $this->render('cart/index.html.twig', compact("dataPanier", "total"));
    }

    /**
     * @Route("/add/{id}", name="cart_add")
     */
    public function add(Song $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/remove/{id}", name="cart_remove")
     */
    public function remove(Song $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/delete/{id}", name="cart_delete")
     */
    public function delete(Song $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/delete", name="cart_delete_all")
     */
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("cart_index");
    }

}