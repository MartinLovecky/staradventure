CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    img_src VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    author_img VARCHAR(255) NOT NULL,
    author_link VARCHAR(255) NOT NULL,
    author_name VARCHAR(255) NOT NULL
);