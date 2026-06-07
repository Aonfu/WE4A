const express = require('express');
const router = express.Router();
const Stat = require('../models/Stat');

// recupere toutes les stats
router.get('/', async (req, res) => {
  const stats = await Stat.find().sort({ date: -1 });
  res.json(stats);
});

// resume : nombre par type de métrique
router.get('/summary', async (req, res) => {
  const visits = await Stat.countDocuments({ metric: 'visit' });
  const encheres = await Stat.countDocuments({ metric: 'enchere' });
  const products = await Stat.countDocuments({ metric: 'product' });

  res.json({ visits, encheres, products });
});

router.post('/create', async (req, res) => {
  const stat = new Stat({
    metric: req.body.metric,
    details: { date: new Date().toISOString().split('T')[0] }
  });
  await stat.save();
  res.json({ message: `${req.body.metric} ajouté` });
});

//compteur de visites
router.get('/visit', async (req, res) => {
  const today = new Date().toISOString().split('T')[0];
  const ip = req.ip;

  const existing = await Stat.findOne({
    metric: 'visit',
    'details.ip': ip,
    'details.date': today
  });

  if (!existing) {
    const stat = new Stat({
      metric: 'visit',
      details: { ip: ip, date: today }
    });
    await stat.save();
  }
  res.json({ message: 'ok' });
});

router.get('/powerbi', async (req, res) => {
  const visits = await Stat.find({ metric: 'visit' });
  const products = await Stat.find({ metric: 'product' });
  const encheres = await Stat.find({ metric: 'enchere' });

  res.json({
    visits: visits.map(v => ({
      date: v.details?.date || v.date.toISOString().split('T')[0],
      ip: v.details?.ip
    })),
    products: products.map(p => ({ date: p.details?.date })),
    encheres: encheres.map(b => ({ date: b.details?.date }))
  });
});

module.exports = router;
