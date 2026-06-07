const express = require('express');
const mongoose = require('mongoose');
const logsRouter = require('./routes/logs');
const filesRouter = require('./routes/files');
const statsRouter = require('./routes/stats');
const { collectVisit } = require('./middleware/collectStats');
require('dotenv').config();

const app = express();
app.use(express.json());
app.use(collectVisit);

const MONGODB_URI = process.env.MONGODB_URI;

// Connexion MongoDB
mongoose.connect(MONGODB_URI)
  .then(() => console.log('Connecté à MongoDB Atlas'))
  .catch((err) => console.error('Erreur de connexion:', err));

// Route de test
app.get('/', (req, res) => {
  res.json({ message: 'Backend marche' });
});

//pour enregistrer les logs
app.use('/api/logs', logsRouter);
//pour enregistrer les fichiers
app.use('/api/files', filesRouter);
//pour les stats
app.use('/api/stats', statsRouter);

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Serveur lancé sur le port ${PORT}`);
});
