const express = require('express');
const router = express.Router();
const Log = require('../models/Log');


// Route pour créer un log
router.post('/create', async (req, res) => {
  const log = new Log({
    userId: req.body.userId,
    action: req.body.action,
    ipAddress: req.ip,
    userAgent: req.get('user-agent')
  });
  await log.save();
  res.json({ message: 'Log créé' });
});

// Récupérer tous les logs
router.get('/', async (req, res) => {
  const logs = await Log.find().sort({ timestamp: -1 });
  res.json(logs);
});

module.exports = router;