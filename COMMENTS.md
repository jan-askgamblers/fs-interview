I do not have php installed on my system, I always use dockerized php in my projects. This way I can avoid conflicts
between different projects and I can easily switch between different php versions. I started with adding a php 8.1 image
to the project.

Seems like the project requires php 8.2 instead of the 8.1 mentioned in the README.md file.
This is composer's output when trying to install the dependencies with php 8.1:
```
Your lock file does not contain a compatible set of packages. Please run composer update.

  Problem 1
    - symfony/css-selector is locked to version v7.0.3 and an update of this package was not requested.
    - symfony/css-selector v7.0.3 requires php >=8.2 -> your php version (8.1.28) does not satisfy that requirement.
  Problem 2
    - symfony/event-dispatcher is locked to version v7.0.3 and an update of this package was not requested.
    - symfony/event-dispatcher v7.0.3 requires php >=8.2 -> your php version (8.1.28) does not satisfy that requirement.
  Problem 3
    - symfony/string is locked to version v7.0.4 and an update of this package was not requested.
    - symfony/string v7.0.4 requires php >=8.2 -> your php version (8.1.28) does not satisfy that requirement.
  Problem 4
    - symfony/yaml is locked to version v7.0.3 and an update of this package was not requested.
    - symfony/yaml v7.0.3 requires php >=8.2 -> your php version (8.1.28) does not satisfy that requirement.
  Problem 5
    - symfony/string v7.0.4 requires php >=8.2 -> your php version (8.1.28) does not satisfy that requirement.
    - symfony/console v6.4.6 requires symfony/string ^5.4|^6.0|^7.0 -> satisfiable by symfony/string[v7.0.4].
    - symfony/console is locked to version v6.4.6 and an update of this package was not requested.
```
Well, I didn't want to downgrade the symfony packages (not looking forward for unforseeable consequences), so I switched
to php 8.2 and it secceeded. Immidiate return on my "not having specific php version installed" investment :).

--- 
```shell
vendor/bin/sail up -d
```
```
Error response from daemon: failed to create task for container: failed to create shim task: OCI runtime create failed:
runc create failed: unable to start container process: error during container init: error mounting 
"/home/hattila/projects/hattila/gig-interview/vendor/laravel/sail/database/mysql/create-testing-database.sh" 
to rootfs at "/docker-entrypoint-initdb.d/10-create-testing-database.sh": 
mount /home/hattila/projects/hattila/gig-interview/vendor/laravel/sail/database/mysql/create-testing-database.sh:
/docker-entrypoint-initdb.d/10-create-testing-database.sh (via /proc/self/fd/6),
flags: 0x5000: not a directory: unknown: Are you trying to mount a directory onto a file (or vice-versa)? 
Check if the specified host path exists and is the expected type
```

I removed the volume named `gig-interview_sail-mysql`, then ran sail up again. Seems like everything is working now.

---

```shell
vendor/bin/sail artisan migrate
```
Indeed, there is an error:

```SQLSTATE[HY000] [2002] Connection refused```

Unable to connect to the database with an IDE like DataGrip as well. I've added the `FORWARD_DB_PORT` .env entry with a
modified port, different from the default 3306, now at lease I can connect with my IDE, and I see the empty DB.

The interesting thing is, that sail can open a connection with compose, but not with artisan. 
So this
```shell
vendor/bin/sail mysql
```
gives me a live promt, but `vendor/bin/sail artisan migrate` still gives me the same error.

Sometime later, I found out that I have to remove the existing volumes again, and recreate them with the following command:
```shell
vendor/bin/sail down -v && vendor/bin/sail up -d
```
After a fresh start, migration was successful. Finally!
