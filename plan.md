# Project Plan: LearnSphere AI

This document outlines the complete plan to build "LearnSphere AI," an AI-powered study assistant. The goal is to learn PHP/Laravel, Vue.js, and AI integration by following a structured, step-by-step process from local development with Docker to live deployment with Laravel Forge.

---

### **Technology Stack**

* **Development Environment:** Docker with Laravel Sail
* **Backend:** PHP 8.2+ with Laravel 11+
* **Frontend:** Vue.js 3 with Vite & Inertia.js
* **Database:** PostgreSQL (managed by Sail)
* **Styling:** Tailwind CSS
* **AI - LLM:** Google Gemini API
* **Deployment:** Laravel Forge + DigitalOcean (or similar)

---

### **Phase 0: The Docker-Powered Foundation**

*Goal: Get your local development environment running perfectly inside Docker.*

1.  **Install Docker Desktop:** This is the only tool you need on your computer.
    * *Learning:* [Install Docker Desktop](https://docs.docker.com/desktop/)

2.  **Create Laravel Project with Sail:** In your terminal, run this command to create a new Laravel project. It uses Docker, so you don't need PHP installed locally.
    ```bash
    curl -s "[https://laravel.build/learnsphere-ai?with=pgsql](https://laravel.build/learnsphere-ai?with=pgsql)" | bash
    ```

3.  **Start the Environment:** Navigate into the project and start the Docker containers.
    ```bash
    cd learnsphere-ai
    ./vendor/bin/sail up -d 
    ```
    Your site is now running at `http://localhost`.

4.  **Install Breeze for Auth & Frontend:** This will scaffold your login/registration system and configure Vue, Inertia, and Tailwind CSS.
    ```bash
    # Run the Breeze installation command
    ./vendor/bin/sail artisan breeze:install
    
    # When prompted, choose: vue (Vue with Inertia)
    
    # Install all the necessary frontend packages
    ./vendor/bin/sail npm install
    
    # Compile the frontend assets (leave this running in a separate terminal)
    ./vendor/bin/sail npm run dev
    
    # Run the database migrations to create the 'users' table
    ./vendor/bin/sail artisan migrate
    ```

5.  **Set up Gemini API Key:**
    * Get your API key from [Google AI Studio](https://aistudio.google.com/).
    * Open the `.env` file in your project.
    * Add this line to the bottom, pasting your key:
        ```env
        GEMINI_API_KEY="YOUR_API_KEY_HERE"
        ```

6.  **Initialize Version Control:**
    ```bash
    git init
    git add .
    git commit -m "Initial project setup with Sail, Breeze, and Vue"
    # Create a new repository on GitHub and push your code
    ```
* **Phase 0 Learning Materials:**
    * [Laravel Sail Documentation](https://laravel.com/docs/sail)
    * [Laravel Breeze Documentation](https://laravel.com/docs/starter-kits#laravel-breeze)

---

### **Phase 1: Core Backend Logic (Laravel)**

*Goal: Build the backend functionality for managing user documents.*

1.  **Create Document Model & Migration:** This command creates both a model file (`app/Models/Document.php`) and a database migration file.
    ```bash
    ./vendor/bin/sail artisan make:model Document -m
    ```
2.  **Define the Schema:** Open the new migration file in `database/migrations/` and define the table columns inside the `up()` method.
    ```php
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->longText('original_content');
        $table->string('status')->default('ready');
        $table->timestamps();
    });
    ```
3.  **Run the Migration:** Apply the changes to your database.
    ```bash
    ./vendor/bin/sail artisan migrate
    ```
4.  **Create the Controller:** This will handle the logic for your documents.
    ```bash
    ./vendor/bin/sail artisan make:controller DocumentController
    ```
5.  **Define Routes and Controller Logic:** Open `routes/web.php` and add routes for your documents. Then, fill in the logic in `app/Http/Controllers/DocumentController.php` to handle creating, reading, and deleting documents.

* **Phase 1 Learning Materials:**
    * [Laravel Eloquent (Models)](https://laravel.com/docs/eloquent)
    * [Laravel Database Migrations](https://laravel.com/docs/migrations)
    * [Laravel Controllers](https://laravel.com/docs/controllers)
    * [Laravel Routing](https://laravel.com/docs/routing)

---

### **Phase 2: Core Frontend (Vue.js + Tailwind CSS)**

*Goal: Build a clean user interface for the features from Phase 1.*

1.  **Create Vue Page Components:** In `resources/js/Pages/`, create the following Vue components:
    * `Dashboard.vue`: Main page after login. Will show a list of documents.
    * `Documents/Create.vue`: A page with a form to create a new document.
    * `Documents/Show.vue`: A page to display the content of a single document.
2.  **Style with Tailwind CSS:** Use Tailwind's utility classes directly in your Vue components to style buttons, forms, and layouts.
3.  **Implement Interactivity:**
    * Use Inertia's `<Link>` component for navigation between your pages without full reloads.
    * Use Inertia's `useForm` helper in `Create.vue` to handle form submission easily.

* **Phase 2 Learning Materials:**
    * [Inertia.js Documentation](https://inertiajs.com/)
    * [Vue 3 Documentation](https://vuejs.org/guide/introduction.html)
    * [Tailwind CSS Documentation](https://tailwindcss.com/docs/utility-first)

---

### **Phase 3: AI Integration (Google Gemini)**

*Goal: Connect to the Gemini API to bring the "magic" to your application.*

1.  **Install Gemini PHP Client:**
    ```bash
    ./vendor/bin/sail composer require google-gemini-php/client
    ```

2.  **Build Quiz Generation Feature:**
    * **Backend:** Create a `QuizController` and a route. When a user clicks "Generate Quiz," this controller will send the document content to the Gemini API with a specific prompt asking for JSON output.
    * **Frontend:** Create a `Quiz.vue` component to display the questions and handle the user's answers.

3.  **Build AI Tutor Chatbot:**
    * **Backend:** Create an API route (in `routes/api.php`) and a `ChatController`. This will take the user's question and the document context, send it to Gemini, and return the AI's answer.
    * **Frontend:** Create a `Chatbot.vue` component with a chat interface. Use a library like Axios (`sail npm install axios`) to send the user's messages to your backend API.

4.  **Build Text-to-Speech (Optional Stretch Goal):**
    * Add an open-source TTS engine like Piper to your `docker-compose.yml` file as a new service.
    * Create a backend route that sends text to this service and returns an audio file URL.
    * Add a "Read Aloud" button in your Vue components that calls this route and plays the audio.

* **Phase 3 Learning Materials:**
    * [Google AI for Developers](https://ai.google.dev/)
    * [Laravel HTTP Client](https://laravel.com/docs/http-client)

---

### **Phase 4: Refinement & Polish**

*Goal: Make the application feel professional and robust.*

1.  **Add Loading Indicators:** Show spinners or loading messages while waiting for responses from the Gemini API.
2.  **Implement Error Handling:** Gracefully handle cases where the API call fails or returns an error.
3.  **Improve Responsiveness:** Use Tailwind's responsive helpers (like `md:`, `lg:`) to ensure your app looks great on all screen sizes.
4.  **Refactor:** Clean up your code, extract reusable Vue components, and organize your backend logic into services if needed.

---

### **Phase 5: Deployment with Laravel Forge**

*Goal: Take your application from your local Docker environment to a live server on the internet.*

1.  **Sign Up for Accounts:**
    * Create an account on [Laravel Forge](https://forge.laravel.com/).
    * Create an account on a cloud provider like [DigitalOcean](https://www.digitalocean.com/).

2.  **Link Your Cloud Provider:** In the Forge dashboard, link your DigitalOcean account by providing an API token.

3.  **Provision a New Server:**
    * In Forge, click "Create Server" and choose your provider (DigitalOcean).
    * Select your desired server size (the smallest is fine to start) and region.
    * Ensure "PostgreSQL" is checked as the database type.
    * Forge will now create the server and install all the necessary software. This takes about 10-15 minutes.

4.  **Configure Your Site:**
    * Once the server is ready, go to the server's "Sites" tab in Forge.
    * Enter your domain name (or a test domain provided by Forge).
    * Connect your GitHub/GitLab repository and the branch you want to deploy (e.g., `main`).
    * Click "Add Site."

5.  **Set Environment Variables:**
    * Go to your new site's management page in Forge.
    * Click on the "Environment" tab.
    * **Crucially, copy your `GEMINI_API_KEY` from your local `.env` file to this panel.**
    * Forge will automatically set your database credentials.

6.  **Deploy!**
    * Go to the "Deployments" tab for your site.
    * Review the "Deploy Script." It should already contain commands like `composer install` and `npm run build`.
    * Click the **"Deploy Now"** button. Forge will pull your code, run the script, and your site will be live!
    * Enable the "Quick Deploy" option so your site automatically redeploys every time you push a new commit to your `main` branch.

* **Phase 5 Learning Materials:**
    * [Laravel Forge Documentation](https://forge.laravel.com/docs/1.0/getting-started.html)
    * [DigitalOcean Getting Started](https://docs.digitalocean.com/getting-started/)