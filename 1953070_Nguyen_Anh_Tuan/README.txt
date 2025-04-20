Project: Music Store Website
Author: Nguyen Anh Tuan
Student ID: 1953070

Deployment Instructions: please use MAMP
1. Unzip the `1953070.zip` into the `domain/file_web_cua_ban` directory.
2. Create a MySQL database name: `music_store`.
3. Import the `product_account.sql` file into the database.
4. Update the `db_connect.php` file with the correct database information:
   - $host: localhost
   - $dbname: music_store
   - $username: root
   - $password: root
5. Ensure the `images/product_img/` directory has read permissions (755 or 644).
6. Access the website via `domain/file_web_cua_ban/index.php`.

Test Accounts:
- Admin: admin@sample.com / Admin123@
- User: user@sample.com / User123@

Main Features:
- User login and registration.
- Product search, detailed view, and adding to cart.
- Cart management (update quantity, remove items).
- Admin product management (add, edit, delete).
Note: if you want to add product image, please make sure yoou place the image in directory images/product_img.
