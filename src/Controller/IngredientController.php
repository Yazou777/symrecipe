<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/ingredient')]
class IngredientController extends AbstractController
{
    #[Route('/', name: 'app_ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {

    $ingredients = $paginator->paginate(
        $ingredientRepository->findAll(), /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/
    );
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/new', name: 'app_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IngredientRepository $ingredientRepository): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredientRepository->save($ingredient, true);

            return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ingredient/new.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ingredient_show', methods: ['GET'])]
    public function show(Ingredient $ingredient): Response
    {
        return $this->render('ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ingredient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ingredient $ingredient, IngredientRepository $ingredientRepository): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredientRepository->save($ingredient, true);

            return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ingredient_delete', methods: ['POST'])]
    public function delete(Request $request, Ingredient $ingredient, IngredientRepository $ingredientRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
            $ingredientRepository->remove($ingredient, true);
        }

        return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
    }
}
