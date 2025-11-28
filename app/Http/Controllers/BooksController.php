<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Http\Resources\BookResource;

class BooksController extends Controller
{
    /**
     * ==========1===========
     * Tampilkan daftar semua buku
     */
    public function index()
    {
        $books = Book::all();
        return response()->json([
            'message' => 'Books retrieved successfully',
            'data' => BookResource::collection($books),
        ], 200);
    }

    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'required|integer|digits:4',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $book = Book::create($request->all());

        return response()->json([
            'message' => 'Book created successfully',
            'data' => new BookResource($book),
        ], 201);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);
        if (!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404);
        }
        return response()->json([
            'message' => 'Book retrieved successfully',
            'data' => new BookResource($book),
        ], 200);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'author' => 'string|max:255',
            'published_year' => 'integer|digits:4',
            'is_available' => 'boolean',
        ]);

        if(!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404);
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $book->update($request->all());

        return response()->json([
            'message' => 'Book updated successfully',
            'data' => new BookResource($book),
        ], 200);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);

        if(!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully'
        ], 200);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::find($id);

        if(!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404);
        }

        $book->is_available = !$book->is_available;
        $book->save();

        $status = $book->is_available ? 'returned' : 'borrowed';

        return response()->json([
            'message' => "Book has been successfully {$status}.",
            'data' => new BookResource($book)
        ], 200);
    }
}
