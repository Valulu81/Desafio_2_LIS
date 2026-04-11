CREATE DATABASE udb_academy_sv;
USE udb_academy_sv;

create table users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    company VARCHAR(255),
    telephone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user'
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) NOT NULL
);

CREATE TABLE quotes (
    id INT AUTO_INCREMENT PRIMARY KEY, -- pa tablas
    code VARCHAR(20) NOT NULL UNIQUE, -- pa cotizaciones
    client_id int NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid_until TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 7 DAY),
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    total DECIMAL(10,2) NOT NULL
);

-- aqui se guardan los servicios que se quieren para cada cotizacion
CREATE TABLE quote_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NOT NULL,
    service_id INT NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL, -- este lo dejo porque creo que al borrar servicios podria afectar el precio de las cotizaciones tons mejor guardarlo aqui
    FOREIGN KEY (quote_id) REFERENCES quotes(id)   ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

-- agregando servicios 12 minimo
-- te recomiendo ponerlos todos e irlos borrando para luego agregarlos y no buscar otros extras que aqui andan
INSERT INTO services (title, category, price, image_url) VALUES
('Desarrollo de página web corporativa','Servicios Tecnológicos', 800.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775883765/diseno-web-corporativa-medida_khpqk8.webp'),
('Mantenimiento de servidores', 'Servicios Tecnológicos', 300.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775884544/mantenimiento-de-servidores_evfmrz.jpg'),
('Configuración de red empresarial', 'Servicios Tecnológicos', 250.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775884595/mejorar-una-red-wifi-empresarial-1-1200x675_oilx3e.webp'),
('Implementación de sistema de respaldo', 'Servicios Tecnológicos', 400.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775884950/images_hpvfyw.jpg'),
('Diseño de logotipo profesional', 'Marketing y Diseño', 150.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775884986/download_lb6hya.jpg'),
('Campaña de publicidad digital', 'Marketing y Diseño', 500.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885006/marketing-digital-e1620825354326_zuvlxo.jpg'),
('Gestión de redes sociales', 'Marketing y Diseño', 350.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885045/plataformas-gestion-redes-sociales_uakxjc.webp'),
('Producción de video promocional', 'Marketing y Diseño', 700.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885220/2112524733-e78e2d261df569a79bbc4b80a7f37bc88708fcf1817cd5da6c1198fcd4b594df-d_zmersh.webp'),
('Reparación de equipos de cómputo', 'Servicios Generales', 120.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885242/tecnicos-en-mantenimiento-de-computadores-ID-01_zvyovt.webp'),
('Instalación de cámaras de seguridad', 'Servicios Generales', 450.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885321/Sabes-como-instalar-camaras-de-circuito-cerrado1_fugljz.jpg'),
('Servicio de limpieza empresarial', 'Servicios Generales', 200.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885354/shutterstock_678273664_heacax.jpg'),
('Capacitación en ofimática para empleados', 'Servicios Generales', 180.00, 'https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885449/software-engineers-working-on-project-and-programm-bducjhl_redimensionar_vmle2t.jpg');

-- agregando usuario admin 
-- la contra es password1234
insert into users (name, email, company, telephone, password, role) values
('Admin User', 'admin@academy_sv.com', 'Academy SV', '12345678', '$2y$10$k33l94PcyDEVg7s4/xYjz.aFv9qOhuGmCSHaXYk72MNEZFcWhppsy', 'admin');