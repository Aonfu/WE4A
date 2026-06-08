const express = require('express');
const router = express.Router();
const Stat = require('../models/Stat');

// Compteur de visites
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

// Créer une stat (produit, enchere)
router.post('/create', async (req, res) => {
  const stat = new Stat({
    metric: req.body.metric,
    details: { date: new Date().toISOString().split('T')[0] }
  });
  await stat.save();
  res.json({ message: `${req.body.metric} ajouté` });
});

// DASHBOARD - 6 cartes
router.get('/dashboard', async (req, res) => {
  const today = new Date().toISOString().split('T')[0];

  const totalVisits = await Stat.aggregate([
    { $match: { metric: 'visit' } },
    { $group: { _id: { ip: '$details.ip', date: '$details.date' } } },
    { $count: 'total' }
  ]);

  const todayVisits = await Stat.aggregate([
    { $match: { metric: 'visit', 'details.date': today } },
    { $group: { _id: '$details.ip' } },
    { $count: 'total' }
  ]);

  res.json({
    totalVisits: totalVisits[0]?.total || 0,
    todayVisits: todayVisits[0]?.total || 0,
    totalProducts: await Stat.countDocuments({ metric: 'product' }),
    todayProducts: await Stat.countDocuments({ metric: 'product', 'details.date': today }),
    totalEncheres: await Stat.countDocuments({ metric: 'enchere' }),
    todayEncheres: await Stat.countDocuments({ metric: 'enchere', 'details.date': today })
  });
});

// GRAPHIQUES
router.get('/charts', async (req, res) => {
  const visitsByDay = await Stat.aggregate([
    { $match: { metric: 'visit' } },
    { $group: { _id: { ip: '$details.ip', date: '$details.date' } } },
    { $group: { _id: '$_id.date', count: { $sum: 1 } } },
    { $sort: { _id: 1 } }
  ]);

  const productsByDay = await Stat.aggregate([
    { $match: { metric: 'product' } },
    { $group: { _id: '$details.date', count: { $sum: 1 } } },
    { $sort: { _id: 1 } }
  ]);

  const encheresByDay = await Stat.aggregate([
    { $match: { metric: 'enchere' } },
    { $group: { _id: '$details.date', count: { $sum: 1 } } },
    { $sort: { _id: 1 } }
  ]);

  res.json({
    visits: visitsByDay.map(item => ({ date: item._id, value: item.count })),
    products: productsByDay.map(item => ({ date: item._id, value: item.count })),
    encheres: encheresByDay.map(item => ({ date: item._id, value: item.count }))
  });
});

// DASHBOARD HTML (version visuelle dans navigateur)
router.get('/html', async (req, res) => {
  const today = new Date().toISOString().split('T')[0];

  const totalVisits = await Stat.aggregate([
    { $match: { metric: 'visit' } },
    { $group: { _id: { ip: '$details.ip', date: '$details.date' } } },
    { $count: 'total' }
  ]);

  const visitsByDay = await Stat.aggregate([
    { $match: { metric: 'visit' } },
    { $group: { _id: { ip: '$details.ip', date: '$details.date' } } },
    { $group: { _id: '$_id.date', count: { $sum: 1 } } },
    { $sort: { _id: 1 } }
  ]);

  const productsByDay = await Stat.aggregate([
    { $match: { metric: 'product' } },
    { $group: { _id: '$details.date', count: { $sum: 1 } } },
    { $sort: { _id: 1 } }
  ]);

  const encheresByDay = await Stat.aggregate([
    { $match: { metric: 'enchere' } },
    { $group: { _id: '$details.date', count: { $sum: 1 } } },
    { $sort: { _id: 1 } }
  ]);

  const totalProducts = await Stat.countDocuments({ metric: 'product' });
  const todayProducts = await Stat.countDocuments({ metric: 'product', 'details.date': today });
  const totalEncheres = await Stat.countDocuments({ metric: 'enchere' });
  const todayEncheres = await Stat.countDocuments({ metric: 'enchere', 'details.date': today });
  const todayVisits = visitsByDay.find(v => v._id === today)?.count || 0;

  const html = `
  <!DOCTYPE html>
  <html>
  <head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      body { font-family: Arial; padding: 20px; background: #f5f5f5; }
      h1 { text-align: center; color: #333; }
      .cards { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
      .card { background: white; padding: 20px; border-radius: 10px; min-width: 150px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
      .card h3 { margin: 0 0 10px 0; color: #666; }
      .card .value { font-size: 32px; font-weight: bold; color: #333; }
      .charts { display: flex; flex-wrap: wrap; gap: 40px; justify-content: center; margin-top: 40px; }
      .chart-container { width: 400px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
      canvas { max-width: 100%; }
    </style>
  </head>
  <body>
    <h1>Dashboard des ventes aux enchères</h1>
    <div class="cards">
      <div class="card"><h3>Visites totales</h3><div class="value">${totalVisits[0]?.total || 0}</div></div>
      <div class="card"><h3>Visites aujourd'hui</h3><div class="value">${todayVisits}</div></div>
      <div class="card"><h3>Produits totaux</h3><div class="value">${totalProducts}</div></div>
      <div class="card"><h3>Produits aujourd'hui</h3><div class="value">${todayProducts}</div></div>
      <div class="card"><h3>Enchères totales</h3><div class="value">${totalEncheres}</div></div>
      <div class="card"><h3>Enchères aujourd'hui</h3><div class="value">${todayEncheres}</div></div>
    </div>
    <div class="charts">
      <div class="chart-container"><canvas id="visitsChart"></canvas></div>
      <div class="chart-container"><canvas id="productsChart"></canvas></div>
      <div class="chart-container"><canvas id="encheresChart"></canvas></div>
    </div>
    <script>
      const visitsData = ${JSON.stringify(visitsByDay.map((v) => ({ date: v._id, count: v.count })))};
      const productsData = ${JSON.stringify(productsByDay.map((p) => ({ date: p._id, count: p.count })))};
      const encheresData = ${JSON.stringify(encheresByDay.map((e) => ({ date: e._id, count: e.count })))};

      new Chart(document.getElementById('visitsChart'), {
        type: 'line',
        data: { labels: visitsData.map(d => d.date), datasets: [{ label: 'Visites', data: visitsData.map(d => d.count), borderColor: '#3498db', fill: true }] },
        options: { scales: { y: { beginAtZero: true } } }
      });
      new Chart(document.getElementById('productsChart'), {
        type: 'line',
        data: { labels: productsData.map(d => d.date), datasets: [{ label: 'Produits', data: productsData.map(d => d.count), borderColor: '#2ecc71', fill: true }] },
        options: { scales: { y: { beginAtZero: true } } }
      });
      new Chart(document.getElementById('encheresChart'), {
        type: 'line',
        data: { labels: encheresData.map(d => d.date), datasets: [{ label: 'Enchères', data: encheresData.map(d => d.count), borderColor: '#e67e22', fill: true }] },
        options: { scales: { y: { beginAtZero: true } } }
      });
  </script>
  </body>
  </html>
  `;
  res.send(html);
});

module.exports = router;
