<?php
namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;

#[Route('/admin')]
class AdminReservationController extends AbstractController
{
    #[Route('/reservations', name: 'admin_reservations')]
    public function reservations(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy([], ['createdAt' => 'DESC']);

        $grouped = [];
        foreach ($reservations as $reservation) {
            $event = $reservation->getEvent();
            $eventId = $event?->getId() ?? 0;
            if (!isset($grouped[$eventId])) {
                $grouped[$eventId] = [
                    'event' => $event,
                    'items' => [],
                ];
            }
            $grouped[$eventId]['items'][] = $reservation;
        }

        return $this->render('admin/reservations.html.twig', [
            'grouped_reservations' => $grouped,
        ]);
    }
}
