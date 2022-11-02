<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase; // on cada test comienza con la BD sin registros
    /** @test */
    public function can_get_all_books()
    {
        $books = Book::factory(4)->create();
        // Visitiar la ruta books.index y esperar que contenga un book en la respuesta
        $this->getJson(route('books.index'))
            ->assertJsonFragment([
                'title' => $books[0]->title,
            ]);

    }
    /** @test */
    public function can_get_one_book()
    {
        $book = Book::factory()->create();
        // Enviar a la ruta show el book creado
        $this->getJson(route('books.show', $book))
        // Esperar que la respuesta contenga el title del book enviado
            ->assertJsonFragment([
                'title' => $book->title,
            ]);

    }
    /** @test */
    public function can_create_books()
    {
        // Verificar que la validacion sea correcta
        $this->postJson(route('books.store'), [])
            ->assertJsonValidationErrorFor('title');

        // Esperar que existe el objeto creado con el title en l abase de datos
        $this->postJson(route('books.store', [
            'title' => 'Mi nuevo libro test',
        ]))->assertJsonFragment([
            'title' => 'Mi nuevo libro test',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Mi nuevo libro test',
        ]);

    }

    /** @test */
    public function can_update_books()
    {
        $book = Book::factory()->create();

        // Verificar que la validacion sea correcta
        // Se envia el book y el request con los datos que queremos actualizar
        $this->patchJson(route('books.update', $book), [])
            ->assertJsonValidationErrorFor('title');

        // Hacer un patch con la info actualizada
        $this->patchJson(route('books.update', $book), [
            'title' => 'Edited book',
        ])->assertJsonFragment([ // Esperar que el titulo sea el actualizado
            'title' => 'Edited book',
        ]);
        // Esperar que en la BD exista un registro con el title actualizado
        $this->assertDatabaseHas('books', [
            'title' => 'Edited book',
        ]);
    }
    /** @test */
    public function can_delete_books()
    {
        $book = Book::factory()->create();

        // Hacer un delete
        $this->deleteJson(route('books.destroy', $book))
            ->assertNoContent(); // No esperar contenido

        $this->assertDatabaseCount('books', 0);
    }
}
