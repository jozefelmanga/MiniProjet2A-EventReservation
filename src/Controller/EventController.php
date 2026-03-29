<?php
namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('events/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'app_event_show', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function show(Event $event, Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setEvent($event);
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation enregistrée.');

            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        return $this->render('events/show.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
