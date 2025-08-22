<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleBooksSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            return;
        }

        $books = [
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'description' => 'A classic novel set in the Jazz Age.'],
            ['title' => '1984', 'author' => 'George Orwell', 'description' => 'Dystopian novel.'],
            ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'description' => 'A story about racial injustice.'],
            ['title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'description' => 'Classic romance novel.'],
            ['title' => 'Moby-Dick', 'author' => 'Herman Melville', 'description' => 'The story of Captain Ahab’s obsession.'],
            ['title' => 'War and Peace', 'author' => 'Leo Tolstoy', 'description' => 'Epic Russian historical novel.'],
            ['title' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'description' => 'Teenage rebellion and identity.'],
            ['title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'description' => 'Fantasy adventure.'],
            ['title' => 'Brave New World', 'author' => 'Aldous Huxley', 'description' => 'Dystopian society novel.'],
            ['title' => 'Crime and Punishment', 'author' => 'Fyodor Dostoevsky', 'description' => 'Moral dilemmas and guilt.'],
            ['title' => 'Jane Eyre', 'author' => 'Charlotte Brontë', 'description' => 'Classic coming-of-age story.'],
            ['title' => 'Animal Farm', 'author' => 'George Orwell', 'description' => 'Political allegory.'],
            ['title' => 'The Odyssey', 'author' => 'Homer', 'description' => 'Epic Greek poem.'],
            ['title' => 'Wuthering Heights', 'author' => 'Emily Brontë', 'description' => 'Tale of passion and revenge.'],
            ['title' => 'The Lord of the Rings', 'author' => 'J.R.R. Tolkien', 'description' => 'Epic fantasy trilogy.'],
            ['title' => 'Great Expectations', 'author' => 'Charles Dickens', 'description' => 'Story of personal growth.'],
            ['title' => 'The Iliad', 'author' => 'Homer', 'description' => 'Epic tale of the Trojan War.'],
            ['title' => 'Les Misérables', 'author' => 'Victor Hugo', 'description' => 'Social justice and redemption.'],
            ['title' => 'Frankenstein', 'author' => 'Mary Shelley', 'description' => 'Gothic science fiction.'],
            ['title' => 'Dracula', 'author' => 'Bram Stoker', 'description' => 'Classic vampire horror novel.'],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(
                ['title' => $book['title'], 'user_id' => $admin->id],
                ['author' => $book['author'], 'description' => $book['description']]
            );
        }
    }
}
