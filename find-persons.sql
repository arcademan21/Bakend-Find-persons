-- Create a table called Users.
CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_name VARCHAR( 100 ) NOT NULL,
    user_email VARCHAR( 100 ) NOT NULL,
    password VARCHAR( 100 ) NOT NULL,
    status VARCHAR( 100 ) NOT NULL,
    role VARCHAR( 100 ) DEFAULT 'suscriber',
    created_at DATETIME DEFAULT NULL,
    searchs LONGTEXT DEFAULT NULL,
    downloads LONGTEXT DEFAULT NULL,
    ip VARCHAR( 100 ) DEFAULT NULL
);

-- Create a table called Suscriptions.
CREATE TABLE Suscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME DEFAULT NULL,
    user_id INT NOT NULL,
    user_name VARCHAR( 100 ) DEFAULT NULL,
    user_email VARCHAR( 100 ) NOT NULL,
    status VARCHAR( 100 ) DEFAULT 'trial',
    ds_date VARCHAR( 100 ) DEFAULT NULL,
    ds_expiry VARCHAR( 100 ) DEFAULT NULL,
    ds_amount VARCHAR( 100 ) DEFAULT NULL,
    ds_merchant_suscription_start_date VARCHAR( 100 ) DEFAULT NULL,
    ds_merchant_matching_data VARCHAR( 100 ) DEFAULT NULL,
    end_date DATETIME DEFAULT NULL,
    canceled_date DATETIME DEFAULT NULL,
    reason VARCHAR( 100 ) DEFAULT NULL
);

-- Create a table called RightToBeforgotten.
CREATE TABLE RightToBeforgotten (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_name VARCHAR( 100 ) DEFAULT NULL,
    user_email VARCHAR( 100 ) NOT NULL,
    created_at DATETIME NOT NULL,
    status BOOLEAN NOT NULL,
    forgotten_name VARCHAR( 100 ) DEFAULT NULL,
    forgotten_reason LONGTEXT DEFAULT NULL
);

-- Create a table called EmailsLogs.
CREATE TABLE EmailsLogs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_name VARCHAR(100) DEFAULT NULL,
    `to` VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    status VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    email_type VARCHAR(100) NOT NULL,
    message LONGTEXT NOT NULL,
    ip VARCHAR(100) DEFAULT NULL
);