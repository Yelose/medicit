<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Pacient;

#[Route('/api', name: 'api_')]
class PacientController extends AbstractController
{
    #[Route('/patients', name: 'show-all-pacients', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $patients = $doctrine
            ->getRepository(Pacient::class)
            ->findAll();

        $data = [];

        foreach ($patients as $patient) {
            $data[] = [
                'id' => $patient->getId(),
                'name' => $patient->getName(),
                "surname" => $patient->getSurname(),
                "healthMatter" => $patient->getHealthMatter(),
                "appointments" => $patient->getAppointments()
            ];
        }

        return $this->json($data);
    }
    #[Route('/patient', name: 'pacient_create', methods: ['POST'])]
    public function addpacient(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $data = $request->toArray();

        $patient = new Pacient();
        $patient->setName($data["name"]);
        $patient->setSurname($data["surname"]);
        $patient->setHealthMatter($data["healthMatter"]);

        $entityManager->persist($patient);
        $entityManager->flush();

        $response = [
            'id' => $patient->getId(),
            'name' => $patient->getName(),
            'surname' => $patient->getSurname(),
            "healthMatter" => $patient->getHealthMatter()
        ];

        return $this->json($response, JsonResponse::HTTP_CREATED);
    }

    #[Route('/patient/{id}', name: 'pacient_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $patient = $doctrine->getRepository(Pacient::class)->find($id);

        if (!$patient) {

            return $this->json('No pacient found for id ' . $id, 404);
        }

        $data[] = [
            'id' => $patient->getId(),
            'name' => $patient->getName(),
            "surname" => $patient->getSurname(),
            "healthMatter" => $patient->getHealthMatter(),
            "appointments" => $patient->getAppointments()
        ];

        return $this->json($data);
    }

    #[Route('/patient/{id}', name: 'patient_update', methods: ['PUT', 'patch'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $patient = $entityManager->getRepository(Pacient::class)->find($id);

        if (!$patient) {
            return $this->json('No patient found for id' . $id, 404);
        }
        $databody = $request->toArray();

        $patient->setName($databody["name"]);
        $patient->setSurname($databody["surname"]);
        $patient->setHealthMatter($databody["healthMatter"]);
        $entityManager->flush();

        $data[] = [
            'id' => $patient->getId(),
            'name' => $patient->getName(),
            "surname" => $patient->getSurname(),
            "healthMatter" => $patient->getHealthMatter(),
            "appointments" => $patient->getAppointments()
        ];

        return $this->json($data);
    }

    #[Route('/patient/{id}', name: 'pacient_delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $patient = $entityManager->getRepository(Pacient::class)->find($id);

        if (!$patient) {
            return $this->json('No pacient found for id' . $id, 404);
        }

        $entityManager->remove($patient);
        $entityManager->flush();

        return $this->json('Deleted a pacient successfully with id ' . $id);
    }
}