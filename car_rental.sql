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
    status ENUM('available', 'rented') DEFAULT 'available', 
    manufacturer VARCHAR(255),
    brand VARCHAR(255),
    registration_plate VARCHAR(50),
    type VARCHAR(50),
    fuel_type VARCHAR(50),
    transmission VARCHAR(50),
    mileage INT,
    free_text TEXT,
    additional_info TEXT
);

ALTER TABLE cars MODIFY status VARCHAR(10);

INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Toyota Yarris', 'available', 'Toyota', "Yarris", '34CCC666', 'sedan', 'gasoline', 'automatic', 53400, 'doktordan temiz.', 'abcdef' ); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Honda Accord', 'available', 'Honda', "Accord", '34CCC111', 'sedan', 'diesel', 'manual', 72000, 'incredible condition', 'abcedfg'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Ford Mustang', 'available', 'Ford', "Mustang", '34CCC222', 'coupe', 'gasoline', 'automatic', 34000, 'good condition.', 'abc'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('BYD Atto 3', 'available', 'BYD', "Atto 3", '34CCC333', 'sedan', 'diesel', 'automatic', 50000, 'very good one.', 'abcd'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Cherry Omoda 5', 'available', 'Cherry', "Omoda 5", '34CCC444', 'sedan', 'gasoline', 'automatic', 20000, 'good condition', 'abcedfgh'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Fiat 500', 'available', 'Fiat', "500", '34CCC555', 'sedan', 'gasoline', 'automatic', 30000, 'very good condition', 'aabbccddee'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Tesla Model S', 'available', 'Tesla', "Model S", '31XXX62', 'sedan', 'electric', 'automatic', 23400, 'very good condition.', 'aaabbb'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Tofas Kartal', 'available', 'Tofas', "Kartal", '62xxx31', 'sedan', 'LPG', 'manual', 253400, 'well, still works :)', 'aaabbbcccdddee'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Togg T10X', 'available', 'TOGG', "T10X", '6262XXX31', 'sedan', 'electric', 'automatic', 2000, 'new car.', 'aaaaabbb'); 
INSERT INTO cars (model, status, manufacturer, brand, registration_plate, type, fuel_type, transmission, mileage, free_text, additional_info)  VALUES ('Fiat Egea', 'available', 'Fiat', "Egea", 'XXXYYYZZZ', 'sedan', 'diesel', 'automatic',153400, 'doktordan temiz.', 'abbbbaa'); 






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




