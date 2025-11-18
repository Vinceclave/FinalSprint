import mysql from 'mysql2';

export const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',          // replace with your MySQL password
  database: 'election',      // replace with your database name
  multipleStatements: true,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});


db.connect((err) => {
  if (err) {
    console.error('Error connecting to the database:', err);
    return;
  }     
    console.log('Connected to the MySQL database.');
});