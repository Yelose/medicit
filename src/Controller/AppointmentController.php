<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Doctor;
use App\Entity\Pacient;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class AppointmentController extends AbstractController
{

    #[Route('/appointment', name: 'app_appointment', methods: ['POST'])]
    public function addAppointment(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $data = $request->toArray();

        $appointment = new Appointment();
        $date = $data["date"];
        $doctorId = $data["doctor"];
        $doctor = $entityManager->getRepository(Doctor::class)->find($doctorId);
        $patientId = $data["patient"];
        $patient = $entityManager->getRepository(Pacient::class)->find($patientId);

        $appointment->setDate($date);
        $appointment->setDoctor($doctor);
        $appointment->setPatient($patient);

        $entityManager->persist($appointment);
        $entityManager->flush();

        $response = [
            'date' => $appointment->getDate(),
            'doctor' => [
                'id' => $doctor->getId(),
                'name' => $doctor->getName(),
                'surname' => $doctor->getSurname()
            ],
            'patient' => [
                'id' => $patient->getId(),
                'name' => $patient->getName(),
                'surname' => $patient->getSurname()
            ]
        ];

        return $this->json($response, JsonResponse::HTTP_CREATED);
    }

}