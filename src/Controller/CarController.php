<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Model\CarSearchCriteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CarRepository;
use App\Form\CarSearchType;
use App\Model\AdminCarListCriteria;

#[Route('/cars', name: 'car_')]
final class CarController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, CarRepository $carRepository): Response
    {
        $search = new CarSearchCriteria();
        $form = $this->createForm(CarSearchType::class, $search);
        $form->handleRequest($request);

        return $this->render('car/list.html.twig', [
            'form' => $form->createView(),
            'cars' => $carRepository->search($search),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $allowed = ['car_list', 'car_dashboard'];
        $redirect = (string) $request->query->get('redirect', 'car_list');
        if (!in_array($redirect, $allowed, true)) {
            $redirect = 'car_list';
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($car);
            $em->flush();

            $this->addFlash('success', 'Voiture créée avec succès.');

            return $this->redirectToRoute($redirect);
        }

        return $this->render('car/create.html.twig', [
            'form' => $form->createView(),
            'redirect' => $redirect,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Car $car, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Voiture mise à jour.');
            return $this->redirectToRoute('car_dashboard', [
                'page' => (int) $request->query->get('page', 1),
                'sort' => (string) $request->query->get('sort', 'createdAt'),
                'dir'  => (string) $request->query->get('dir', 'desc'),
            ]);
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        Car $car,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('delete_car_' . $car->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('car_dashboard', $request->query->all());
        }

        $em->remove($car);
        $em->flush();

        $this->addFlash('success', 'Voiture supprimée.');

        return $this->redirectToRoute('car_dashboard', $request->query->all());
    }

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(Request $request, CarRepository $carRepository): Response
    {
        $criteria = new AdminCarListCriteria();

        $criteria->page  = (int) $request->query->get('page', 1);
        $criteria->sort  = (string) $request->query->get('sort', 'createdAt');
        $criteria->dir   = (string) $request->query->get('dir', 'desc');
        $criteria->limit = (int) $request->query->get('limit', $criteria->limit);

        $criteria->normalize();

        $pagination = $carRepository->paginate($criteria);

        return $this->render('car/dashboard.html.twig', [
            'pagination' => $pagination,
            'criteria' => $criteria,
        ]);
    }

    #[Route('/_ptra-field', name: 'ptra_field', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function ptraField(Request $request): Response
    {
        $type = $request->query->getString('type');

        $car = new Car();
        $form = $this->createForm(CarType::class, $car);

        $form->submit(['type' => $type], false);

        return $this->render('car/_ptra_field.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
