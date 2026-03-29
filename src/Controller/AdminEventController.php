<?php
namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminEventController extends AbstractController
{
    #[Route('/events', name: 'admin_events')]
    public function events(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('admin/events.html.twig', [
            'events' => $events,
        ]);
    }
}
