Requirements:\
PHP/MySQL and LDAP\
\
Used as seat booking system for office buildings.\
LDAP user can login, select floor, room and seat and book/block it.\
Backend for creating groups, floors, rooms, seats.

Installation:
1. Copy files to your web root\
2. Create mysql tables (/admin/db.txt)\
3. Edit /config/db.php with your database credentials\
4. Edit login.php with your ldap servername and domainname (tested with AD)\
5. Add a new admin user to db
6. Login and access the backend
7. Create groups, floors, rooms

Last step:\
Until we are not done with replacing db credentials from files to /config/db.php you have to search all files for "$pdo = new PDO" and replace db credentials.\
Hopefully we are done in some weeks with it.
