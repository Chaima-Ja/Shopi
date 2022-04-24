<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use App\Repository\SongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/song")
 */
class SongController extends AbstractController
{
    /**
     * @Route("/", name="song_index", methods={"GET"})
     */
    public function index(SongRepository $songRepository, Request $request, PaginatorInterface $paginator)
    {
        $songs= $paginator->paginate(
            $songRepository-> findAllSongs(),
            $request->query->getInt('page',1),
            8
        );
        return $this->render('home/allsongs.html.twig', [
            'songs' => $songs,
        ]);
        // return $this->render('products/index.html.twig', [
        //     'products' => $productsRepository->findAll()
        // ]);
        /*return $this->render('song/index.html.twig', [
            'songs' => $songRepository->findAll(),
        ]);*/
    }

    /**
     * @Route("/femme", name="song_femme", methods={"GET"})
     */
     public function femme(SongRepository $songRepository, Request $request, PaginatorInterface $paginator)
     {
         $songs= $paginator->paginate(
             $songRepository-> findAllSongs(),
             $request->query->getInt('page',1),
             8
         );
         return $this->render('cat/femme.html.twig', [
             'songs' => $songs,
             
         ]);
     }
     /**
     * @Route("/homme", name="song_homme", methods={"GET"})
     */
    public function homme(SongRepository $songRepository, Request $request, PaginatorInterface $paginator)
    {
        $songs= $paginator->paginate(
            $songRepository-> findAllSongs(),
            $request->query->getInt('page',2),
            8
        );
        return $this->render('cat/homme.html.twig', [
            'songs' => $songs,
        ]);
    }

    /**
     * @Route("/index1", name="song_index1", methods={"GET"})
     */
    public function index1(SongRepository $songRepository, Request $request, PaginatorInterface $paginator)
    {
        $songs= $paginator->paginate(
            $songRepository-> findAllSongs(),
            $request->query->getInt('page',1),
            8
        );
        return $this->render('song/index.html.twig', [
            'songs' => $songs,
        ]);
    }



    

    /**
     * @Route("/new", name="song_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($song->getCoverImage()=="")
            $song->setCoverImage("no_image_jpg.jpg");
             else 
            {
            $file= $song->getCoverImage();
            $fileName= md5(uniqid()).'.'.$file ->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $song->setCoverImage($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($song);
            $entityManager->flush();

            return $this->redirectToRoute('song_index');
        }

        return $this->render('song/new.html.twig', [
            'song' => $song,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="song_show", methods={"GET"})
     */
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', [
            'song' => $song,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="song_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Song $song): Response
    {
        $name= $song->getCoverImage();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($song->getCoverImage()=="")
            $song->setCoverImage($name);
             else 
             {
         $file= new File($song->getCoverImage());
         $fileName= md5(uniqid()).'.'.$file ->guessExtension();
         $file->move($this->getParameter('images_directory'), $fileName);
         $song->setCoverImage($fileName);

                   if($name !="no_image_jpg.jpg")
                     if(file_exists("uploads/images/".$name))
                     unlink("uploads/images/".$name);
             }


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('song_index');
        }

        return $this->render('song/edit.html.twig', [
            'song' => $song,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="song_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Song $song): Response
    {
        if ($this->isCsrfTokenValid('delete'.$song->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($song);
            $entityManager->flush();
        }

        return $this->redirectToRoute('song_index');
    }
}
