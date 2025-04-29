<?php
namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\DTO\CreateTodoDTO;
use App\DTO\UpdateTodoDTO;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TodoController extends AbstractController
{
    #[Route('/api/todos', methods: ['GET'])]
    public function getTodos(TodoRepository $todoRepository, SerializerInterface $serializer,UserInterface $user): JsonResponse
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $todos = $todoRepository->findAll();
        } else {
            $todos = $todoRepository->findBy(['user' => $user]);
        }
        $json = $serializer->serialize($todos, 'json', ['groups' => 'todo:read']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/todos', methods: ['POST'])]
    public function createTodo(Request $request,ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new CreateTodoDTO();
        $dto->title = $data['title'] ?? '';
        $dto->description = $data['description'] ?? '';
        // DTO
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], 400);
        }
        $todo = new Todo();
        $todo->setTitle($dto->title);
        $todo->setDescription($dto->description);
        $todo->setUser($this->getUser());

        $em->persist($todo);
        $em->flush();
        return $this->json([
            'message' => 'Todo created successfully!',
            'id' => $todo->getId()
        ], 201);
    }
    
    #[Route('/api/todos/{id}', methods: ['PUT'])]
    public function updateTodo(int $id, Request $request, TodoRepository $todoRepository,ValidatorInterface $validator, EntityManagerInterface $em, UserInterface $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $todo = $todoRepository->find($id);
        if (!$todo) {
            return $this->json(['message' => 'Todo not found'], 404);
        }
        $dto = new UpdateTodoDTO();
        $dto->title = $data['title'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->completed = $data['completed'] ?? null;
        //DTO
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }
        
        if ($dto->title !== null) {
            $todo->setTitle($dto->title);
        }
        if ($dto->description !== null) {
            $todo->setDescription($dto->description);
        }
        if ($dto->completed !== null) {
            $todo->setCompleted($dto->completed);
        }
        $em->persist($todo);
        $em->flush();

        return $this->json([
            'message' => 'Todo updated successfully!',
            'id' => $todo->getId()
        ], 200);
    }

    #[Route('/api/todos/{id}', methods: ['DELETE'])]
    public function deleteTodo(int $id, TodoRepository $todoRepository, EntityManagerInterface $em, UserInterface $user): JsonResponse
    {
        $todo = $todoRepository->find($id);

        if (!$todo) {
            return $this->json(['message' => 'Todo not found'], 404);
        }

        if (!$this->isGranted('ROLE_ADMIN') && $todo->getUser() !== $user) {
            return $this->json(['message' => 'Access denied'], 403);
        }

        $em->remove($todo);
        $em->flush();

        return $this->json([
            'message' => 'Todo deleted successfully'
        ]);
    }

    #[Route('/api/todos/{id}', methods: ['PATCH'])]
    public function isCompletedTodo(int $id, Request $request, TodoRepository $todoRepository,ValidatorInterface $validator, EntityManagerInterface $em, UserInterface $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $todo = $todoRepository->find($id);
        if (!$todo) {
            return $this->json(['message' => 'Todo not found'], 404);
        }
        $todo->setCompleted();
        $em->persist($todo);
        $em->flush();

        return $this->json([
            'message' => 'Todo updated successfully!',
            'id' => $todo->getId()
        ], 200);
    }

}