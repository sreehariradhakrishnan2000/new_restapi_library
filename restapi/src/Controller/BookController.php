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


class BookController extends AbstractController
{
    private $bookRepository;
    private $serializer;
    private $validator;

    public function __construct(BookRepository $bookRepository, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->bookRepository = $bookRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/books", name="get_books", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $books = $this->bookRepository->findAll();
        $bookDtos = [];

        foreach ($books as $book) {
            $bookDto = new BookDto();
            $bookDto->setId($book->getId());
            $bookDto->setTitle($book->getTitle());
            $bookDto->setAuthor($book->getAuthor());
            $bookDto->setCreatedAt($book->getCreatedAt());
            $bookDto->setUpdatedAt($book->getUpdatedAt());

            $bookDtos[] = $bookDto;
        }

        $data = $this->serializer->serialize($bookDtos, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/books/{id}", name="get_book", methods={"GET"})
     */
    public function show(Book $book): JsonResponse
    {
        $bookDto = new BookDto();
        $bookDto->setId($book->getId());
        $bookDto->setTitle($book->getTitle());
        $bookDto->setAuthor($book->getAuthor());
        $bookDto->setCreatedAt($book->getCreatedAt());
        $bookDto->setUpdatedAt($book->getUpdatedAt());

        $data = $this->serializer->serialize($bookDto, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/book", name="create_book", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $book = new Book();
        $book->setTitle($data['title']);
        // Set other properties

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['error' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

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

    /**
     * @Route("/api/bookupdate/{id}", name="update_book", methods={"PUT"})
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Update book properties
        $book->setTitle($data['title']);
        // Update other properties

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['error' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        // Return the updated book DTO
        $bookDto = new BookDto();
        $bookDto->setId($book->getId());
        $bookDto->setTitle($book->getTitle());
        $bookDto->setAuthor($book->getAuthor());
        $bookDto->setCreatedAt($book->getCreatedAt());
        $bookDto->setUpdatedAt($book->getUpdatedAt());

        $data = $this->serializer->serialize($bookDto, 'json');

        return new JsonResponse(['message' => 'Book updated!', 'book' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/api/bookdelete/{id}", name="delete_book", methods={"DELETE"})
     */
    public function delete(Book $book): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
