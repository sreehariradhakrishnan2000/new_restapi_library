<?php

namespace App\Controller;

use App\Entity\Book;
use App\DTO\BookDto;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    private $bookRepository;
    private $serializer;
    private $validator;
    private $entityManager;

    public function __construct(BookRepository $bookRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->bookRepository = $bookRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/books', name: 'get_books', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $paginator = $this->bookRepository->createQueryBuilder('b')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalBooks = $this->bookRepository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $bookDtos = array_map(function (Book $book) {
            return $this->serializeBookDto($book);
        }, $paginator);

        $prevPage = ($page > 1) ? $page - 1 : null;
        $nextPage = ($page * $limit < $totalBooks) ? $page + 1 : null;

        $responseData = [
            'data' => $bookDtos,
            'pagination' => [
                'total' => $totalBooks,
                'page' => $page,
                'limit' => $limit,
                'prev_page' => $prevPage,
                'next_page' => $nextPage,
            ]
        ];

        return new JsonResponse($this->serializer->serialize($responseData, 'json'), Response::HTTP_OK, [], true);
    }

    #[Route('/api/books/{id}', name: 'get_book', methods: ['GET'])]
    public function show(Book $book): JsonResponse
    {
        return new JsonResponse($this->serializer->serialize($this->serializeBookDto($book), 'json'), Response::HTTP_OK, [], true);
    }

    #[Route('/api/bookcreate', name: 'create_book', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['author'])) {
            return new JsonResponse(['error' => 'Author field is required'], Response::HTTP_BAD_REQUEST);
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setCreatedAt(new \DateTime()); // Set createdAt
        $book->setUpdatedAt(new \DateTime()); // Set updatedAt

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        // Return the created book DTO
        $bookDto = new BookDto();
        $bookDto->setId($book->getId());
        $bookDto->setTitle($book->getTitle());
        $bookDto->setAuthor($book->getAuthor());
        $bookDto->setCreatedAt($book->getCreatedAt());
        $bookDto->setUpdatedAt($book->getUpdatedAt());

        $data = $this->serializer->serialize($bookDto, 'json');

        return new JsonResponse(['message' => 'Book created!', 'book' => $data], Response::HTTP_CREATED);
    }

    #[Route('/api/bookupdate/{id}', name: 'update_book', methods: ['PUT'])]
    public function update(Request $request, Book $book): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Update book properties
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']); // Update author
        // Update other properties if needed

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->entityManager->flush();

        return new JsonResponse($this->serializer->serialize($this->serializeBookDto($book), 'json'), Response::HTTP_OK, [], true);
    }

    #[Route('/api/bookdelete/{id}', name: 'delete_book', methods: ['DELETE'])]
    public function delete(Book $book): JsonResponse
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();

        $response = new JsonResponse(['message' => 'Book deleted successfully'], Response::HTTP_NO_CONTENT);
        return $response;
    }

    // Add this method to your BookController class
    private function serializeBookDto(Book $book): BookDto
    {
        $bookDto = new BookDto();
        $bookDto->setId($book->getId());
        $bookDto->setTitle($book->getTitle());
        $bookDto->setAuthor($book->getAuthor());
        $bookDto->setCreatedAt($book->getCreatedAt());
        $bookDto->setUpdatedAt($book->getUpdatedAt());

        return $bookDto;
    }

    private function validationErrorResponse($errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return new JsonResponse(['error' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }
}
