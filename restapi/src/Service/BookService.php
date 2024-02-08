<?php
// src/Service/BookService.php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;

class BookService
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function getAllBooks(): array
    {
        return $this->bookRepository->findAll();
    }

    public function getBookById(int $id): ?Book
    {
        return $this->bookRepository->find($id);
    }

    public function createBook(Book $book): Book
    {
        $this->bookRepository->save($book);
        return $book;
    }

    public function updateBook(Book $book): Book
    {
        $this->bookRepository->save($book);
        return $book;
    }

    public function deleteBook(Book $book): void
    {
        $this->bookRepository->remove($book);
    }
}
