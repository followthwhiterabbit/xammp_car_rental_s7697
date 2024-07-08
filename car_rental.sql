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

ALTER TABLE cars ADD COLUMN image_url VARCHAR(255);


UPDATE cars SET image_url = 'img/toyota_yarris.jpg' WHERE model = 'Toyota Yarris';
UPDATE cars SET image_url = 'img/honda_accord.jpg' WHERE model = 'Honda Accord';
UPDATE cars SET image_url = 'img/ford_mustang.jpeg' WHERE model = 'Ford Mustang';

CREATE TABLE rentals(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    car_id  INT NOT NULL, 
    rental_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    return_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (car_id)  REFERENCES cars(id)
);




