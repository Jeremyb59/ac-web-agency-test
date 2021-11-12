<?php

namespace App\Controller\Admin;

use App\Entity\{ Film, Genre };
use App\Form\FilmType;
use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;

/**
 * @Route("/admin/film")
 */
class FilmController extends AbstractController
{
    /**
     * @Route("/", name="admin_film_index", methods={"GET"})
     */
    public function index(FilmRepository $filmRepository): Response
    {
        return $this->render('admin/film/index.html.twig', [
            'films' => $filmRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_film_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();
            if ($file) {
                $brochureFileName = $fileUploader->upload($file);
                $film->setImage($brochureFileName);
            }
            $genre = $form->get('genreFilm')->getData();
            if ($genre) {
                $film->setGenre($this->getDoctrine()->getRepository(Genre::class)->find($genre));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('admin_film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/film/new.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_film_show", methods={"GET"})
     */
    public function show(Film $film): Response
    {
        return $this->render('admin/film/show.html.twig', [
            'film' => $film,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_film_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Film $film, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();
            if ($file) {
                $brochureFileName = $fileUploader->upload($file);
                $film->setImage($brochureFileName);
            }
            $genre = $form->get('genreFilm')->getData();
            if ($genre) {
                $film->setGenre($this->getDoctrine()->getRepository(Genre::class)->find($genre));
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/film/edit.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_film_delete", methods={"POST"})
     */
    public function delete(Request $request, Film $film): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_film_index', [], Response::HTTP_SEE_OTHER);
    }
}
