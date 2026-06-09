const mongoose = require('mongoose');

const logSchema = new mongoose.Schema({
  userId: String,
  action: String,
  ipAddress: String,
  userAgent: String, //navigateur/OS
  status: { type: String, default: 'success' },
  timestamp: { type: Date, default: Date.now }
});

module.exports = mongoose.model('Log', logSchema);