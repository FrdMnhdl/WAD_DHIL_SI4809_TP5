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
     * ==========1============
     * Tampilkan daftar semua buku
     */
    public function index()
    {
        $books = Book::all();
        return BookResource::collection($books);
    }

    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'author' => 'required|string',
            'published_year' => 'required|digits:4',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $data['is_available'] = true;

        $book = Book::create($data);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(
                ['message' => 'Book not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        return new BookResource($book);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);
        if(!$book) {
            return response()->json(
                ['message' => 'Book not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string',
            'author' => 'sometimes|required|string',
            'published_year' => 'sometimes|required|digits:4',
            'is_available' => 'sometimes|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book->update($validator->validated());

        return new BookResource($book);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);
        if(!$book) {
            return response()->json(
                ['message' => 'Book not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        $book->delete();
        return response()->json(
            ['message' => 'Book deleted'],
            Response::HTTP_OK
        );
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(
                ['message' => 'Book not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        $book->is_available = !$book->is_available;
        $book->save();

        return new BookResource($book);
    }
}
