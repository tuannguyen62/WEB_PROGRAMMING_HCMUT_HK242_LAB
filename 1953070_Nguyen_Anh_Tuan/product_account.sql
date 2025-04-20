-- Tạo bảng categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(255) NOT NULL
);

-- Tạo bảng products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(255) NOT NULL, 
    description TEXT, 
    price DECIMAL(10, 2) NOT NULL, 
    category_id INT, 
    image VARCHAR(255), 
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Thêm các thể loại sản phẩm
INSERT INTO categories (name) VALUES 
('Vinyl'), 
('Merch');

-- Thêm 40 sản phẩm bao gồm Vinyl và Merch
-- Sản phẩm Vinyl
INSERT INTO products (name, description, price, category_id, image) VALUES 
('Taylor Swift - Folklore', 'Limited edition', 35.00, 1, 'ts_folklore_vinyl.jpg'),
('Taylor Swift - Evermore', 'Vinyl record', 36.50, 1, 'ts_evermore_vinyl.jpg'),
('Lady Gaga - Chromatica', 'Vinyl record', 40.00, 1, 'gaga_chromatica_vinyl.jpg'),
('Ariana Grande - Sweetener', 'Vinyl record', 30.00, 1, 'ariana_sweetener_vinyl.jpg'),
('Ariana Grande - Thank U, Next', 'Thank U, Next vinyl album by Ariana Grande', 32.50, 1, 'ariana_thankunext_vinyl.jpg'),
('Lana Del Rey - Norman Fucking Rockwell', 'Norman Fucking Rockwell vinyl album', 35.99, 1, 'lana_norman_rockwell_vinyl.jpg'),
('Lana Del Rey - Born To Die', 'Vinyl album of Born to Die', 28.00, 1, 'lana_born_to_die_vinyl.jpg'),
('The Weeknd - After Hours', 'Vinyl record of After Hours album', 38.00, 1, 'weeknd_after_hours_vinyl.jpg'),
('The Weeknd - Starboy', 'Vinyl record of Starboy album', 36.50, 1, 'weeknd_starboy_vinyl.jpg'),
('Blackpink - The Album', 'Vinyl record', 40.00, 1, 'blackpink_the_album_vinyl.jpg'),
('Katy Perry - Teenage Dream', 'Vinyl album', 28.00, 1, 'katy_teenage_dream_vinyl.jpg'),
('Billie Eilish - Happier Than Ever', 'Vinyl of Happier Than Ever album', 36.00, 1, 'billie_happier_than_ever_vinyl.jpg'),
('Ed Sheeran - Divide', 'Deluxe version', 32.00, 1, 'edsheeran_divide_vinyl.jpg'),
('Dua Lipa - Future Nostalgia', 'Vinyl album', 37.00, 1, 'dualipa_future_nostalgia_vinyl.jpg'),
('Billie Eilish - When We All Fall Asleep', 'Vinyl album', 33.00, 1, 'billie_when_we_all_fall_asleep_vinyl.jpg'),
('Drake - Scorpion', 'Latest Vinyl album', 35.00, 1, 'drake_scorpion_vinyl.jpg'),
('Harry Styles - Fine Line', 'Deluxe version', 37.00, 1, 'harry_styles_fine_line_vinyl.jpg'),
('Kendrick Lamar - DAMN', 'Vinyl album', 32.00, 1, 'kendrick_lamar_damn_vinyl.jpg'),
('Taylor Swift - Lover T-shirt', 'Cotton T-shirt with Lover album artwork', 22.50, 2, 'ts_lover_tshirt.jpg'),
('Lady Gaga - Chromatica Poster', 'Poster featuring artwork from Lady Gaga', 15.00, 2, 'gaga_chromatica_poster.jpg'),
('Ariana Grande - Thank U, Next Hoodie', 'Hoodie featuring artwork from Thank U, Next album', 45.00, 2, 'ariana_thankunext_hoodie.jpg'),
('Lana Del Rey - Born To Die T-shirt', 'T-shirt with cover art from Born To Die album', 20.00, 2, 'lana_born_to_die_tshirt.jpg'),
('The Weeknd - Blinding Lights Poster', 'Poster featuring The Weeknd single artwork', 18.00, 2, 'weeknd_blinding_lights_poster.jpg'),
('Blackpink - Lightstick', 'Official Blackpink lightstick', 60.00, 2, 'blackpink_lightstick.jpg'),
('Katy Perry - Prism T-shirt', 'T-shirt with Prism album artwork by Katy Perry', 22.00, 2, 'katy_prism_tshirt.jpg'),
('Billie Eilish - All The Good Girls Go To Hell Hoodie', 'Hoodie featuring artwork from Billie Eilish', 48.00, 2, 'billie_good_girls_hoodie.jpg'),
('Ed Sheeran - Shape of You T-shirt', 'T-shirt with Shape of You cover art', 21.50, 2, 'edsheeran_shape_of_you_tshirt.jpg'),
('Dua Lipa - Levitating Poster', 'Poster featuring artwork of Levitating song by Dua Lipa', 16.00, 2, 'dualipa_levitating_poster.jpg'),
('Billie Eilish - Music Poster', 'Poster featuring Billie Eilish album cover', 17.00, 2, 'billie_music_poster.jpg'),
('Ariana Grande - Sweetener T-shirt', 'T-shirt featuring Ariana Grande Sweetener album art', 23.00, 2, 'ariana_sweetener_tshirt.jpg'),
('Lana Del Rey - Honeymoon Poster', 'Poster featuring Lana Del Rey Honeymoon album art', 18.00, 2, 'lana_honeymoon_poster.jpg'),
('The Weeknd - Starboy T-shirt', 'Stylish T-shirt with The Weeknd Starboy artwork', 25.00, 2, 'weeknd_starboy_tshirt.jpg'),
('Blackpink - Blackpink Hoodie', 'Blackpink hoodie with logo', 49.00, 2, 'blackpink_hoodie.jpg'),
('Katy Perry - Firework Mug', 'Ceramic mug with Firework album artwork', 12.00, 2, 'katy_firework_mug.jpg'),
('Ed Sheeran - Perfect Poster', 'Poster featuring Ed Sheeran Perfect album art', 14.00, 2, 'edsheeran_perfect_poster.jpg'),
('Drake - Views T-shirt', 'T-shirt featuring Drake Views album art', 24.00, 2, 'drake_views_tshirt.jpg'),
('Kendrick Lamar - To Pimp a Butterfly Hoodie', 'Hoodie with album cover art from To Pimp a Butterfly', 55.00, 2, 'kendrick_lamar_hoodie.jpg'),
('Lizzo - Juice T-shirt', 'T-shirt featuring Lizzo Juice single artwork', 19.99, 2, 'lizzo_juice_tshirt.jpg');

-- Tạo bảng accounts
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'guest') NOT NULL DEFAULT 'user'
);

-- Thêm admin
INSERT INTO accounts (fullname, email, password, role) 
VALUES ('Admin User', 'admin@sample.com', '$2y$10$6st6tbmkzFiDiqrM/Yc4teQXenD/hY2AE6D9IVaaCgFarTBswihbq', 'admin');

-- Thêm user sample
INSERT INTO accounts (fullname, email, password, role) 
VALUES ('User Sample', 'user@sample.com', '$2y$10$O1zBYEp3m5Nbjd19h90TcuiblNOrUNobnXY2gjz1AjefBOnoBs5Hu', 'user');

-- Tạo bảng stores
CREATE TABLE stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    latitude DECIMAL(9, 6) NOT NULL,
    longitude DECIMAL(9, 6) NOT NULL
);

-- Thêm dữ liệu mẫu cho các cửa hàng
INSERT INTO stores (name, latitude, longitude) VALUES
-- dia chi truong co so 1
('Times Records Store 1', 10.772212, 106.658423),
-- dia chi nha
('Times Records Store 2', 10.849968, 106.591389), 
-- dia chi truong co so 2
('Times Records Store 3', 10.880327, 106.805386);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES accounts(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_cart (user_id, product_id)
);