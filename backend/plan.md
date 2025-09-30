Project Plan: LearnSphere AI (API Edition)
This plan outlines the development of a decoupled web application where a Laravel API backend serves data to a standalone Vue.js SPA frontend. The application will use AI to generate quizzes from user-submitted content.

Technology Stack
Backend: Laravel 11+ (API Only) with Laravel Sanctum for authentication.

Frontend: Vue.js 3 SPA with Vite, Vue Router, and Pinia.

Development: Docker with Laravel Sail.

Database: PostgreSQL.

Styling: Tailwind CSS.

AI - LLM: Google Gemini API.

Deployment: Laravel Forge.

Phase 0: The Decoupled Foundation
Goal: Set up the Laravel API for stateless authentication and create the separate Vue.js project.

Configure Laravel for API Authentication: We'll use Laravel Sanctum for token-based authentication. Breeze can set this up for us in API-only mode.

Bash

# Install Breeze
./vendor/bin/sail composer require laravel/breeze --dev

# Run the Breeze installation for an API
./vendor/bin/sail artisan breeze:install api

# Run the migrations to create the users table
./vendor/bin/sail artisan migrate
Configure CORS: Since your Vue app will run on a different port (and later, a different domain), you need to allow it to make requests to your API.

Open config/cors.php.

In the paths array, make sure it includes 'api/*'.

In allowed_origins, add your frontend's development URL (Vite's default is http://localhost:5173). For production, you'll add your actual domain.

Create the Vue.js Project: In a separate terminal, navigate outside your Laravel project directory and create the Vue app.

Bash

# This creates a new 'frontend' directory
npm create vue@latest frontend

# Follow the prompts:
# ✔ Project name: … frontend
# ✔ Add TypeScript? … No
# ✔ Add JSX Support? … No
# ✔ Add Vue Router for Single Page Application development? … Yes
# ✔ Add Pinia for state management? … Yes
# ✔ Add Vitest for Unit Testing? … No
# ✔ Add an End-to-End Testing Solution? … No
# ✔ Add ESLint for code quality? … Yes
# ✔ Add Prettier for code formatting? … Yes

# Navigate into the new project and install dependencies
cd frontend
npm install
Run Both Servers: You will now have two servers running in two separate terminals for development.

Terminal 1 (Laravel API): cd your-laravel-project -> ./vendor/bin/sail up

Terminal 2 (Vue Frontend): cd frontend -> npm run dev

Phase 1: API Backend & Database Schema
Goal: Build the database structure and API endpoints for managing documents and quizzes.

Generate All Models & Migrations: We need tables for documents, quizzes, questions, and answers.

Bash

# Document model and migration
./vendor/bin/sail artisan make:model Document -m

# Quiz model and migration
./vendor/bin/sail artisan make:model Quiz -m

# Question model and migration
./vendor/bin/sail artisan make:model Question -m

# Answer model and migration
./vendor/bin/sail artisan make:model Answer -m
Define the Database Schema: Open the new migration files in database/migrations/ and define the table structures.

..._create_documents_table.php

PHP

Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->longText('content');
    $table->enum('status', ['pending', 'processing', 'ready', 'failed'])->default('pending');
    $table->timestamps();
});
..._create_quizzes_table.php

PHP

Schema::create('quizzes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('document_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->timestamps();
});
..._create_questions_table.php

PHP

Schema::create('questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
    $table->text('question_text');
    $table->timestamps();
});
..._create_answers_table.php

PHP

Schema::create('answers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('question_id')->constrained()->cascadeOnDelete();
    $table->text('answer_text');
    $table->boolean('is_correct')->default(false);
    $table->timestamps();
});
Run Migrations: Apply the new schema to your database.

Bash

./vendor/bin/sail artisan migrate
Create API Controllers & Routes: Define the endpoints in routes/api.php. Use resource controllers for standard CRUD operations.

Bash

# Create a controller for documents
./vendor/bin/sail artisan make:controller Api/DocumentController --api
In routes/api.php, protect your routes with Sanctum's middleware.

PHP

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('documents', App\Http\Controllers\Api\DocumentController::class);
    // Add other protected routes here for quizzes, etc.
});
Phase 2: Building the Vue.js SPA
Goal: Create the user interface for authentication, document management, and quiz taking.

Set up API Communication: Install Axios in your Vue project (npm install axios). Create a service or utility file to handle base configuration (e.g., setting the API base URL http://localhost:8000).

Implement Authentication Flow:

Create Login.vue and Register.vue pages.

Use Pinia to store the user's authentication state and API token globally.

Use Vue Router's navigation guards to protect routes that require a user to be logged in.

Build Core Feature Pages:

Dashboard.vue: Fetch and display a list of the user's documents from the /api/documents endpoint.

DocumentCreate.vue: A form to upload a new document (title and content).

DocumentView.vue: Display a document's content and include a button to "Generate Quiz."

Phase 3: AI Integration & Logic
Goal: Connect to the Gemini API and use the response to populate your database.

Create Quiz Generation Endpoint:

Create a new controller, e.g., QuizGenerationController.

Define a POST route like /api/documents/{document}/generate-quiz.

Implement Backend Logic:

In the controller, retrieve the document's content.

Send the content to the Gemini API with a carefully crafted prompt asking for quiz data in JSON format.

When you receive the JSON response, parse it.

Use your Eloquent models (Quiz, Question, Answer) to save the entire generated quiz to your database, linking everything together.

Return the newly created Quiz's ID or the full Quiz object as a JSON response.

Build Frontend Quiz Interface:

Create a QuizView.vue page.

When the user is on this page, it will fetch the quiz data (including questions and answers) from an endpoint like /api/quizzes/{quiz_id}.

Build the UI to display one question at a time, handle user selections, and calculate a final score.