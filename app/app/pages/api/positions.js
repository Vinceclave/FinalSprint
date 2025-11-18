import { pool } from "../lib/db";

export default async function handler(req, res) {
  try {
    if (req.method === 'GET') {
      const [rows] = await pool.query('SELECT * FROM positions');
      res.status(200).json(rows);
    } else if (req.method === 'POST') {
      const { posName, numOfPositions } = req.body;
      const [result] = await pool.query(
        'INSERT INTO positions (posName, numOfPositions) VALUES (?, ?)',
        [posName, numOfPositions]
      );
      res.status(201).json({ id: result.insertId, posName, numOfPositions });
    } else if (req.method === 'PUT') {
      const { id, posName, numOfPositions } = req.body;
      await pool.query(
        'UPDATE positions SET posName = ?, numOfPositions = ? WHERE posID = ?',
        [posName, numOfPositions, id]
      );
      res.status(200).json({ id, posName, numOfPositions });
    } else if (req.method === 'DELETE') {
      const { id } = req.body;
      await pool.query('DELETE FROM positions WHERE posID = ?', [id]);
      res.status(200).json({ id });
    } else {
      res.setHeader('Allow', ['GET','POST','PUT','DELETE']);
      res.status(405).end(`Method ${req.method} Not Allowed`);
    }
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Database error' });
  }
}
