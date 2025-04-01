## Requirements
1. Node.js: v18+
2. npm
3. php: v8.1+
4. composer

## Dependencies
Before running the application, the following dependencies have to be set up:

1. Set up npm in the root directory in the terminal and install its dependencies: npm install 

2. Install the Laravel dependencies in the root directory in the terminal: composer install

3. In order to run the front-end Vite server with Node.js dependencies, alongside the backend,run in the terminal: composer run dev
   (if there are issues, to run the Laravel backend, type in the terminal (root directory): php artisan serve)

4. Ctrl + click (or command + click on MacOS) on the port taht server that runs, following the composer run dev output: usually [http://127.0.0.1:8000]

## Application walkthrough

1. There are 3 registered users:

| Email            | Password   |
|------------------|------------|
| lucas@lucas.com  | lucas1234  |
| admin@admin.com  | admin1234  |
| john@john.com    | john1234   |

The admin user has admin privileges and is the only one that can access the filamentPHP page.

The welcome page only prompts users to register or log in. Once they do, they are redirected to their dashboard.

2. The dashboard (that shows up at http://127.0.0.1:8000/dashboard) will show the projects assigned to a user.

3. Tapping on a project will expand it and provide more details: title, description, tasks (containing the details specified in the requirements)

4. Tapping on a specific task will provide a comment section related to that specific task. Any user can write comments related to a task and post them.

5. Hovering above a task's priority will allow the user to modify their priority level, among the possible choices: To Do, In Progress, Under Review, Completed,

6. A user that has the role of an Owner in a project can also create Tasks. This button will appear if a user has the required role. Tapping that button will redirect a user to a form to create a form. By filling in the required data, he can create a new task and assign it to someone part of the same project.

7. The admin user can also log in to the filamentPHP admin page. It can be found at http://127.0.0.1:8000/admin.

8. Once the admin logs in, he can find a few informational widgets on the admin dashboard, as well as 3 other management tables: Projects, Users, Comments

9. The User table only provides the ability to modify the username. The Project table is the biggest admin management table. From there, the admin can add projects, add users to the project and assign them roles and add tasks to projects and assign them to users also part of the project. The tasks contain the attributes specified in the requirements. The Comments table contains the comments, also sorted by the tasks that they are part of, and in turn, the projects those tasks are part of, including the author. All attributes in these tables are sortable and searchable. The admin can modify almost all attributes contained in the database (exclusing user information such as passwords and timestamp()) and create the elements contained in those tables 

## Project Structure
The project is based on the Laravel-Vue starter kit, including Filament as an admin page framework. Relevant file tempaltes have been generated via php artisan commands.

1. The developed Vue frontend and its components are in resources/js/pages.
2. The admin resources and the Filament framework is located at app/Filament/Resources. The filament dashboard is at app/Providers/Filament.
3. The database resources are located in the database folder, with necessary files in the factories, migrations, seeders in their own folders. Inside database also resides the SQLite database in database.sqlite and other .sql files used in the database population.
4. The RESTful API is developed inside web.php is located inside the routes folder. The relevant controllers are found in app/Http/Controllers.


## Notes and explanations

1. Sometimes, if a non-admin user has been logged in before, then the user tries to connect to the admin page directly from the dashboard (by typing http://127.0.0.1:8000/admin) with the normal user logged in, it will be forbidden. The solution is to log out of the dashboard with the normal user account, then try to access the admin page. That way, the last user cache is emptied and the admin can access the admin page. The admin can also access the page directly if he is logged in to the normal user dashboard (at http://127.0.0.1:8000/dashboard) and then he switches directly to the admin page, without logging in.

2. The design of the app has been done while running the system in Dark mode, so the coloring might be conflicting in Light mode. Recommended to run in Dark mode settings.

3. (FIXED 1 Apr. 16:00: A user can now add a task via the dashboard UI!)

Adding a task via the dashboard UI (for Owners of a project) is not functional. A bug that does not allow the rest of the users part of the project to be passed on to the CreateTaskForm without issues makes the process impossible (as a Task HAS to have a user).

4. A project and tasks can ONLY be added via the admin page. Comments can be added by anyone, as well as modifying the task's priority.

5. Live notifications and attachments to comments could not be developed due to lack of time. The database supports attachments, however.

I apologise for all these lacking features. I hope that the structure, way of working, and implemented features show my qualities, potential to learn fast and capability to integrate in a team with different roles. Enjoy the app!

