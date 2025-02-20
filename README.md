## Prompt Writing App - Work in progress

### TODOs

- implement random prompt "homepage" to create a quick note, based on random prompt, Maybe fixed: since there is a random prompt select command
- fix color-selection in easyadmin, in adjusting tags
- implement validity checks in every service-class

## Features & Functions

# Prompt-Writing Application

The Prompt-Writing Application-API is a versatile Symfony/PHP-based tool designed for capturing and organizing thoughts, reflections,
and inspirations. By offering a range of predefined prompts, the application encourages creative and insightful responses,
which users can save, tag, categorize, and organize for future reference. There is an easyadmin-dashboard for managing
database-entries easily. Furthermore, there are several API-routes to send requests to.

### Key Features

- Answer predefined prompts in the console and let the program automatically create a note with your response.
- Prompts for Inspiration: Browse a collection of predefined prompts to spark ideas or reflections.
- Each prompt offers a starting point for writing, making it easy to capture insights on various topics.
- Note Creation and Organization: After responding to a prompt, save your response as a note. Notes can be tagged, categorized, and organized into custom folders, allowing you to structure your thoughts intuitively.
- Customizable Categories: Both notes and prompts can be assigned to specific categories, such as “Journal,”, “Quotes,” or any other relevant label.
- Tagging System: Add tags to notes for easy retrieval and organization. Tags allow for flexible categorization, making it simple to locate notes by theme or subject.

### Use Cases

- Personal Journaling: Use the prompts as a daily journaling tool, capturing your thoughts, emotions, or insights.
- Work on the command-line with the RandomPromptCommand to get a randomized, preselected prompt for you to answer and save.
- Quote Compilation: Collect quotes from various sources and organize them within a dedicated category, making it easy to reference and draw inspiration.

This application provides a powerful and flexible way to record, categorize, and revisit notes, making it a valuable tool for journaling, organizing quotes, and tracking personal reflections.

### Prerequisites

- Docker
- own .env-file for project (standard, when generating a new symfony project, with db-string (preferably: mariadb-10.6.0))

### 1. Installation and setup

#### 1.1 Clone the repository:

```git clone https://gitlab.com/pascalrie/prompt-writing-project.git```

#### 1.2 Navigate to the project repository:

```cd prompt-writing-project```

#### First: Make sure the docker application is started!
#### Second: Update the database-credentials in docker-compose.yml (!)

start the docker-container to run more commands in it:
```docker-compose up -d```

#### create/copy (old) .env - file into the project-root directory:
```cp path/to/own/.env path/to/prompt-writing-project/```
**"This application was tested on mariadb-10.6.0"**

#### install composer-dependencies:
```docker-compose exec app composer install```
### 2. Generate database

#### 2.1 Execute command to create database in ddev-docker-container
```docker-compose exec app php bin/console doctrine:database:create```

#### 2.2 Update the schema of the database (to use the structure of the entities)

```docker-compose exec app php bin/console doctrine:schema:update --force```

### 3. Usage:

#### 3.1 Possible Routes of API

##### 3.1.1 Show via Command
```docker-compose exec app php bin/console debug:router```
##### 3.1.2 Alternatively listed here:
- possible routes in a table:

| 	identifier         | method	 | route	                 |
|---------------------|---------|------------------------|
| api_create_category | POST    | /category/create       |
| api_list_categories | GET     | /category/list         |
| api_show_category   | GET     | /category/show/{id}    |
| api_update_category | PUT	    | /category/update/{id}  |
| api_delete_category | DELETE	 | 	/category/delete/{id} |
|                     |         |                        |
|                     |         |                        |
| api_create_folder   | POST	   | 	/folder/create        |
| api_list_folders	   | GET	    | 	/folder/list          |
| api_show_folder	    | GET     | 	/folder/show/{id}     |
| api_update_folder   | 	PUT    | 	/folder/update/{id}   |
| api_delete_folder	  | DELETE	 | 	/folder/delete/{id}   |
| 	                   | 	       | 	                      |
| 	                   | 	       | 	                      |
| api_create_note	    | POST	   | 	/note/create          |
| api_list_notes	     | GET 	   | 	 /note/list           |
| api_show_note	      | GET	    | 	  /note/show/{id}     |
| api_update_note	    | PUT	    | 	/note/update/{id}     |
| api_delete_note	    | DELETE	 | 	/note/delete/{id}     |
| 	                   | 	       | 	                      |
| 	                   | 	       | 	                      |
| api_create_prompt 	 | POST	   | 	 /prompt/create       |
| api_list_prompts	   | GET	    | 	  /prompt/list        |
| api_show_prompt	    | GET	    | 	  /prompt/show/{id}   |
| api_update_prompt	  | PUT	    | 	  /prompt/update/{id} |
| api_delete_prompt	  | DELETE	 | 	/prompt/delete/{id}   |
| 	                   | 	       | 	                      |
| 	                   | 	       | 	                      |
| api_create_tag	     | POST	   | 	 /tag/create          |
| api_list_tags	      | GET	    | 	 /tag/list            |
| api_show_tag	       | GET	    | 	 /tag/show/{id}       |
| api_update_tag	     | PUT	    | 	 /tag/update/{id}     |
| api_delete_tag	     | DELETE	 | 	  /tag/delete/{id}    |

#### 3.1.3 Access Server in Browser (reference: terminal after ```docker-compose up -d```):
-  http://localhost:8083

#### 3.1.4 To access the easyadmin-dashboard, access the route: /admin
- http://localhost:8083/admin

#### 3.2 Usage of random prompts in terminal
- **Prerequisites**:
  - at least 1 prompt in the database, thus at least 1 category is required for prompt-creation

```docker-compose exec app php bin/console api:random-prompt```
- follow the instructions.
### 4. Execute tests in terminal:
```./vendor/bin/phpunit```