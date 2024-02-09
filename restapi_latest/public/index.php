<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books CRUD</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Books CRUD</h1>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addBookModal">
            Add Book
        </button>

        <!-- Table to display books -->
        <table class="table">
            <thead>
                <tr>
                     <th>Id</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Actions</th>
                    <th>Created time</th>
                    <th>Updated time</th>
                </tr>
            </thead>
            <tbody id="bookList">
                <!-- Here goes the dynamically generated content from the backend -->
            </tbody>
        </table>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Form to add a new book -->
                <form id="addBookForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBookModalLabel">Add Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="author" name="author">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
$(document).ready(function () {
    // Function to fetch all books and populate the table
    function fetchBooks() {
        $.get("/api/books", function (data) {
            $("#bookList").empty(); // Clear existing content
            data.forEach(function (book) {
                $("#bookList").append(
                    "<tr>" +
                    "<td>" + book.id + "</td>" +
                    "<td>" + book.title + "</td>" +
                    "<td>" + book.author + "</td>" +
                    "<td>" + book.created_at + "</td>" +
                    "<td>" + book.updated_at + "</td>" +
                    "<td>" +
                    "<button type='button' class='btn btn-info btn-sm edit' data-id='" + book.id + "'>Edit</button> " +
                    "<button type='button' class='btn btn-danger btn-sm delete' data-id='" + book.id + "'>Delete</button>" +
                    "</td>" +
                    "</tr>"
                );
            });
        });
    }

    // Initial fetch of books when the page loads
    fetchBooks();

    // Handle form submission for adding a new book
    $("#addBookForm").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.post("/api/bookcreate", formData, function (response) {
            alert(response.message);
            fetchBooks(); // Refresh the book list after adding a new book
            $("#addBookModal").modal("hide"); // Hide the modal after adding a new book
        });
    });

    // Handle delete button click
    $(document).on("click", ".delete", function () {
        var bookId = $(this).data("id");
        $.ajax({
            url: "/api/bookdelete/" + bookId,
            type: "DELETE",
            success: function (response) {
                alert("Book deleted!");
                fetchBooks(); // Refresh the book list after deleting a book
            }
        });
    });

    // Handle edit button click
    $(document).on("click", ".edit", function () {
        var bookId = $(this).data("id");
        $.get("/api/books/" + bookId, function (data) {
            $("#editTitle").val(data.title);
            $("#editAuthor").val(data.author);
            $("#editBookModal").modal("show");
            $("#editBookForm").attr("action", "/api/bookupdate/" + bookId);
        });
    });

    // Handle form submission for editing a book
    $("#editBookForm").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        var actionUrl = $(this).attr("action");
        $.ajax({
            url: actionUrl,
            type: "PUT",
            data: formData,
            success: function (response) {
                alert(response.message);
                fetchBooks(); // Refresh the book list after editing a book
                $("#editBookModal").modal("hide"); // Hide the modal after editing a book
            }
        });
    });
});

    </script>
</body>
</html>
