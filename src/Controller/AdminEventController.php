<?php
namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/events')]
class AdminEventController extends AbstractController
{
    #[Route('', name: 'admin_events', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('admin/events.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'admin_events_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Event created.');

            return $this->redirectToRoute('admin_events');
        }

        return $this->render('admin/event_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    #[Route('/{id}', name: 'admin_events_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(Event $event): Response
    {
        return $this->render('admin/event_show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_events_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Event updated.');

            return $this->redirectToRoute('admin_events');
        }

        return $this->render('admin/event_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_events_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): RedirectResponse
    {
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete_event_' . $event->getId(), $submittedToken)) {
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash('success', 'Event deleted.');
        } else {
            $this->addFlash('error', 'Invalid delete request.');
        }

        return $this->redirectToRoute('admin_events');
    }
}
