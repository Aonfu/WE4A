const Stat = require('../models/Stat');

const collectVisit = async (req, res, next) => {
  if (req.url === '/') {
    const today = new Date().toISOString().split('T')[0]; 
    const ip = req.ip;
    
    // Vérifier si cette IP a déjà visité aujourd'hui
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
  }
  next();
};

module.exports = { collectVisit };