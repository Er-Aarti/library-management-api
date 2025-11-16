<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Books;
use Illuminate\Http\Response;
use App\Models\Borrowing;
use App\Events\BookBorrowed;
use App\Http\Requests\BooksRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="Get all books",
     *     description="Returns a paginated list of all books",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of books",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="The Great Gatsby"),
     *                 @OA\Property(property="author", type="string", example="F. Scott Fitzgerald")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 25);
        $books = Cache::tags(['books'])->remember("books_page_{$page}_limit_{$limit}", 3600, function () use ($limit) {
            return Books::paginate($limit);
        });
        return response()->json($books);
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     summary="Get book details",
     *     description="Returns details of a specific book by ID",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="book_data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="The Great Gatsby"),
     *                 @OA\Property(property="author", type="string", example="F. Scott Fitzgerald"),
     *                 @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
     *                 @OA\Property(property="status", type="string", example="available")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $book = Books::findOrFail($id);
            return response()->json(['book_data' => $book], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Book not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
        return response()->json($book);
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     summary="Create a new book",
     *     description="Adds a new book to the library",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"title","author","date"},
     *             @OA\Property(property="title", type="string", example="The Great Gatsby"),
     *             @OA\Property(property="author", type="string", example="F. Scott Fitzgerald"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="The Great Gatsby"),
     *             @OA\Property(property="author", type="string", example="F. Scott Fitzgerald"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(BooksRequest $request)
    {
        $book = Books::create($request->all());
        Cache::tags(['books'])->flush();
        return response()->json($book, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/books/{id}",
     *     description="Update an existing book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the book to update",
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="The Great Gatsby"),
     *             @OA\Property(property="author", type="string", example="F. Scott Fitzgerald"),
     *             @OA\Property(property="date", type="date", example="2025-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *        description="Book updated successfully"
     *    ),
     * )
     */
    public function update(BooksRequest $request, $id)
    {
        $book = Books::findOrFail($id);
        $book->update($request->all());

        Cache::tags(['books'])->flush();
        return response()->json($book);
    }

    public function destroy($id)
    {
        Books::destroy($id);
        Cache::tags(['books'])->flush();
        return response()->json(['message' => 'Book deleted']);
    }

    /**
     * @OA\Post(
     *     path="/api/books/{id}/borrow",
     *     description="Borrow a book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the book to borrow",
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Book borrowed successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Book is already borrowed"
     *     )
     * )
     */
    public function borrow($id)
    {
        try {
            $book = Books::findOrFail($id);
            if ($book->status === 'available') {
                $book->status = 'borrowed';
                $book->save();

                $borrowing = Borrowing::create([
                    'user_id' => auth()->id(),
                    'book_id' => $book->id,
                    'borrowed_at' => now()
                ]);

                event(new BookBorrowed(auth()->user(), $book));

                return response()->json(['message' => 'Book borrowed successfully']);
            } else {
                return response()->json(['message' => 'Book is already borrowed'], 400);
            }
        } catch (\Exception $e) {
            Log::error("Error while borrowing book: " . $e->getMessage());
            return response()->json(['message' => 'Something went wrong while borrowing the book'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/books/{id}/return",
     *     description="Return a borrowed book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the book to return",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book returned successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Book was not borrowed"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You did not borrow this book"
     *     )
     * )
     */
    public function returnBook($id)
    {
        $book = Books::findOrFail($id);
        $borrowing = Borrowing::where('user_id', auth()->id())
            ->where('book_id', $id)
            ->whereNull('returned_at')
            ->first();

        if (!$borrowing) {
            return response()->json([
                'message' => 'You did not borrow this book. Only the user who borrowed it can return it.'
            ], 403);
        }

        if ($book->status === 'borrowed') {
            $book->update(['status' => 'available']);
            $borrowing->update(['returned_at' => now()]);

            return response()->json(['message' => 'Book returned successfully']);
        } else {
            return response()->json(['message' => 'Book was not borrowed'], 400);
        }
    }
}
