const mongoose = require('mongoose');

const fileSchema = new mongoose.Schema({
  filename: String,        // nom stocké sur serveur
  originalName: String,    // nom original du fichier
  mimeType: String,        // png ou jpeg ou ...
  size: Number,            // taille en bytes
  userId: String,          // qui a uploadé
  productId: String,       // produit associé
  uploadDate: { type: Date, default: Date.now }
});

module.exports = mongoose.model('File', fileSchema);