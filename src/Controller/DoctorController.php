<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Doctor;

#[Route('/api', name: 'api_')]
class DoctorController extends AbstractController
{
    #[Route('/doctors', name: 'show-all-doctors', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $doctors = $doctrine
            ->getRepository(Doctor::class)
            ->findAll();

        $data = [];

        foreach ($doctors as $doctor) {
            $data[] = [
                'id' => $doctor->getId(),
                'name' => $doctor->getName(),
                "surname" => $doctor->getSurname(),
                "appointments" => $doctor->getAppointments(),
            ];
        }

        return $this->json($data);
    }
    #[Route('/doctor', name: 'doctor_create', methods: ['POST'])]
    public function addDoctor(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $data = $request->toArray();

        $doctor = new Doctor();
        $doctor->setName($data["name"]);
        $doctor->setSurname($data["surname"]);

        $entityManager->persist($doctor);
        $entityManager->flush();

        $response = [
            'id' => $doctor->getId(),
            'name' => $doctor->getName(),
            'surname' => $doctor->getSurname(),
        ];

        return $this->json($response, JsonResponse::HTTP_CREATED);
    }

    #[Route('/doctor/{id}', name: 'doctor_show', methods: ['get'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $doctor = $doctrine->getRepository(Doctor::class)->find($id);

        if (!$doctor) {

            return $this->json('No doctor found for id ' . $id, 404);
        }

        $data[] = [
            'id' => $doctor->getId(),
            'name' => $doctor->getName(),
            "surname" => $doctor->getSurname(),
            "appointments" => $doctor->getAppointments(),
        ];

        return $this->json($data);
    }

    #[Route('/doctor/{id}', name: 'doctor_update', methods: ['put', 'patch'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $doctor = $entityManager->getRepository(Doctor::class)->find($id);

        if (!$doctor) {
            return $this->json('No doctor found for id' . $id, 404);
        }
        $databody = $request->toArray();

        $doctor->setName($databody["name"]);
        $doctor->setSurname($databody["surname"]);
        $entityManager->flush();

        $data[] = [
            'id' => $doctor->getId(),
            'name' => $doctor->getName(),
            "surname" => $doctor->getSurname(),
            "appointments" => $doctor->getAppointments(),
        ];

        return $this->json($data);
    }

    #[Route('/doctor/{id}', name: 'doctor_delete', methods: ['delete'])]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $doctor = $entityManager->getRepository(Doctor::class)->find($id);

        if (!$doctor) {
            return $this->json('No doctor found for id' . $id, 404);
        }

        $entityManager->remove($doctor);
        $entityManager->flush();

        return $this->json('Deleted a doctor successfully with id ' . $id);
    }
}