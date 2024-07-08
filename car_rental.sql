USE car_rental; 

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    username VARCHAR(50) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL
);

INSERT INTO users(username, password) VALUES    ('admin', 'admin'); 
INSERT INTO users(username, password) VALUES    ('user1', 'password1');
INSERT INTO users(username, password) VALUES    ('user2', 'password2');


CREATE TABLE cars(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    model VARCHAR(50) NOT NULL, 
    status ENUM('available', 'rented') DEFAULT 'available'
);

INSERT INTO cars (model, status)  VALUES ('Toyota Yarris', 'available'); 
INSERT INTO cars (model, status)  VALUES ('Honda Accord',  'available');
INSERT INTO cars (model, status)  VALUES ('Ford Mustang', 'available');
INSERT INTO cars (model, status)  VALUES ('BYD Atto 3', 'available');
INSERT INTO cars (model, status)  VALUES ('Cherry Omoda 5', 'available');
INSERT INTO cars (model, status)  VALUES ('Fiat 500', 'available');
INSERT INTO cars (model, status)  VALUES ('Tesla Model S', 'available');
INSERT INTO cars (model, status)  VALUES ('Tofas Kartal', 'available');
INSERT INTO cars (model, status)  VALUES ('Togg T10X', 'available');
INSERT INTO cars (model, status)  VALUES ('Fiat Egea', 'available');





ALTER TABLE cars ADD COLUMN image_url VARCHAR(255);


UPDATE cars SET image_url = 'img/toyota_yarris.jpg' WHERE model = 'Toyota Yarris';
UPDATE cars SET image_url = 'img/honda_accord.jpg' WHERE model = 'Honda Accord';
UPDATE cars SET image_url = 'img/ford_mustang.jpeg' WHERE model = 'Ford Mustang';
UPDATE cars SET image_url = 'img/byd_atto_3.jpg' WHERE model = 'BYD Atto 3';
UPDATE cars SET image_url = 'img/Chery-Omoda-5-cover.jpg' WHERE model = 'Cherry Omoda 5';
UPDATE cars SET image_url = 'img/fiat_500.jpg' WHERE model = 'Fiat 500';
UPDATE cars SET image_url = 'img/tesla_model_s.jpg' WHERE model = 'Tesla Model S';
UPDATE cars SET image_url = 'img/tofas_Kartal.jpg' WHERE model = 'Tofas Kartal';
UPDATE cars SET image_url = 'img/togg_t10x.jpg' WHERE model = 'Togg T10X';
UPDATE cars SET image_url = 'img/fiat_egea.jpg' WHERE model = 'Fiat Egea';

CREATE TABLE rentals(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    car_id  INT NOT NULL, 
    rental_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    return_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (car_id)  REFERENCES cars(id)
);




