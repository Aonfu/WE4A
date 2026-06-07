const mongoose = require('mongoose');

const statSchema = new mongoose.Schema({
  metric: String,
  details: Object,  
  date: { type: Date, default: Date.now }
});

module.exports = mongoose.model('Stat', statSchema);