## Prompt Writing App - Work in progress

### TODOs
- remove ddev configuration to use docker-compose
- implement test db
- ext-http in composer.json => find solution

#### TODOs Readme:
- short description
- features & functions of application

### Prerequisites

- Docker
- ddev (https://ddev.com)

### 1. Installation and setup

#### 1.1 Clone the repository:

```git clone https://gitlab.com/pascalrie/prompt-writing-project.git```

#### 1.2 Navigate to the project repository:

```cd prompt-writing-project```

#### 1.3 Start and build the ddev-docker-container locally
#### First: Make sure the docker application is started!
```ddev config```

- mostly 3 presses of enter will suffice
- if not: adjust the .ddev/config.yaml (for example for the host-db-port)

- to start the ddev-docker-container: \
```ddev start```

#### 1.4 Install dependencies of the application inside ddev-docker-container:

```ddev exec composer install```

#### 1.5 Add Apache-config-file into project-root (.htaccess) (only necessary for docker/apache-config)

### 2. Generate database

#### 2.1 Create/Copy (old) .env - file into the project-directory

```ddev exec php bin/console doctrine:database:create```

#### 2.2 Update the schema of the database

```ddev exec php bin/console doctrine:schema:update --force```

### 3. Usage:

#### 3.1 Possible Routes of API

##### 3.1.1 Show via Command
```ddev exec php bin/console debug:router```
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

#### 3.1.3 Access Server in Browser (reference: terminal after ```ddev start```):
- https://prompt-writing-project.ddev.site 

#### 3.1.4 To access the easyadmin-dashboard, access the route: /admin
- https://prompt-writing-project.ddev.site/admin