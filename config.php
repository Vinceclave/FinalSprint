<?php 
    $host = "localhost";
    $user = "root";
    $pass = "";
    $database = "sample";

    const sql = "
        CREATE TABLE IF NOT EXISTS positions (
            posID INT AUTO_INCREMENT PRIMARY KEY,
            posName VARCHAR(100) NOT NULL,
            numOfPositions INT NOT NULL,
            posStat ENUM('open, 'closed) DEFAULT 'open',
        );
    
        CREATE TABLE IF NOT EXISTS voters (
            voterID INT AUTO_INCREMENT PRIMARY KEY,
            voterPass VARCHAR(100) NOT NULL,
            voterFName VARCHAR(100) NOT NULL,
            voterMName VARCHAR(100) NOT NULL,
            voterLame VARCHAR(100) NOT NULL,
            voterStat ENUM ('active', 'inactive') DEFAULT 'active'
            voted ENUM('y', 'n') DEFAULT 'n'
        );

        CREATE TABLE IF NOT EXISTS candidates (
            candID INT AUTO_INCREMENT PRIMARY KEY,
            candFName VARCHAR(100) NOT NULL,        
            candMName VARCHAR(100) NOT NULL,        
            candLName VARCHAR(100) NOT NULL,        
            posID INT,
            candStat ENUM('active', 'inactive') DEFAULT 'active',

            FOREIGN KEY (posID) REFERENCES positions(posID)
        );
    
        CREATE TABLE IF NOT EXISTS voters (
            posID INT,
            voterID INT, 
            candID INT, `


        );    
    ";



?>